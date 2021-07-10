<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Fields\IntegerField;

class IntegerFieldTest extends TestCase
{
    /** @test */
    public function integer_field_returns_whatever_is_input()
    {
        $input = 'apple';
        $output = IntegerField::new()->setValue($input)->execute();

        $this->assertEquals('apple', $output);
    }
}
