<?php

namespace App\Web\Game\CreateGame;

use App\Foundation\Request\RequestFactory;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use MohammedManssour\FormRequestTester\TestsFormRequests;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\LaravelTestCase;

#[CoversClass(StoreGameRequest::class)]
#[AllowLaravelTestCase]
class StoreGameRequestTest extends LaravelTestCase
{
    use TestsFormRequests;

    #[DataProvider('validationErrorProvider')]
    public function test_validation_errors(RequestFactory $factory, string $field): void
    {
        $this->formRequest(StoreGameRequest::class, $factory->create())->assertValidationErrors([$field]);

    }

    /**
     * @return array<mixed>
     */
    public static function validationErrorProvider(): array
    {
        $f = StoreGameRequestFactory::new();

        return [
            'name required' => [$f->without('name'), 'name'],
            'name string' => [$f->override('name', 123), 'name'],
            'phaseLengthInMinutes required' => [$f->without('phaseLengthInMinutes'), 'phaseLengthInMinutes'],
            'phaseLengthInMinutes integer' => [$f->override('phaseLengthInMinutes', 'abc'), 'phaseLengthInMinutes'],
            'phaseLengthInMinutes min' => [$f->override('phaseLengthInMinutes', 9), 'phaseLengthInMinutes'],
            'phaseLengthInMinutes max' => [$f->override('phaseLengthInMinutes', 1441), 'phaseLengthInMinutes'],
            'joinLengthInDays required' => [$f->without('joinLengthInDays'), 'joinLengthInDays'],
            'joinLengthInDays integer' => [$f->override('joinLengthInDays', 'abc'), 'joinLengthInDays'],
            'joinLengthInDays min' => [$f->override('joinLengthInDays', 0), 'joinLengthInDays'],
            'joinLengthInDays max' => [$f->override('joinLengthInDays', 366), 'joinLengthInDays'],
            'startWhenReady required' => [$f->without('startWhenReady'), 'startWhenReady'],
            'startWhenReady boolean' => [$f->override('startWhenReady', 'abc'), 'startWhenReady'],
            'variantId required' => [$f->without('variantId'), 'variantId'],
            'variantId uuid' => [$f->override('variantId', 'abc'), 'variantId'],
        ];
    }
}
