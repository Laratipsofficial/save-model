<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Fields\BooleanField;

class BooleanFieldTest extends TestCase
{
    /** @test */
    public function boolean_field_returns_true_for_the_given_truthy_fields()
    {
        $this->assertEquals(true, BooleanField::new()->setValue(true)->execute());
        $this->assertEquals(true, BooleanField::new()->setValue('true')->execute());
        $this->assertEquals(true, BooleanField::new()->setValue(1)->execute());
        $this->assertEquals(true, BooleanField::new()->setValue('1')->execute());
        $this->assertEquals(true, BooleanField::new()->setValue('on')->execute());
        $this->assertEquals(true, BooleanField::new()->setValue('yes')->execute());

        $this->assertEquals(false, BooleanField::new()->setValue(false)->execute());
        $this->assertEquals(false, BooleanField::new()->setValue('false')->execute());
        $this->assertEquals(false, BooleanField::new()->setValue(0)->execute());
        $this->assertEquals(false, BooleanField::new()->setValue('0')->execute());
        $this->assertEquals(false, BooleanField::new()->setValue('off')->execute());
        $this->assertEquals(false, BooleanField::new()->setValue(null)->execute());
        $this->assertEquals(false, BooleanField::new()->setValue('')->execute());
    }
}
