<?php

namespace Asdh\SaveModel\Tests\Models;

use Asdh\SaveModel\Contracts\CanBeSavedContract;
use Asdh\SaveModel\Database\Factories\UserFactory;
use Asdh\SaveModel\Fields\DatetimeField;
use Asdh\SaveModel\Fields\FileField;
use Asdh\SaveModel\Fields\PasswordField;
use Asdh\SaveModel\Fields\StringField;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model implements AuthorizableContract, AuthenticatableContract, CanBeSavedContract
{
    use Authorizable;
    use Authenticatable;
    use HasFactory;

    protected $guarded = [];

    protected $table = 'users';

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function saveableFields(): array
    {
        return [
            'name' => StringField::new(),
            'email' => StringField::new(),
            'email_verified_at' => DatetimeField::new(),
            'password' => PasswordField::new(),
            'image' => FileField::new(),
        ];
    }
}
