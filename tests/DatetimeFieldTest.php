<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Fields\DatetimeField;

class DatetimeFieldTest extends TestCase
{
    /** @test */
    public function nullable_datetime_field_returns_null_as_output()
    {
        $output = DatetimeField::new()->setValue(null)->execute();

        $this->assertNull($output);
    }

    /** @test */
    public function non_nullable_datetime_field_returns_properly_formatted_datetime_output()
    {
        $output1 = DatetimeField::new()->setValue('2021-06-10')->execute();
        $output2 = DatetimeField::new()->setValue('2021/06/10 23:12:22')->execute();

        $this->assertEquals('2021-06-10 00:00:00', $output1);
        $this->assertEquals('2021-06-10 23:12:22', $output2);
    }
}
