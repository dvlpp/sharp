<?php

namespace Dvlpp\Sharp\Repositories\AutoUpdater\Valuators;

use Dvlpp\Sharp\Repositories\AutoUpdater\SharpEloquentAutoUpdaterService;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use InvalidArgumentException;

/**
 * Class ListValuator
 * @package Dvlpp\Sharp\Repositories\AutoUpdater\Valuators
 */
class ListValuator implements Valuator
{

    /**
     * @var
     */
    private $instance;

    /**
     * @var string
     */
    private $listKey;

    /**
     * @var array
     */
    private $itemsForm;

    /**
     * @var
     */
    private $listFieldConfig;

    /**
     * @var SharpCmsRepository
     */
    private $sharpRepository;

    /**
     * @var SharpEloquentAutoUpdaterService
     */
    private $autoUpdater;

    /**
     * @param $instance
     * @param $listKey
     * @param $itemsForm
     * @param $listFieldConfig
     * @param $sharpRepository
     * @param \Dvlpp\Sharp\Repositories\AutoUpdater\SharpEloquentAutoUpdaterService $autoUpdater
     */
    function __construct(
        $instance,
        $listKey,
        $itemsForm,
        $listFieldConfig,
        $sharpRepository,
        SharpEloquentAutoUpdaterService $autoUpdater
    ) {
        $this->instance = $instance;
        $this->listKey = $listKey;
        $this->itemsForm = $itemsForm;
        $this->listFieldConfig = $listFieldConfig;
        $this->sharpRepository = $sharpRepository;
        $this->autoUpdater = $autoUpdater;
    }

    /**
     * Valuate the field
     */
    public function valuate()
    {
        // First save the entity if new and transient (pivot creation would be impossible if entity has no ID)
        if (!$this->instance->getKey()) {
            $this->instance->save();
        }

        $order = 0;
        $saved = [];

        if (is_array($this->itemsForm)) {
            $itemIdAttribute = $this->listFieldConfig->itemIdAttribute() ?: "id";

            // Iterate items posted
            foreach ($this->itemsForm as $itemForm) {
                $item = null;
                $itemId = $itemForm[$itemIdAttribute];

                if (starts_with($itemId, "N_")) {
                    // First test if there is a special hook method on the controller
                    // that takes the precedence. Method name should be "create[$listKey]ListItem"
                    $methodName = "create" . ucFirst(camel_case($this->listKey)) . "ListItem";

                    if (method_exists($this->sharpRepository, $methodName)) {
                        $item = $this->sharpRepository->$methodName($this->instance);
                    } else {
                        // Have to create this item : we can't use $entity->$listKey()->create([]), because
                        // we don't want a ->save() call on the item (which could fail because of mandatory DB attribute)
                        $item = $this->instance->{$this->listKey}()->getRelated()->newInstance([]);
                        $item->setAttribute(
                            $this->instance->{$this->listKey}()->getPlainForeignKey(),
                            $this->instance->{$this->listKey}()->getParentKey());
                    }
                } else {
                    foreach ($this->instance->{$this->listKey} as $itemDb) {
                        if ($itemDb->$itemIdAttribute == $itemId) {
                            // DB item found
                            $item = $itemDb;
                            break;
                        }
                    }
                }

                if (!$item) {
                    // Item can't be found and isn't new. It's an error.
                    throw new InvalidArgumentException("Item [$itemId] can't be found.");
                }

                // Update item
                $methodName = "update" . ucFirst(camel_case($this->listKey)) . "ListItem";

                if (method_exists($this->sharpRepository, $methodName)) {
                    $item = $this->sharpRepository->$methodName($this->instance, $item, $itemForm);
                } else {
                    foreach ($itemForm as $attr => $value) {
                        if ($attr == $itemIdAttribute) {
                            // Id is not updatable
                            continue;
                        }

                        // For other attributes:
                        $itemFormFieldConfig = $this->listFieldConfig->findItemField($attr);
                        if($itemFormFieldConfig) {
                            // Call the auto-updater updateField method
                            $this->autoUpdater->updateField($item, $itemForm, $itemFormFieldConfig, $this->listKey);
                        }

//                        foreach ($this->listFieldConfig->itemFormFieldsConfig() as $itemFormFieldConfig) {
//                            if ($itemFormFieldConfig->key() == $attr) {
//                                $configListItemConfigAttr = $this->listFieldConfig->item->$configListItemKey;
//
//                                // Call the auto-updater updateField method
//                                $this->autoUpdater->updateField($item, $itemForm, $configListItemConfigAttr,
//                                    $configListItemKey, $this->listKey);
//                            }
//                        }
                    }
                }

                // Manage order
                if ($this->listFieldConfig->orderAttribute()) {
                    if (str_contains($this->listFieldConfig->orderAttribute(), "~")) {
                        list($relation, $orderAttr) = explode('~', $this->listFieldConfig->orderAttribute());
                        $itemRelation = $item->{$relation};
                        $itemRelation->{$orderAttr} = $order;
                        $itemRelation->save();
                    } else {
                        $item->{$this->listFieldConfig->orderAttribute()} = $order;
                    }
                    $order++;
                }

                // Eloquent save
                $item->save();

                // Keep reference of the item for deletions
                $saved[] = $item->$itemIdAttribute;
            }

            // Manage deletions of the non-present items
            foreach ($this->instance->{$this->listKey} as $itemDb) {
                if (!in_array($itemDb->$itemIdAttribute, $saved)) {
                    $this->deleteItem($itemDb);
                }
            }

        } else {
            // No item sent.
            foreach ($this->instance->{$this->listKey} as $itemDb) {
                $this->deleteItem($itemDb);
            }
        }
    }

    private function deleteItem($itemDb)
    {
        $methodName = "delete" . ucFirst(camel_case($this->listKey)) . "ListItem";

        if (method_exists($this->sharpRepository, $methodName)
            && !$this->sharpRepository->$methodName($this->instance, $itemDb)
        ) {
            // Deletion managed in the repository
            return;
        }

        $itemDb->delete();
    }

} 