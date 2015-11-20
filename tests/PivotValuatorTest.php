<?php

use Dvlpp\Sharp\Config\FormFields\SharpPivotFormFieldConfig;
use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\PivotValuator;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;

class PivotValuatorTest extends TestCase
{
    /** @test */
    public function pivot_values_are_synced()
    {
        $instance = Mockery::mock(TestPivotEntityModelWithPivot::class);
        $sharpRepo = Mockery::mock(SharpCmsRepository::class);

        $instance->shouldReceive("getKey")->andReturn(null);
        $instance->shouldReceive("save");
        $instance->shouldReceive("pivot")->andReturn(
            Mockery::mock(TestPivotEntityModelWithPivot::class)->makePartial()
        );

        (new PivotValuator($instance, "pivot", ["one", "two"], $this->pivotConfig(), $sharpRepo))
            ->valuate();
    }

    private function pivotConfig()
    {
        return SharpPivotFormFieldConfig::create("pivot", null)
            ->setAddable(true);
    }
}

class TestPivotEntityModelWithPivot extends TestEntity {
    public function sync(array $values) {
    }
}