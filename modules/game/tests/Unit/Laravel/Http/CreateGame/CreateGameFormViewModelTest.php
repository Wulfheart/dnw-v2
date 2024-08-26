<?php

namespace Dnw\Game\Tests\Unit\Laravel\Http\CreateGame;

use Dnw\Foundation\Identity\Id;
use Dnw\Game\Core\Application\Query\GetAllVariants\VariantDto;
use Dnw\Game\Core\Application\Query\GetAllVariants\VariantPowerDto;
use Dnw\Game\Http\CreateGame\CreateGameFormViewModel;
use Dnw\Game\ViewModel\CreateGame\ViewModel\VariantInformationOption;
use Dnw\Game\ViewModel\CreateGame\ViewModel\VariantInformationOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CreateGameFormViewModel::class)]
#[CoversClass(VariantInformationOptions::class)]
#[CoversClass(VariantInformationOption::class)]
class CreateGameFormViewModelTest extends TestCase
{
    public function test_fromLaravel(): void
    {
        $variants = [
            new VariantDto(
                Id::generate(),
                '::NAME_1::',
                '::DESCRIPTION_1::',
                [
                    new VariantPowerDto(Id::generate(), '::POWER_1::'),
                    new VariantPowerDto(Id::generate(), '::POWER_2::'),
                    new VariantPowerDto(Id::generate(), '::POWER_3::'),
                ]
            ),
            new VariantDto(
                Id::generate(),
                '::NAME_2::',
                '::DESCRIPTION_2::',
                [
                    new VariantPowerDto(Id::generate(), '::2POWER_1::'),
                    new VariantPowerDto(Id::generate(), '::2POWER_2::'),
                    new VariantPowerDto(Id::generate(), '::2POWER_3::'),
                ]
            ),
        ];

        $vm = CreateGameFormViewModel::fromLaravel($variants);

        $this->expectNotToPerformAssertions();
    }
}