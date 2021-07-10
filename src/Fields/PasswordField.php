<?php

namespace Asdh\SaveModel\Fields;

use Illuminate\Support\Facades\Hash;

class PasswordField extends Field
{
    public function execute(): mixed
    {
        if (!$this->value) {
            return $this->value;
        }

        return Hash::make($this->value);
    }
}
