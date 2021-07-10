<?php

namespace Asdh\SaveModel;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Asdh\SaveModel\SaveModel
 */
class SaveModelFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'save-model';
    }
}
