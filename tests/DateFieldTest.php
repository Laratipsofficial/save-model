<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Fields\DateField;

class DateFieldTest extends TestCase
{
    /** @test */
    public function nullable_date_field_returns_null_as_output()
    {
        $output = DateField::new()->setValue(null)->execute();

        $this->assertNull($output);
    }

    /** @test */
    public function non_nullable_datetime_field_returns_properly_formatted_datetime_output()
    {
        $output1 = DateField::new()->setValue('2021-06-10')->execute();
        $output2 = DateField::new()->setValue('2021/06/10 23:12:22')->execute();

        $this->assertEquals('2021-06-10', $output1);
        $this->assertEquals('2021-06-10', $output2);
    }
}
