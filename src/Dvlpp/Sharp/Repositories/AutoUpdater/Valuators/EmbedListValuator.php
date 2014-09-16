<?php namespace Dvlpp\Sharp\Repositories\AutoUpdater\Valuators;

use Dvlpp\Sharp\Config\Entities\SharpEntity;
use Dvlpp\Sharp\Config\Entities\SharpEntityFormField;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Illuminate\Support\Str;

class EmbedListValuator implements Valuator {

    /**
     * @var Object
     * The entity instance which owns the embedList
     */
    private $instance;

    /**
     * @var string
     * The embedList key
     */
    private $attr;

    /**
     * @var array
     * The posted array of embeds
     */
    private $items;

    /**
     * @var SharpCmsRepository
     */
    private $sharpRepository;

    /**
     * @var SharpEntity
     */
    private $embedEntityConfig;

    /**
     * @var SharpEntityFormField
     */
    private $embedListConfig;


    /**
     * @param $instance
     * @param $attr
     * @param $data
     * @param $embedListConfig
     * @param $embedEntityConfig
     * @param $sharpRepository
     */
    function __construct($instance, $attr, $data, $embedListConfig, $embedEntityConfig, $sharpRepository)
    {
        $this->instance = $instance;
        $this->attr = $attr;
        $this->items = $data;
        $this->sharpRepository = $sharpRepository;
        $this->embedEntityConfig = $embedEntityConfig;
        $this->embedListConfig = $embedListConfig;
    }

    /**
     * Valuate the field
     */
    function valuate()
    {
        if(is_array($this->items))
        {
            $saved = [];
            $itemIdAttribute = $this->embedEntityConfig->item_id_attribute ?: "id";
            $order = 0;

            // Iterate items posted
            foreach($this->items as $item)
            {
                $itemId = $item[$itemIdAttribute];

                $valuator = new EmbedValuator($this->instance, $this->attr, $item['__embed_data'], $this->embedEntityConfig, $this->sharpRepository);

                // Sets the embed id (if not new)
                $valuator->setListItemEmbedId(Str::startsWith($itemId, "N_") ? null : $itemId);

                // Manage order with a callback before save
                if($this->embedListConfig->order_attribute)
                {
                    $valuator->setCallbackBeforeSave(function($instance) use($order)
                    {
                        $instance->{$this->embedListConfig->order_attribute} = $order;
                    });
                    $order++;
                }

                // And valuate the object
                $valuator->valuate();

                // Keep reference of the item for deletions
                $saved[] = $itemId;
            }

            // Manage deletions of the non-present items
            foreach($this->instance->{$this->attr} as $itemDb)
            {
                if(!in_array($itemDb->$itemIdAttribute, $saved))
                {
                    $itemDb->delete();
                }
            }
        }

        else
        {
            // No item sent.
            foreach($this->instance->{$this->attr} as $itemDb)
            {
                $itemDb->delete();
            }
        }
    }
}