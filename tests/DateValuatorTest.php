<?php

use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\DateValuator;

class DateValuatorTest extends TestCase
{
    /** @test */
    public function date_field_is_updated()
    {
        $instance = new stdClass;

        $date = \Carbon\Carbon::create(2015, 11, 20, 8, 20)
            ->format("Ymd H:i:s");

        (new DateValuator($instance, "date", $date))->valuate();

        $this->assertEquals($instance->date, "2015-11-20 08:20:00");
    }


}