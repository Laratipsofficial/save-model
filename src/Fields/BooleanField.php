<?php

namespace Asdh\SaveModel\Fields;

class BooleanField extends Field
{
    public function execute(): mixed
    {
        return in_array($this->value, [true, 'true', 1, '1', 'on', 'yes'], true);
    }
}
