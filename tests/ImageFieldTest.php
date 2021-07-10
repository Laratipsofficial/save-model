<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Fields\ImageField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageFieldTest extends TestCase
{
    private $fakeImage;
    private $fakeDisk;

    private $defaultImagesFolder;

    public function setUp(): void
    {
        parent::setUp();

        $this->fakeDisk = 'local';

        Storage::fake($this->fakeDisk);

        $this->fakeImage = UploadedFile::fake()->image('test.jpg');
        $this->defaultImagesFolder = config('save_model.image_upload_folder');
    }

    /** @test */
    public function returns_null_as_output_when_value_is_not_instance_of_uploaded_file()
    {
        $output1 = ImageField::new()->setValue(null)->execute();
        $output2 = ImageField::new()->setValue('hello.jpg')->execute();

        $this->assertNull($output1);
        $this->assertEquals('hello.jpg', $output2);
    }

    /** @test */
    public function uploads_image_to_the_default_disk_when_disk_name_is_not_provided()
    {
        $output = ImageField::new()->setValue($this->fakeImage)->execute();

        Storage::disk($this->fakeDisk)->assertExists($output);
    }

    /** @test */
    public function uploads_image_to_the_given_disk_when_disk_name_is_provided()
    {
        Storage::fake('photos');

        $output = ImageField::new()->setValue($this->fakeImage)->setDisk('photos')->execute();

        Storage::disk('photos')->assertExists($output);
    }

    /** @test */
    public function stores_image_to_the_default_folder_when_folder_is_not_set()
    {
        $output = ImageField::new()->setValue($this->fakeImage)->execute();

        $this->assertStringContainsString($this->defaultImagesFolder, $output);
    }

    /** @test */
    public function stores_image_to_the_given_folder_when_folder_is_set()
    {
        $output = ImageField::new()->setValue($this->fakeImage)->setFolder('photos')->execute();

        $this->assertStringContainsString('photos', $output);
    }

    /** @test */
    public function stores_image_with_a_random_name_when_name_is_not_provided()
    {
        ImageField::new()->setValue($this->fakeImage)->execute();

        Storage::disk($this->fakeDisk)->assertMissing($this->defaultImagesFolder . '/' . 'test.jpg');
    }

    /** @test */
    public function stores_image_with_the_given_name_when_name_is_provided()
    {
        ImageField::new()->setValue($this->fakeImage)->setFileName(function (UploadedFile $uploadedFile) {
            return 'custom-name.jpg';
        })->execute();

        Storage::disk($this->fakeDisk)->assertExists($this->defaultImagesFolder . '/' . 'custom-name.jpg');
    }

    /** @test */
    public function stores_image_with_the_given_name_inside_given_folder_when_name_and_folders_are_provided()
    {
        ImageField::new()
            ->setValue($this->fakeImage)
            ->setFolder('photos')
            ->setFileName(function (UploadedFile $uploadedFile) {
                return 'custom-name.jpg';
            })
            ->execute();

        Storage::disk($this->fakeDisk)->assertExists('photos/custom-name.jpg');
    }
}
