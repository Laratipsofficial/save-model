<?php

namespace Asdh\SaveModel\Fields;

class IntegerField extends Field
{
    public function execute(): mixed
    {
        return $this->value;
    }
}
