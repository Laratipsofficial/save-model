<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Fields\FileField;
use Asdh\SaveModel\Tests\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileFieldTest extends TestCase
{
    private UploadedFile $fakeFile;

    private string $fakeDisk;

    private string $defaultFilesFolder;

    private FileField $fileField;

    public function setUp(): void
    {
        parent::setUp();

        $this->fakeDisk = 'local';

        Storage::fake($this->fakeDisk);

        $this->fakeFile = UploadedFile::fake()->image('test.jpg');
        $this->defaultFilesFolder = config('save_model.file_upload_folder');
        $this->fileField = FileField::new()->onColumn('image')->ofModel(new User());
    }

    /** @test */
    public function returns_the_passed_value_as_output_when_value_is_not_instance_of_uploaded_file()
    {
        $output1 = $this->fileField->setValue(null)->execute();
        $output2 = $this->fileField->setValue('hello.jpg')->execute();

        $this->assertNull($output1);
        $this->assertEquals('hello.jpg', $output2);
    }

    /** @test */
    public function uploads_file_to_the_default_disk_when_disk_name_is_not_provided()
    {
        $output = $this->fileField->setValue($this->fakeFile)->execute();

        Storage::disk($this->fakeDisk)->assertExists($output);
    }

    /** @test */
    public function uploads_file_to_the_given_disk_when_disk_name_is_provided()
    {
        Storage::fake('photos');

        $output = $this->fileField->setValue($this->fakeFile)->setDisk('photos')->execute();

        Storage::disk('photos')->assertExists($output);
    }

    /** @test */
    public function stores_file_to_the_default_folder_when_folder_is_not_set()
    {
        $output = $this->fileField->setValue($this->fakeFile)->execute();

        $this->assertStringContainsString($this->defaultFilesFolder, $output);
    }

    /** @test */
    public function stores_file_to_the_given_folder_when_folder_is_set()
    {
        $output = $this->fileField->setValue($this->fakeFile)->setFolder('photos')->execute();

        $this->assertStringContainsString('photos', $output);
    }

    /** @test */
    public function stores_file_with_a_random_name_when_name_is_not_provided()
    {
        $this->fileField->setValue($this->fakeFile)->execute();

        Storage::disk($this->fakeDisk)->assertMissing($this->defaultFilesFolder . '/' . 'test.jpg');
    }

    /** @test */
    public function stores_file_with_the_given_name_when_name_is_provided()
    {
        $this->fileField
            ->setValue($this->fakeFile)
            ->setFileName(function (UploadedFile $uploadedFile) {
                return 'custom-name.jpg';
            })
            ->execute();

        Storage::disk($this->fakeDisk)->assertExists($this->defaultFilesFolder . '/' . 'custom-name.jpg');
    }

    /** @test */
    public function stores_file_with_the_given_name_inside_given_folder_when_name_and_folders_are_provided()
    {
        $this->fileField
            ->setValue($this->fakeFile)
            ->setFolder('photos')
            ->setFileName(function (UploadedFile $uploadedFile) {
                return 'custom-name.jpg';
            })
            ->execute();

        Storage::disk($this->fakeDisk)->assertExists('photos/custom-name.jpg');
    }

    /** @test */
    public function deletes_old_file_if_new_file_is_passed_when_updating()
    {
        $oldFileName = UploadedFile::fake()->image('old-file.jpg')->store($this->defaultFilesFolder, $this->fakeDisk);

        Storage::disk($this->fakeDisk)->assertExists($oldFileName);

        $user = User::factory()->create([
            'image' => $oldFileName,
        ]);

        $this->fileField->setValue($this->fakeFile)->ofModel($user)->execute();

        Storage::disk($this->fakeDisk)->assertMissing($oldFileName);
    }

    /** @test */
    public function does_not_delete_old_file_if_null_is_passed_when_updating()
    {
        $oldFileName = UploadedFile::fake()->image('old-file.jpg')->store($this->defaultFilesFolder, $this->fakeDisk);

        Storage::disk($this->fakeDisk)->assertExists($oldFileName);

        $user = User::factory()->create([
            'image' => $oldFileName,
        ]);

        $this->fileField->setValue(null)->ofModel($user)->execute();

        Storage::disk($this->fakeDisk)->assertExists($oldFileName);
    }

    /** @test */
    public function does_not_delete_old_file_if_string_is_passed_as_value_when_updating()
    {
        $oldFileName = UploadedFile::fake()->image('old-file.jpg')->store($this->defaultFilesFolder, $this->fakeDisk);

        Storage::disk($this->fakeDisk)->assertExists($oldFileName);

        $user = User::factory()->create([
            'image' => $oldFileName,
        ]);

        $this->fileField->setValue('old-file.jpg')->ofModel($user)->execute();

        Storage::disk($this->fakeDisk)->assertExists($oldFileName);
    }
}
