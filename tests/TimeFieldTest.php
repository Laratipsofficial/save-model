<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Fields\TimeField;

class TimeFieldTest extends TestCase
{
    /** @test */
    public function nullable_time_field_returns_null_as_output()
    {
        $output = TimeField::new()->setValue(null)->execute();

        $this->assertNull($output);
    }

    /** @test */
    public function non_nullable_datetime_field_returns_properly_formatted_datetime_output()
    {
        $output1 = TimeField::new()->setValue('2021-06-10')->execute();
        $output2 = TimeField::new()->setValue('2021/06/10 23:12:22')->execute();

        $this->assertEquals('00:00:00', $output1);
        $this->assertEquals('23:12:22', $output2);
    }
}
