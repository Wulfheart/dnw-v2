<?php

namespace App\Foundation\Id;

use Dnw\Foundation\Identity\Id;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FakeIdGenerator::class)]
class FakeIdGeneratorTest extends TestCase
{
    public function test_generates_the_supplied_ids(): void
    {
        $first = Id::generate();
        $second = Id::generate();
        $third = Id::generate();
        $generator = new FakeIdGenerator([$first, $second]);
        $generator->addId($third);
        $id = $generator->generate();
        $this->assertSame($first, $id);
        $id = $generator->generate();
        $this->assertSame($second, $id);
        $id = $generator->generate();
        $this->assertSame($third, $id);
    }

    public function test_throws_error_if_generation_is_out_of_bounds(): void
    {
        $first = Id::generate();
        $second = Id::generate();
        $generator = new FakeIdGenerator([$first, $second]);
        $id = $generator->generate();
        $id = $generator->generate();

        $this->expectException(Exception::class);
        $generator->generate();
    }
}
