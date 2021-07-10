<?php

namespace Asdh\SaveModel;

use Asdh\SaveModel\Contracts\CanBeSavedContract;
use Asdh\SaveModel\Exceptions\FieldDoesNotExistException;
use Asdh\SaveModel\Fields\Field;
use Exception;
use Illuminate\Database\Eloquent\Model;

class SaveModel
{
    public function __construct(private Model $model, private array $data)
    {
        $modelClassName = $model::class;

        if (!($model instanceof CanBeSavedContract)) {
            throw new Exception("The {$modelClassName} must implement " . CanBeSavedInterface::class);
        }

        foreach ($data as $column => $value) {
            if (!$this->saveableFieldExists($column)) {
                throw new FieldDoesNotExistException("The field '{$column}' does not exist on the 'saveableFields' method of {$modelClassName}");
            }
        }
    }

    public static function new(Model $model, array $data): static
    {
        return new static($model, $data);
    }

    private function saveableFieldExists(string $column): bool
    {
        return array_key_exists($column, $this->model->saveableFields());
    }

    public function execute(): Model
    {
        foreach ($this->data as $column => $value) {
            $this->model->{$column} = $this
                ->saveableField($column)
                ->setValue($value)
                ->onColumn($column)
                ->ofModel($this->model)
                ->execute();
        }

        $this->model->save();

        return $this->model;
    }

    private function saveableField($column): Field
    {
        return $this->model->saveableFields()[$column];
    }
}
