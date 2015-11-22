<?php

use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\SimpleValuator;

class SimpleValuatorTest extends TestCase
{
    /** @test */
    public function text_field_is_updated()
    {
        $instance = new stdClass;

        (new SimpleValuator($instance, "text", "test"))
            ->valuate();

        $this->assertEquals($instance->text, "test");
    }
}