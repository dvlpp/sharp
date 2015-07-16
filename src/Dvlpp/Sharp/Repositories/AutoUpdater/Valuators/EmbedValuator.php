<?php namespace Dvlpp\Sharp\Repositories\AutoUpdater\Valuators;

use Dvlpp\Sharp\Repositories\AutoUpdater\SharpEloquentAutoUpdaterService;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * Class EmbedValuator
 * @package Dvlpp\Sharp\Repositories\AutoUpdater\Valuators
 */
class EmbedValuator implements Valuator {
    /**
     * @var
     */
    private $instance;

    /**
     * @var string
     */
    private $attr;

    /**
     * @var array
     */
    private $data;

    /**
     * @var SharpCmsRepository
     */
    private $sharpRepository;

    /**
     * @var
     */
    private $embedFieldConfig;

    /**
     * @var integer
     * DB id of the embed object
     */
    protected $listItemEmbedId;

    /**
     * @var
     * Optional callback function called before the instance is persisted
     */
    protected $callbackBeforeSave;


    /**
     * @param $instance
     * @param $attr
     * @param $data
     * @param $embedFieldConfig
     * @param $sharpRepository
     */
    function __construct($instance, $attr, $data, $embedFieldConfig, $sharpRepository)
    {
        $this->instance = $instance;
        $this->attr = $attr;
        $this->data = $data;
        $this->sharpRepository = $sharpRepository;
        $this->embedFieldConfig = $embedFieldConfig;
    }

    /**
     * Valuate the field
     */
    public function valuate()
    {

        // First catch the DB instance
        $embeddedInstance = $this->instance->{$this->attr};
        if($embeddedInstance instanceof Collection)
        {
            // Embed-list case: we have to find the correct item
            $embeddedInstance = $this->findItem($embeddedInstance);
        }

        // Special cases: deletion, empty value
        if($this->data == "__DELETE__" || $this->data == "")
        {
            // Deleted instance
            if($embeddedInstance)
            {
                // We have to delete the embedded instance
                $embeddedInstance->delete();
            }

            return null;
        }

        // Embed data is compressed: we have to decode it.
        $embeddedData = sharp_decode_embedded_entity_data($this->data);

        // First save the entity if new and transient (embed creation would be impossible if entity has no ID)
        if(!$this->instance->getKey()) $this->instance->save();

        if(!$embeddedInstance)
        {
            // Embedded instance needs to be created. We test if there is a special hook method on the controller
            // that takes the precedence. Method name should be "create[key]Embed"
            $methodName = "create" . ucFirst(Str::camel($this->attr)) . "Embed";

            if(method_exists($this->sharpRepository, $methodName))
            {
                $embeddedInstance = $this->sharpRepository->$methodName($this->instance);
            }
            else
            {
                // Have to create this embedded instance: we can't use $entity->$attr()->create([]), because
                // we don't want a ->save() call on the object (which could fail because of mandatory DB attribute)
                $embeddedInstance = $this->instance->{$this->attr}()->getRelated()->newInstance([]);
                $embeddedInstance->setAttribute(
                    $this->instance->{$this->attr}()->getPlainForeignKey(),
                    $this->instance->{$this->attr}()->getParentKey());
            }
        }

        // Update embedded instance
        $autoUpdaterService = new SharpEloquentAutoUpdaterService;
        return $autoUpdaterService->updateEntityWithConfig(
            $this->sharpRepository, $this->embedFieldConfig,
            $embeddedInstance, $embeddedData, $this->callbackBeforeSave);
    }

    /**
     * @param $embedId
     */
    public function setListItemEmbedId($embedId)
    {
        $this->listItemEmbedId = $embedId;
    }

    /**
     * @param $callback
     */
    public function setCallbackBeforeSave($callback)
    {
        $this->callbackBeforeSave = $callback;
    }

    /**
     * @param $embeddedInstancesList
     * @return object|null
     */
    private function findItem($embeddedInstancesList)
    {
        if ($this->listItemEmbedId)
        {
            foreach ($embeddedInstancesList as $instance)
            {
                if ($instance->getKey() == $this->listItemEmbedId)
                {
                    return $instance;
                }
            }
        }

        return null;
    }

} 