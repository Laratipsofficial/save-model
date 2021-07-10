<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Fields\PasswordField;
use Illuminate\Support\Facades\Hash;

class PasswordFieldTest extends TestCase
{
    /** @test */
    public function nullable_password_field_returns_null_as_output()
    {
        $output = PasswordField::new()->setValue(null)->execute();

        $this->assertNull($output);
    }

    /** @test */
    public function non_nullable_password_field_returns_hashed_value_as_output()
    {
        $output = PasswordField::new()->setValue('hello&world')->execute();

        $this->assertTrue(Hash::check('hello&world', $output));
    }
}
