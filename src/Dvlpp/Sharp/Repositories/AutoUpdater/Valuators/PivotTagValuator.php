<?php namespace Dvlpp\Sharp\Repositories\AutoUpdater\Valuators;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Illuminate\Support\Str;

/**
 * Class PivotTagValuator
 * @package Dvlpp\Sharp\Repositories\AutoUpdater\Valuators
 */
class PivotTagValuator implements Valuator {

    /**
     * @var
     */
    private $instance;

    /**
     * @var string
     */
    private $pivotKey;

    /**
     * @var array
     */
    private $dataPivot;

    /**
     * @var
     */
    private $pivotConfig;
    /**
     * @var SharpCmsRepository
     */
    private $sharpRepository;

    /**
     * @param $instance
     * @param $pivotKey
     * @param $dataPivot
     * @param $pivotConfig
     * @param SharpCmsRepository $sharpRepository
     */
    function __construct($instance, $pivotKey, $dataPivot, $pivotConfig, SharpCmsRepository $sharpRepository)
    {
        $this->instance = $instance;
        $this->pivotKey = $pivotKey;
        $this->dataPivot = $dataPivot;
        $this->pivotConfig = $pivotConfig;
        $this->sharpRepository = $sharpRepository;
    }

    /**
     * Valuate the field
     */
    public function valuate()
    {
        // First save the entity if new and transient (pivot creation would be impossible if entity has no ID)
        if(!$this->instance->getKey()) $this->instance->save();

        $isCreatable = $this->pivotConfig->addable ?: false;
        $createAttribute = $isCreatable ? $this->pivotConfig->create_attribute : null;
        $hasOrder = $this->pivotConfig->sortable ?: false;
        $orderAttribute = $hasOrder ? $this->pivotConfig->order_attribute : null;

        $existingPivots = [];
        $newPivots = [];
        $order = 1;
        foreach($this->dataPivot as $d)
        {
            if(!starts_with($d, '#'))
            {
                // Existing tag
                if($hasOrder)
                {
                    $existingPivots[$d] = [$orderAttribute=>$order++];
                }
                else
                {
                    $existingPivots[] = $d;
                }
            }

            elseif($isCreatable)
            {
                // Create a new tag
                $newPivots[$order++] = substr($d, 1);
            }
        }

        // Sync existing ones
        $this->instance->{$this->pivotKey}()->sync($existingPivots);

        // Create new
        $methodName = "create" . ucFirst(Str::camel($this->pivotKey)) . "PivotTag";

        foreach($newPivots as $order => $newPivot)
        {
            if(method_exists($this->sharpRepository, $methodName))
            {
                // There's a special method for that in the repo
                $this->sharpRepository->$methodName($newPivot, $order);
            }
            else
            {
                // We have to create this ourselves
                $joiningArray = $orderAttribute ? [$orderAttribute => $order] : [];

                $this->instance->{$this->pivotKey}()->create([$createAttribute => $newPivot], $joiningArray);
            }
        }
    }

} 