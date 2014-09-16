<?php

use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Exceptions\InstanceNotFoundException;
use Dvlpp\Sharp\Exceptions\ValidationException;

/**
 * Class CmsEmbeddedEntityController
 */
class CmsEmbeddedEntityController extends BaseController {

    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @return mixed
     */
    function create($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey)
    {
        // We flush input if we came directly from the master form, because Input::old() can otherwise be conflictual
        // (in case of attributes with same key)
        if(!Input::old("masterInstanceData")) Input::flush();

        return $this->form($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey);
    }

    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @param $id
     * @return mixed
     */
    function edit($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $id)
    {
        // We flush input if we came directly from the master form, because Input::old() can otherwise be conflictual
        // (in case of attributes with same key)
        if(!Input::old("masterInstanceData")) Input::flush();

        return $this->form($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $id);
    }

    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @param $id
     * @return mixed
     */
    public function update($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $id)
    {
        return $this->save($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $id);
    }

    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @return mixed
     */
    public function store($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey)
    {
        return $this->save($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, null, true);
    }

    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @return mixed
     */
    public function cancel($masterCategoryKey, $masterEntityKey)
    {
        // We are going back to the master form. Let's restore the master form data...
        Session::flash('masterInstanceData', serialize(sharp_decode_embedded_entity_data(Input::get('masterInstanceData'))));

        // ... and redirect back to the master entity form
        return $this->redirectToMaster($masterCategoryKey, $masterEntityKey, Input::get('masterInstanceId'));
    }


    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @param null $id
     * @return mixed
     * @throws Dvlpp\Sharp\Exceptions\InstanceNotFoundException
     */
    private function form($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $id=null)
    {
        if(!Input::old("masterInstanceData"))
        {
            // First time this form is displayed, meaning we are coming from a "master entity"
            // We have to check this because the only way to retrieve the master instance id
            // is to look at the Input (because the master entity form was posted to get here)

            // Get the master instance id (to determine if we are in a master entity update or create)
            $masterEntityConfig = SharpCmsConfig::findEntity($masterCategoryKey, $masterEntityKey);
            $masterInstanceId = Input::get($masterEntityConfig->id_attribute);

            // Get the master instance data (to repopulate the form after)
            $masterInstanceData = sharp_encode_embedded_entity_data(Input::except("_token", "_method"));

            $masterEntityLabel = $masterEntityConfig->label;

            if(Input::has($masterFieldKey))
            {
                // The embed instance is already "transient" (was updated before but not persisted yet)
                // We have to repopulate the embed form (this form) as it was before
                $masterFieldValue = Input::get($masterFieldKey);
                if($masterFieldValue != "__DELETE__")
                {
                    $formOldDataStr = sharp_decode_embedded_entity_data($masterFieldValue);
                    Session::flashInput($formOldDataStr);
                }
            }

        }
        else
        {
            $masterInstanceData = Input::old("masterInstanceData");
            $masterInstanceId = Input::old("masterInstanceId");
            $masterEntityLabel = Input::old("masterEntityLabel");
        }

        // Find Entity config (from sharp CMS config file)
        $embeddedEntity = SharpCmsConfig::findEntity($embeddedCategoryKey, $embeddedEntityKey);

        // Instantiate the entity repository
        $repo = App::make($embeddedEntity->repository);

        // Retrieve the corresponding DB entity
        $instance = $id && !starts_with($id, "N_") ? $repo->find($id) : $repo->newInstance();

        if($instance)
        {
            // And return the View
            return View::make('sharp::cms.entityForm', [
                'instance'=>$instance,
                'entity'=>$embeddedEntity,
                'category'=>SharpCmsConfig::findCategory($embeddedCategoryKey),
                'isEmbedded'=>true,
                'masterCategoryKey'=>$masterCategoryKey,
                'masterEntityKey'=>$masterEntityKey,
                'masterInstanceId'=>$masterInstanceId,
                'masterEntityLabel'=>$masterEntityLabel,
                'masterInstanceData'=>$masterInstanceData,
                'masterFieldKey'=>$masterFieldKey
            ]);
        }
        else
        {
            throw new InstanceNotFoundException("Instance of id [$id] and type [$embeddedCategoryKey.$embeddedEntityKey] can't be found");
        }
    }


    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @param $id
     * @param bool $creation
     * @return mixed
     */
    private function save($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $id, $creation=false)
    {
        $data = Input::all();

        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($embeddedCategoryKey, $embeddedEntityKey);

        try {
            // First : validation
            if($entity->validator)
            {
                $validator = App::make($entity->validator);
                $validator->validate($data, !$creation?$id:null);
            }

            // Data is valid, we are going back to the master form. Let's restore the master form data...
            $masterInstanceData = sharp_decode_embedded_entity_data($data['masterInstanceData']);

            // ... add the embedded form data...
            $embeddedInstanceData = sharp_encode_embedded_entity_data(
                Input::except(["_method", "_token", "masterInstanceData", "masterInstanceId", "masterEntityLabel"])
            );

            if(strpos($masterFieldKey, ".") !== false)
            {
                // Embedded instance in part of a list item
                list($listKey, $itemId, $fieldKey) = explode(".", $masterFieldKey);
                $masterInstanceData[$listKey][$itemId][$fieldKey] = $embeddedInstanceData;
            }
            else
            {
                $masterInstanceData[$masterFieldKey] = $embeddedInstanceData;
            }

            Session::flash('masterInstanceData', serialize($masterInstanceData));

            // ... and redirect back to the master entity form
            return $this->redirectToMaster($masterCategoryKey, $masterEntityKey, $data['masterInstanceId']);
        }

        catch(ValidationException $e)
        {
            return Redirect::back()->withInput()->withErrors($e->getErrors());
        }
    }

    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterInstanceId
     * @return mixed
     */
    private function redirectToMaster($masterCategoryKey, $masterEntityKey, $masterInstanceId)
    {
        if($masterInstanceId)
        {
            return Redirect::route('cms.edit', [$masterCategoryKey, $masterEntityKey, $masterInstanceId]);
        }
        else
        {
            return Redirect::route('cms.create', [$masterCategoryKey, $masterEntityKey]);
        }
    }

} 