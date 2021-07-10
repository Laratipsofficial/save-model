<?php

namespace Asdh\SaveModel\Contracts;

interface CanBeSavedContract
{
    public function saveableFields(): array;
}
