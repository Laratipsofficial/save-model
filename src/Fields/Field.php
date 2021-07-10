<?php

namespace Asdh\SaveModel\Fields;

abstract class Field
{
    protected $value;

    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    public static function new(): static
    {
        return new static;
    }

    abstract public function execute(): mixed;
}
