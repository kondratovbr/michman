<?php

namespace Tests\Unit;

use Tests\AbstractUnitTest;
use Tests\Dummies\DummyDto;
use ArgumentCountError;
use Throwable;

class DtoTest extends AbstractUnitTest
{
    public function test_positional_creation()
    {
        $dto = new DummyDto('foo', 'bar', 6, [1, 2, 3]);

        $this->assertEquals('foo', $dto->foo);
        $this->assertEquals('bar', $dto->bar);
        $this->assertEquals(6, $dto->n);
        $this->assertEquals([1, 2, 3], $dto->arr);
    }

    public function test_named_creation()
    {
        $dto = new DummyDto(
            foo: 'foo',
            bar: 'bar',
            n: 6,
            arr: [1, 2, 3],
        );

        $this->assertEquals('foo', $dto->foo);
        $this->assertEquals('bar', $dto->bar);
        $this->assertEquals(6, $dto->n);
        $this->assertEquals([1, 2, 3], $dto->arr);
    }

    public function test_array_creation()
    {
        $dto = DummyDto::fromArray([
            'bar' => 'bar',
            'foo' => 'foo',
            'n' => 6,
            'arr' => [1, 2, 3],
        ]);

        $this->assertEquals('foo', $dto->foo);
        $this->assertEquals('bar', $dto->bar);
        $this->assertEquals(6, $dto->n);
        $this->assertEquals([1, 2, 3], $dto->arr);
    }

    public function test_error_on_missing_array_parameters()
    {
        try {
            $dto = DummyDto::fromArray([
                'foo' => 'foo',
                'bar' => 'bar',
            ]);
        } catch (Throwable $e) {
            $this->assertTrue($e instanceof ArgumentCountError);
        }
    }

    public function test_to_array()
    {
        $dto = DummyDto::fromArray([
            'arr' => [1, 2, 3],
            'bar' => 'bar',
            'foo' => 'foo',
            'n' => 6,
        ]);

        $this->assertEquals([
            'foo' => 'foo',
            'bar' => 'bar',
            'n' => 6,
            'arr' => [1, 2, 3],
        ], $dto->toArray());
    }

    public function test_add()
    {
        $dto = DummyDto::fromArray([
            'arr' => [1, 2, 3],
            'bar' => 'bar',
            'foo' => 'foo',
            'n' => 6,
        ]);

        $this->assertEquals([
            'foo' => 'foo',
            'bar' => 'bar',
            'n' => 6,
            'arr' => [1, 2, 3],
            'foobar' => 666,
        ], $dto->toArray([
            'foobar' => 666,
        ]));
    }

    public function test_only()
    {
        $dto = DummyDto::fromArray([
            'arr' => [1, 2, 3],
            'bar' => 'bar',
            'foo' => 'foo',
            'n' => 6,
        ]);

        $this->assertEquals([
            'foo' => 'foo',
            'n' => 6,
        ], $dto->only('foo', 'n')->toArray());
    }

    public function test_except()
    {
        $dto = DummyDto::fromArray([
            'arr' => [1, 2, 3],
            'bar' => 'bar',
            'foo' => 'foo',
            'n' => 6,
        ]);

        $this->assertEquals([
            'foo' => 'foo',
            'n' => 6,
        ], $dto->except('bar', 'arr')->toArray());
    }

    public function test_only_add()
    {
        $dto = DummyDto::fromArray([
            'arr' => [1, 2, 3],
            'bar' => 'bar',
            'foo' => 'foo',
            'n' => 6,
        ]);

        $this->assertEquals([
            'foo' => 'foo',
            'n' => 6,
            'foobar' => 666,
        ], $dto->only('foo', 'n')->toArray([
            'foobar' => 666,
        ]));
    }

    public function test_except_add()
    {
        $dto = DummyDto::fromArray([
            'arr' => [1, 2, 3],
            'bar' => 'bar',
            'foo' => 'foo',
            'n' => 6,
        ]);

        $this->assertEquals([
            'foo' => 'foo',
            'n' => 6,
            'foobar' => 666,
        ], $dto->except('bar', 'arr')->toArray([
            'foobar' => 666,
        ]));
    }
}
