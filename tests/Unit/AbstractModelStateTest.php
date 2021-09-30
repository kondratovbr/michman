<?php

namespace Tests\Unit;

use Tests\AbstractUnitTest;
use Tests\Dummies\DummyModelWithState;
use Tests\Dummies\DummyStates\First;
use Tests\Dummies\DummyStates\Second;
use Tests\Dummies\DummyStates\Third;
use Tests\Dummies\McDummyStates\McFirst;
use Tests\Dummies\McDummyStates\McSecond;

class AbstractModelStateTest extends AbstractUnitTest
{
    public function test_truthy_case_of_is()
    {
        $model = new DummyModelWithState([
            'state' => First::class,
        ]);

        $this->assertTrue($model->state->is(First::class));
    }

    public function test_falsy_case_of_is()
    {
        $model = new DummyModelWithState([
            'state' => First::class,
        ]);

        $this->assertFalse($model->state->is(Second::class));
    }

    public function test_invalid_case_of_is()
    {
        $model = new DummyModelWithState([
            'state' => First::class,
        ]);

        $this->expectException(\RuntimeException::class);

        $model->state->is(McFirst::class);
    }

    public function test_truthy_array_of_is()
    {
        $model = new DummyModelWithState([
            'state' => First::class,
        ]);

        $this->assertTrue($model->state->is([Second::class, First::class]));
    }

    public function test_falsy_array_of_is()
    {
        $model = new DummyModelWithState([
            'state' => First::class,
        ]);

        $this->assertFalse($model->state->is([Second::class, Third::class]));
    }

    public function test_invalid_array_of_is()
    {
        $model = new DummyModelWithState([
            'state' => First::class,
        ]);

        $this->expectException(\RuntimeException::class);

        $model->state->is([McFirst::class, McSecond::class]);
    }

    public function test_cannot_transition_to_the_same_state()
    {
        $model = new DummyModelWithState([
            'state' => First::class,
        ]);

        $this->assertFalse($model->state->canTransitionTo(First::class));
    }
}
