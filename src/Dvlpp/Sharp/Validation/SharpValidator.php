<?php namespace Dvlpp\Sharp\Validation;


use Dvlpp\Sharp\Exceptions\ValidationException;
use Validator;

abstract class SharpValidator {
    protected $rules = [];
    protected $messages = [];
    private $base_messages = [];

    public function validate($input)
    {
        $validation = Validator::make($input, $this->rules, array_merge($this->base_messages, $this->messages));

        if($validation->fails()) throw new ValidationException($validation->messages());

        return true;
    }
} 