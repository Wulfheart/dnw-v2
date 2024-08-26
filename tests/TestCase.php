<?php

namespace Tests;

use App\Models\User;
use Dnw\Foundation\Identity\Id;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PHPUnit\Framework\Assert as PHPUnitAssert;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    protected function randomUser(): User
    {
        return User::factory()->create();
    }

    protected function userWithId(Id $id): User
    {
        return User::factory()->create(['id' => $id]);
    }

    public function assertActionUsesFormRequest(string $controller, string $method, string $form_request): void
    {
        PHPUnitAssert::assertTrue(is_subclass_of($form_request, 'Illuminate\\Foundation\\Http\\FormRequest'), $form_request . ' is not a type of Form Request');

        try {
            // @phpstan-ignore argument.type
            $reflector = new ReflectionClass($controller);
            $action = $reflector->getMethod($method);
        } catch (ReflectionException $exception) {
            PHPUnitAssert::fail('Controller action could not be found: ' . $controller . '@' . $method);
        }

        PHPUnitAssert::assertTrue($action->isPublic(), 'Action "' . $method . '" is not public, controller actions must be public.');

        $actual = collect($action->getParameters())->contains(function ($parameter) use ($form_request) {
            return $parameter->getType() instanceof ReflectionNamedType && $parameter->getType()->getName() === $form_request;
        });

        PHPUnitAssert::assertTrue($actual, 'Action "' . $method . '" does not have validation using the "' . $form_request . '" Form Request.');
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $type
     * @return T
     */
    protected function bootstrap(string $type): mixed
    {
        return $this->app->make($type);
    }
}
