<?php

namespace Dvlpp\Sharp\Commands;

use Illuminate\Http\Request;
use Validator;
use Dvlpp\Sharp\Exceptions\ValidationException;

trait CommandValidateTrait
{
    protected function rules() {
        return [];
    }

    protected function messages() {
        return [];
    }

    public function validate(Request $request) {
        $validation = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validation->fails()) {
            throw new ValidationException($validation->messages());
        }

        return true;
    }
}