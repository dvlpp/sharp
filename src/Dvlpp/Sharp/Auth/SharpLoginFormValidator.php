<?php

namespace Dvlpp\Sharp\Auth;

use Dvlpp\Sharp\Validation\SharpValidator;

class SharpLoginFormValidator extends SharpValidator {

    protected $rules = [
        "login" => "required",
        "password" => "required"
    ];

} 