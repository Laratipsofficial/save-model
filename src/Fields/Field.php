<?php

namespace Asdh\SaveModel\Fields;

use Illuminate\Database\Eloquent\Model;

abstract class Field
{
    protected mixed $value;

    protected string $column;

    protected Model $model;

    abstract public function execute(): mixed;

    public static function new(): static
    {
        return new static;
    }

    public function setValue($value): static
    {
        $this->value = $value;

        return $this;
    }

    public function onColumn(string $column): static
    {
        $this->column = $column;

        return $this;
    }

    public function ofModel(Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function isUpdateMode(): bool
    {
        return $this->model->exists;
    }
}
