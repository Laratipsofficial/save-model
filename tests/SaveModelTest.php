<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Exceptions\FieldDoesNotExistException;
use Asdh\SaveModel\SaveModel;
use Asdh\SaveModel\Tests\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SaveModelTest extends TestCase
{
    /** @test */
    public function model_can_be_created()
    {
        Storage::fake('local');

        $user = SaveModel::new(new User(), [
            'name' => 'User name',
            'email' => 'user@gmail.com',
            'password' => 'p@ssword@123',
            'email_verified_at' => '2021/07/10',
            'image' => UploadedFile::fake()->image('test.jpg'),
        ])->execute();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'name' => 'User name',
            'email' => 'user@gmail.com',
            'email_verified_at' => '2021-07-10 00:00:00',
            'image' => $user->image,
        ]);
    }

    /** @test */
    public function only_passed_data_is_saved()
    {
        (new SaveModel(new User(), [
            'name' => 'User name',
            'email' => 'user@gmail.com',
            'password' => 'p@ssword@123',
        ]))->execute();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'name' => 'User name',
            'email' => 'user@gmail.com',
        ]);

        $user = User::first();
        $this->assertNull($user->email_verified_at);
        $this->assertNull($user->image);
    }

    /** @test */
    public function throws_exception_if_a_field_is_not_present_on_the_saveable_field_method_in_model()
    {
        $this->expectException(FieldDoesNotExistException::class);
        $this->expectExceptionMessage("The field 'role' does not exist on the 'saveableFields' method of " . User::class);

        (new SaveModel(new User(), [
            'name' => 'User name',
            'email' => 'user@gmail.com',
            'role' => 'admin',
        ]))->execute();
    }
}
