<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Fields\FileField;
use Asdh\SaveModel\Tests\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileFieldTest extends TestCase
{
    private UploadedFile $fakeFile;

    private string $fakeFileName = 'test image.jpg';

    private string $fakeDisk;

    private string $defaultFilesDirectory;

    private FileField $fileField;

    public function setUp(): void
    {
        parent::setUp();

        $this->fakeDisk = 'local';

        Storage::fake($this->fakeDisk);

        $this->fakeFile = UploadedFile::fake()->image($this->fakeFileName);
        $this->defaultFilesDirectory = config('save_model.file_upload_directory');
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
    public function stores_file_to_the_default_directory_when_directory_is_not_set()
    {
        $output = $this->fileField->setValue($this->fakeFile)->execute();

        $this->assertStringContainsString($this->defaultFilesDirectory, $output);
    }

    /** @test */
    public function stores_file_to_the_given_directory_when_directory_is_set()
    {
        $output = $this->fileField->setValue($this->fakeFile)->setDirectory('photos')->execute();

        $this->assertStringContainsString('photos', $output);
    }

    /** @test */
    public function stores_file_with_the_original_name()
    {
        $this->fileField->setValue($this->fakeFile)->uploadAsOriginalName()->execute();

        Storage::disk($this->fakeDisk)->assertExists($this->defaultFilesDirectory . '/' . $this->fakeFileName);
    }

    /** @test */
    public function stores_file_with_a_random_name_when_name_is_not_provided()
    {
        $this->fileField->setValue($this->fakeFile)->execute();

        Storage::disk($this->fakeDisk)->assertMissing($this->defaultFilesDirectory . '/' . $this->fakeFileName);
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

        Storage::disk($this->fakeDisk)->assertExists($this->defaultFilesDirectory . '/' . 'custom-name.jpg');
    }

    /** @test */
    public function set_file_name_method_takes_precedence_over_upload_as_original_name_if_both_methods_are_used()
    {
        $this->fileField
            ->setValue($this->fakeFile)
            ->uploadAsOriginalName()
            ->setFileName(function (UploadedFile $uploadedFile) {
                return 'custom-name.jpg';
            })
            ->execute();

        Storage::disk($this->fakeDisk)->assertExists($this->defaultFilesDirectory . '/' . 'custom-name.jpg');
        Storage::disk($this->fakeDisk)->assertMissing($this->defaultFilesDirectory . '/' . $this->fakeFileName);
    }

    /** @test */
    public function stores_file_with_the_given_name_inside_given_directory_when_name_and_directorys_are_provided()
    {
        $this->fileField
            ->setValue($this->fakeFile)
            ->setDirectory('photos')
            ->setFileName(function (UploadedFile $uploadedFile) {
                return 'custom-name.jpg';
            })
            ->execute();

        Storage::disk($this->fakeDisk)->assertExists('photos/custom-name.jpg');
    }

    /** @test */
    public function deletes_old_file_if_new_file_is_passed_when_updating()
    {
        $oldFileName = UploadedFile::fake()->image('old-file.jpg')->store($this->defaultFilesDirectory, $this->fakeDisk);

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
        $oldFileName = UploadedFile::fake()->image('old-file.jpg')->store($this->defaultFilesDirectory, $this->fakeDisk);

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
        $oldFileName = UploadedFile::fake()->image('old-file.jpg')->store($this->defaultFilesDirectory, $this->fakeDisk);

        Storage::disk($this->fakeDisk)->assertExists($oldFileName);

        $user = User::factory()->create([
            'image' => $oldFileName,
        ]);

        $this->fileField->setValue('old-file.jpg')->ofModel($user)->execute();

        Storage::disk($this->fakeDisk)->assertExists($oldFileName);
    }
}
