<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Fields\StringField;

class StringFieldTest extends TestCase
{
    /** @test */
    public function string_field_returns_whatever_is_input()
    {
        $input = 'apple';
        $output = StringField::new()->setValue($input)->execute();

        $this->assertEquals('apple', $output);
    }
}
