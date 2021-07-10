<?php

namespace Asdh\SaveModel\Fields;

use Carbon\Carbon;

class DatetimeField extends Field
{
    public function execute(): mixed
    {
        if (!$this->value) {
            return $this->value;
        }

        return Carbon::parse($this->value)->toDateTimeString();
    }
}
