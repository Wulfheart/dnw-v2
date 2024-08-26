<?php

namespace Dnw\Foundation\Event;

use Dnw\Foundation\Event\Attributes\DomainEvent;
use Dnw\Foundation\Event\Attributes\DomainListener;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Filesystem\Filesystem;

readonly class DomainEventProvider implements DomainEventProviderInterface
{
    public function __construct(
        private string $cachePath,
        private Filesystem $filesystem
    ) {}

    private const string BASE_MODULE_NAMESPACE = 'Dnw\\';

    /**
     * @return array<class-string, array<ListenerInfo>>
     */
    public function getEvents(): array
    {
        if ($this->isCached()) {
            return $this->retrieveCached();
        } else {
            return $this->discoverEvents();
        }
    }

    private function isCached(): bool
    {
        return $this->filesystem->exists($this->cachePath);
    }

    /**
     * @return array<class-string, array<ListenerInfo>>
     */
    private function retrieveCached(): array
    {
        if ($this->isCached()) {
            return require $this->cachePath;
        } else {
            trigger_error('Events are not cached but something tried to access them cached', E_USER_WARNING);

            return [];
        }
    }

    public function cacheEvents(): void
    {
        $events = $this->discoverEvents();

        $this->deleteCachedEvents();
        $fileContent = '<?php return ' . var_export($events, true) . ';';
        $this->filesystem->dumpFile($this->cachePath, $fileContent);
    }

    public function deleteCachedEvents(): void
    {
        $this->filesystem->remove($this->cachePath);
    }

    /**
     * @return array<class-string, array<ListenerInfo>>
     */
    private function discoverEvents(): array
    {
        $classes = ClassFinder::getClassesInNamespace(self::BASE_MODULE_NAMESPACE, ClassFinder::RECURSIVE_MODE);

        /** @var array<class-string> $module_classes */
        $module_classes = array_filter($classes, function ($class) {
            return str_starts_with($class, self::BASE_MODULE_NAMESPACE);
        });

        $event_classes = [];
        $listener_classes = [];

        foreach ($module_classes as $module_class) {
            $reflection = new ReflectionClass($module_class);
            $attributes = $reflection->getAttributes();

            foreach ($attributes as $attribute) {
                if ($attribute->getName() === DomainEvent::class) {
                    $event_classes[] = $module_class;
                }
                if ($attribute->getName() === DomainListener::class) {
                    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                        if (
                            (
                                ! Str::is('handle*', $method->name)
                                && ! Str::is('__invoke', $method->name)
                            )
                            || ! isset($method->getParameters()[0])) {
                            continue;
                        }

                        /** @var array<class-string> $listenerEvents */
                        $listenerEvents = Reflector::getParameterClassNames(
                            $method->getParameters()[0]
                        );

                        /** @var DomainListener $attributeInstance */
                        $attributeInstance = $attribute->newInstance();

                        foreach ($listenerEvents as $listenerEvent) {
                            $listener_classes[$listenerEvent][] = new ListenerInfo(
                                $module_class,
                                $method->name,
                                $this->shouldBeQueued($listenerEvent, $module_class)
                                || $attributeInstance->async,
                            );
                        }

                    }

                }
            }
        }

        return $listener_classes;

    }

    /**
     * @param  class-string  $event
     * @param  class-string  $listener
     */
    private function shouldBeQueued(string $event, string $listener): bool
    {
        if (
            str_starts_with($listener, self::BASE_MODULE_NAMESPACE)
        ) {
            $eventNamespaceParts = collect(explode('\\', Str::replaceStart(self::BASE_MODULE_NAMESPACE, '', $event)));
            $listenerNamespaceParts = collect(explode('\\', Str::replaceStart(self::BASE_MODULE_NAMESPACE, '', $listener)));

            // Removing the class name to only get the namespace part
            $eventNamespaceParts->pop();
            $listenerNamespaceParts->pop();

            return $eventNamespaceParts->first() !== $listenerNamespaceParts->first();
        }

        return false;
    }
}
