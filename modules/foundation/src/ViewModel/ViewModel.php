<?php

namespace Dnw\Foundation\ViewModel;

use Illuminate\Contracts\Support\Arrayable;
use ReflectionClass;
use ReflectionProperty;

/**
 * @implements Arrayable<string, static>
 */
abstract class ViewModel implements Arrayable {

    /**
     * @return array<string, static>
     */
    public function toArray(): array
    {
        return ['view' => $this];
    }

    /**
     * @return array<string, mixed>
     */
    public function toLivewire(): array
    {
        $reflectionClass = new ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties(
            ReflectionProperty::IS_PUBLIC);
        $data = [];

        foreach ($properties as $property) {
            // For now only support public properties as they should all be public anyway
            if($property->isPublic()) {
                $data[$property->getName()] = $property->getValue($this);
            }
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $value
     */
    public static function fromLivewire($value):static
    {
        $reflectionClass = new ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties(
            ReflectionProperty::IS_PUBLIC);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($properties as $property) {
            if($property->isPublic()) {
                $property->setValue($object, $value[$property->getName()]);
            }
        }

        return $object;
    }
}
