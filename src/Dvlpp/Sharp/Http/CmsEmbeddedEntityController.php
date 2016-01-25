<?php namespace Dvlpp\Sharp\Http;

use Dvlpp\Sharp\Config\SharpCmsConfig;
use Dvlpp\Sharp\Exceptions\InstanceNotFoundException;
use Dvlpp\Sharp\Exceptions\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class CmsEmbeddedEntityController
 */
class CmsEmbeddedEntityController extends Controller {

    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @param Request $request
     * @return mixed
     * @throws InstanceNotFoundException
     */
    function create($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, Request $request)
    {
        // We flush input if we came directly from the master form, because Input::old() can otherwise be conflictual
        // (in case of attributes with same key)
        if( ! $request->old("masterInstanceData")) $request->flush();

        return $this->form($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $request);
    }

    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @param $id
     * @param Request $request
     * @return mixed
     * @throws InstanceNotFoundException
     */
    function edit($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $id, Request $request)
    {
        // We flush input if we came directly from the master form, because Input::old() can otherwise be conflictual
        // (in case of attributes with same key)
        if( ! $request->old("masterInstanceData")) $request->flush();

        return $this->form($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $request, $id);
    }

    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $id, Request $request)
    {
        return $this->save($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $request, $id);
    }

    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @return mixed
     */
    public function store($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, Request $request)
    {
        return $this->save($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, $request, null, true);
    }

    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param Request $request
     * @return mixed
     */
    public function cancel($masterCategoryKey, $masterEntityKey, Request $request)
    {
        // We are going back to the master form. Let's restore the master form data...
        \Session::flash('masterInstanceData', serialize(sharp_decode_embedded_entity_data($request->get('masterInstanceData'))));

        // ... and redirect back to the master entity form
        return $this->redirectToMaster($masterCategoryKey, $masterEntityKey, $request->get('masterInstanceId'));
    }


    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @param Request $request
     * @param null $id
     * @return mixed
     * @throws InstanceNotFoundException
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    private function form($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, Request $request, $id=null)
    {
        $isSharpDuplication = false;

        if( ! $request->old("masterInstanceData"))
        {
            // First time this form is displayed, meaning we are coming from a "master entity"
            // We have to check this because the only way to retrieve the master instance id
            // is to look at the Input (because the master entity form was posted to get here)

            // Get the master instance id (to determine if we are in a master entity update or create)
            $masterEntityConfig = SharpCmsConfig::findEntity($masterCategoryKey, $masterEntityKey);
            $masterInstanceId = $request->get($masterEntityConfig->id_attribute);

            // Get the master instance data (to repopulate the form after)
            $masterInstanceData = sharp_encode_embedded_entity_data($request->except(["_token", "_method"]));

            $masterEntityLabel = $masterEntityConfig->label;

            if($request->has($masterFieldKey))
            {
                // The embed instance is already "transient" (was updated before but not persisted yet)
                // We have to repopulate the embed form (this form) as it was before

                // Don't know why I have to change the dotted notation to brackets, but it won't
                // work without this (update in 2.0.7 version)
                list($masterFieldKey1, $masterFieldKey2, $masterFieldKey3) = explode(".", $masterFieldKey);
                $masterFieldValue = $request->get($masterFieldKey1."[".$masterFieldKey2."][".$masterFieldKey3."]", null, true);

                if($masterFieldValue != "__DELETE__")
                {
                    $formOldDataStr = sharp_decode_embedded_entity_data($masterFieldValue);
                    $isSharpDuplication = isset($formOldDataStr["__sharp_duplication"]) && $formOldDataStr["__sharp_duplication"];
                    \Session::flashInput($formOldDataStr);
                }
            }

        }
        else
        {
            $masterInstanceData = $request->old("masterInstanceData");
            $masterInstanceId = $request->old("masterInstanceId");
            $masterEntityLabel = $request->old("masterEntityLabel");
        }

        // Find Entity config (from sharp CMS config file)
        $embeddedEntity = SharpCmsConfig::findEntity($embeddedCategoryKey, $embeddedEntityKey);

        // Instantiate the entity repository
        $repo = app($embeddedEntity->repository);

        // Retrieve the corresponding DB entity
        $instance = $id && !starts_with($id, "N_") ? $repo->find($id) : $repo->newInstance();

        if($instance)
        {
            $instance->__sharp_duplication = $isSharpDuplication;

            // And return the View
            return view('sharp::cms.entityForm', [
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

        throw new InstanceNotFoundException("Instance of id [$id] and type [$embeddedCategoryKey.$embeddedEntityKey] can't be found");
    }


    /**
     * @param $masterCategoryKey
     * @param $masterEntityKey
     * @param $masterFieldKey
     * @param $embeddedCategoryKey
     * @param $embeddedEntityKey
     * @param Request $request
     * @param $id
     * @param bool $creation
     * @return mixed
     * @throws \Dvlpp\Sharp\Exceptions\EntityConfigurationNotFoundException
     */
    private function save($masterCategoryKey, $masterEntityKey, $masterFieldKey, $embeddedCategoryKey, $embeddedEntityKey, Request $request, $id, $creation=false)
    {
        $data = $request->all();

        // Find Entity config (from sharp CMS config file)
        $entity = SharpCmsConfig::findEntity($embeddedCategoryKey, $embeddedEntityKey);

        try {
            // First : validation
            if($entity->validator)
            {
                $validator = app($entity->validator);
                $validator->validate($data, !$creation?$id:null);
            }

            // Data is valid, we are going back to the master form. Let's restore the master form data...
            $masterInstanceData = sharp_decode_embedded_entity_data($data['masterInstanceData']);

            // ... add the embedded form data...
            $embeddedInstanceData = sharp_encode_embedded_entity_data(
                $request->except(["_method", "_token", "masterInstanceData", "masterInstanceId", "masterEntityLabel"])
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

            \Session::flash('masterInstanceData', serialize($masterInstanceData));

            // ... and redirect back to the master entity form
            return $this->redirectToMaster($masterCategoryKey, $masterEntityKey, $data['masterInstanceId']);
        }

        catch(ValidationException $e)
        {
            return redirect()->back()->withInput()->withErrors($e->getErrors());
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
            return redirect()->route('cms.edit', [$masterCategoryKey, $masterEntityKey, $masterInstanceId]);
        }

        return redirect()->route('cms.create', [$masterCategoryKey, $masterEntityKey]);
    }

} 