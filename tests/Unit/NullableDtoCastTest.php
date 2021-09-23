<?php

namespace Tests\Unit;

use App\Casts\NullableDtoCast;
use App\Models\AbstractModel;
use Tests\AbstractUnitTest;
use Tests\Dummies\DummyDto;

class NullableDtoCastTest extends AbstractUnitTest
{
    public function test_non_null_get()
    {
        $data = [
            'foo' => 'fooValue',
            'bar' => 'barValue',
            'n' => 6,
            'arr' => ['foo', 'bar', 'baz'],
        ];
        $value = json_encode($data);

        $model = new class extends AbstractModel {};

        $cast = new NullableDtoCast(DummyDto::class);

        /** @var DummyDto $dto */
        $dto = $cast->get(
            $model,
            'dummy',
            $value,
            ['dummy' => $value],
        );

        $this->assertTrue($dto instanceof DummyDto);
        $this->assertEquals('fooValue', $dto->foo);
        $this->assertEquals('barValue', $dto->bar);
        $this->assertEquals(6, $dto->n);
        $this->assertEquals(['foo', 'bar', 'baz'], $dto->arr);
        $this->assertEquals($data, $dto->toArray());
    }

    public function test_null_get()
    {
        $model = new class extends AbstractModel {};

        $cast = new NullableDtoCast(DummyDto::class);

        $result = $cast->get(
            $model,
            'dummy',
            null,
            ['dummy' => null],
        );

        $this->assertNull($result);
    }

    public function test_non_null_set()
    {
        $data = [
            'foo' => 'fooValue',
            'bar' => 'barValue',
            'n' => 6,
            'arr' => ['foo', 'bar', 'baz'],
        ];
        $dto = DummyDto::fromArray($data);

        $model = new class extends AbstractModel {};

        $cast = new NullableDtoCast(DummyDto::class);

        $value = $cast->set(
            $model,
            'dummy',
            $dto,
            [],
        );

        $this->assertEquals(json_encode($data), $value);
    }

    public function test_null_set()
    {
        $model = new class extends AbstractModel {};

        $cast = new NullableDtoCast(DummyDto::class);

        $value = $cast->set(
            $model,
            'dummy',
            null,
            [],
        );

        $this->assertNull($value);
    }
}
