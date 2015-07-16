<?php namespace Dvlpp\Sharp\Validation;

use Dvlpp\Sharp\Exceptions\ValidationException;
use Validator;

/**
 * Class SharpValidator
 * @package Dvlpp\Sharp\Validation
 */
abstract class SharpValidator {
    /**
     * Shared rules
     * @var array
     */
    protected $rules = [];

    /**
     * Rules in update case only
     * @var array
     */
    protected $updateRules = [];

    /**
     * Rules in creation case only
     * @var array
     */
    protected $creationRules = [];

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var
     */
    private $instanceId;

    /**
     * @param $input
     * @param null $instanceId
     * @return bool
     * @throws \Dvlpp\Sharp\Exceptions\ValidationException
     */
    public function validate($input, $instanceId=null)
    {
        $this->instanceId = $instanceId;

        // Grab rules
        $rules = array_merge($this->getRules(),
            $this->getInstanceId() ? $this->getUpdateRules() : $this->getCreationRules()
        );

        // and validate
        $validation = Validator::make($input, $rules, $this->getMessages());

        if($validation->fails())
        {
            throw new ValidationException($validation->messages());
        }

        return true;
    }

    public function isCreation()
    {
        return $this->instanceId == null;
    }

    public function getInstanceId()
    {
        return $this->instanceId;
    }

    public function getUpdateRules()
    {
        return $this->updateRules;
    }

    public function getCreationRules()
    {
        return $this->creationRules;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function getMessages()
    {
        return $this->messages;
    }
} 