# Save Model

Save Model is a Laravel package that allows you to save data in the database in a new way. No need to worry about `$guarded` and `$fillable` properties in the model anymore. Just relax an use `Save Model` package.

---

[![Latest Version on Packagist](https://img.shields.io/packagist/v/asdh/save-model.svg?style=flat-square)](https://packagist.org/packages/asdh/save-model)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/asdh/save-model/run-tests?label=tests)](https://github.com/asdh/save-model/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/asdh/save-model/Check%20&%20fix%20styling?label=code%20style)](https://github.com/asdh/save-model/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/asdh/save-model.svg?style=flat-square)](https://packagist.org/packages/asdh/save-model)

---

## Installation

You can install the package via composer:

```bash
composer require asdh/save-model
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Asdh\SaveModel\SaveModelServiceProvider"
```

This is the contents of the published config file:

```php
// config/save_model.php

return [
    /**
     * The directory name where the files should be stored
     * This can be changed via 'saveableFields' method on model
     */
    'file_upload_directory' => 'files',
];
```

## Usage

```php
// In controller

use Asdh\SaveModel\SaveModel;

SaveModel::new(
    new User,
    $request->only(['name', 'email', 'password', 'image'])
)->execute();

// OR

(new SaveModel(
    new User,
    $request->only(['name', 'email', 'password', 'image'])
)->execute();
```

You just do this and a new user will be created and saved to the 'users' table. The password will be automatically hashed, and uploading of the image will also be automatically handled.

To update a model, you just have to pass the model that you want to update.

```php
// In controller

use Asdh\SaveModel\SaveModel;

$user = User::find(1);

SaveModel::new(
    $user,
    $request->only(['name', 'email'])
)->execute();
```
Only name and email will be updated and no other columns will be touched.

**For this to work, you need to do these things:**

Go to User model class or any other model class and add `CanBeSavedContract` class to it. In this example, I will use User model.

```php
use Asdh\SaveModel\Contracts\CanBeSavedContract;

class User extends Authenticatable implements CanBeSavedContract
{

}
```

After adding this, you need to add `saveableFields` method to the User model and map every columns of the users table like so:

```php
use Asdh\SaveModel\Contracts\CanBeSavedContract;
use Asdh\SaveModel\Fields\DatetimeField;
use Asdh\SaveModel\Fields\FileField;
use Asdh\SaveModel\Fields\PasswordField;
use Asdh\SaveModel\Fields\StringField;

class User extends Authenticatable implements CanBeSavedContract
{
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
```

After doing this you are good to go. In the controller, you just need to get the data and use the `SaveModel` class

```php
use Asdh\SaveModel\SaveModel;

SaveModel::new(
    new User,
    $request->only(['name', 'email', 'password', 'image'])
)->execute();

// OR

(new SaveModel(
    new User,
    $request->only(['name', 'email', 'password', 'image'])
)->execute();
```

The files will be uploaded using the default `Laravel's filesystem`. Which means that you can directly configure to upload the files directly to the `S3` as well or any other that Laravel supports.

Also, the files will be uploaded to the `files` directory by default. You can change that globally by changing the value of `file_upload_directory` on the `save_model.php` configuration file.

You can also change it per model like so:

```php
// app/Models/User.php

public function saveableFields(): array
{
    return [
        'image' => FileField::new()->setDirectory('images'),
    ];
}
```

It will now store the `image` of the user to the `images` directory and for every other `Models`, it will use from the `save_model.php` config file.

You can also, choose the Laravel Filesystem's `disk` per model like so:

```php
// app/Models/User.php

public function saveableFields(): array
{
    return [
        'image' => FileField::new()
            ->setDirectory('images')
            ->setDisk('s3'),
    ];
}
```

By default `random name` will be generated for the uploaded files, but you can change that also. You just have to pass closure on the `setFileName` method and you will get access to the uploaded file there. And whatever you return from here will be saved to the database as the file name.

This example shows how to return the original file name.

```php
// app/Models/User.php

use Illuminate\Http\UploadedFile;

public function saveableFields(): array
{
    return [
        'image' => FileField::new()
            ->setDirectory('images')
            ->setFileName(function (UploadedFile $uploadedFile) {
                return $uploadedFile->getClientOriginalName();
            }),
    ];
}
```

Not only this, the deletion of the file will also be automatically handled when updating a model. By default, when a model is updated, the old file will be automatically deleted if a new file is being uploaded. If you don't want the old images to be deleted then you can chain `dontDeleteOldFileOnUpdate` method.

```php
// app/Models/User.php

use Illuminate\Http\UploadedFile;

public function saveableFields(): array
{
    return [
        'image' => FileField::new()
            ->setDirectory('images')
            ->dontDeleteOldFileOnUpdate(),
    ];
}
```

## Available Fields
```php
Asdh\SaveModel\Fields\StringField::class
Asdh\SaveModel\Fields\IntegerField::class
Asdh\SaveModel\Fields\DatetimeField::class
Asdh\SaveModel\Fields\DateField::class
Asdh\SaveModel\Fields\TimeField::class
Asdh\SaveModel\Fields\PasswordField::class
Asdh\SaveModel\Fields\FileField::class
Asdh\SaveModel\Fields\BooleanField::class
```

Other field will be added in the future and I am open to pull requests.

## Creating your own model field class

You can create your own field class as well. To create one, you need to run an artisan command

```bash
php artisan make:field BooleanField
```

This will create a `BooleanField` class inside `App\ModelFields` directory and it will look like this:

```php
<?php

namespace App\ModelFields;

use Asdh\SaveModel\Fields\Field;

class BooleanField extends Field
{
    public function execute(): mixed
    {
        // Perform your logic and return the value...

        // return strtoupper($this->value)
    }
}
```

It is not necessary that `BooleanField` class must be inside `App\ModelFields` directory. You can place it wherever you like.

You will have access to the data passed from the controller as `$this->value`. Then you can do whatever you want to do and return the value that you want to save in the database. In above case, we can do it like so:

```php
<?php

namespace App\ModelFields;

use Asdh\SaveModel\Fields\Field;

class BooleanField extends Field
{
    public function execute(): mixed
    {
        return in_array($this->value, [1, '1', true, 'true', 'on', 'yes']);
    }
}

```
If the input is any one of these, then we will consider it to be true and for every one of these values, we will save `true` (which will be `1` when stored in database) to the database.

Then you can easily use your own field in the model's `saveableFields` method. You can now use this `BooleanField` along with other fields like this:

```php
use Asdh\SaveModel\Contracts\CanBeSavedContract;
use Asdh\SaveModel\Fields\DatetimeField;
use Asdh\SaveModel\Fields\FileField;
use Asdh\SaveModel\Fields\PasswordField;
use Asdh\SaveModel\Fields\StringField;
use App\ModelFields\BooleanField;

class User extends Authenticatable implements CanBeSavedContract
{
    public function saveableFields(): array
    {
        return [
            'name' => StringField::new(),
            'email' => StringField::new(),
            'email_verified_at' => DatetimeField::new(),
            'password' => PasswordField::new(),
            'image' => FileField::new(),
            'is_admin' => Boolean::new(),
        ];
    }
}

```
Make sure you add the namespace correctly as shown above.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
