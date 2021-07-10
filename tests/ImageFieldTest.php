<?php

namespace Asdh\SaveModel\Tests;

use Asdh\SaveModel\Fields\ImageField;
use Asdh\SaveModel\Tests\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageFieldTest extends TestCase
{
    private UploadedFile $fakeImage;

    private string $fakeDisk;

    private string $defaultImagesFolder;

    private ImageField $imageField;

    public function setUp(): void
    {
        parent::setUp();

        $this->fakeDisk = 'local';

        Storage::fake($this->fakeDisk);

        $this->fakeImage = UploadedFile::fake()->image('test.jpg');
        $this->defaultImagesFolder = config('save_model.image_upload_folder');
        $this->imageField = ImageField::new()->onColumn('image')->ofModel(new User());
    }

    /** @test */
    public function returns_the_passed_value_as_output_when_value_is_not_instance_of_uploaded_file()
    {
        $output1 = $this->imageField->setValue(null)->execute();
        $output2 = $this->imageField->setValue('hello.jpg')->execute();

        $this->assertNull($output1);
        $this->assertEquals('hello.jpg', $output2);
    }

    /** @test */
    public function uploads_image_to_the_default_disk_when_disk_name_is_not_provided()
    {
        $output = $this->imageField->setValue($this->fakeImage)->execute();

        Storage::disk($this->fakeDisk)->assertExists($output);
    }

    /** @test */
    public function uploads_image_to_the_given_disk_when_disk_name_is_provided()
    {
        Storage::fake('photos');

        $output = $this->imageField->setValue($this->fakeImage)->setDisk('photos')->execute();

        Storage::disk('photos')->assertExists($output);
    }

    /** @test */
    public function stores_image_to_the_default_folder_when_folder_is_not_set()
    {
        $output = $this->imageField->setValue($this->fakeImage)->execute();

        $this->assertStringContainsString($this->defaultImagesFolder, $output);
    }

    /** @test */
    public function stores_image_to_the_given_folder_when_folder_is_set()
    {
        $output = $this->imageField->setValue($this->fakeImage)->setFolder('photos')->execute();

        $this->assertStringContainsString('photos', $output);
    }

    /** @test */
    public function stores_image_with_a_random_name_when_name_is_not_provided()
    {
        $this->imageField->setValue($this->fakeImage)->execute();

        Storage::disk($this->fakeDisk)->assertMissing($this->defaultImagesFolder . '/' . 'test.jpg');
    }

    /** @test */
    public function stores_image_with_the_given_name_when_name_is_provided()
    {
        $this->imageField
            ->setValue($this->fakeImage)
            ->setFileName(function (UploadedFile $uploadedFile) {
                return 'custom-name.jpg';
            })
            ->execute();

        Storage::disk($this->fakeDisk)->assertExists($this->defaultImagesFolder . '/' . 'custom-name.jpg');
    }

    /** @test */
    public function stores_image_with_the_given_name_inside_given_folder_when_name_and_folders_are_provided()
    {
        $this->imageField
            ->setValue($this->fakeImage)
            ->setFolder('photos')
            ->setFileName(function (UploadedFile $uploadedFile) {
                return 'custom-name.jpg';
            })
            ->execute();

        Storage::disk($this->fakeDisk)->assertExists('photos/custom-name.jpg');
    }

    /** @test */
    public function deletes_old_image_if_new_image_is_passed_when_updating()
    {
        $oldImageName = UploadedFile::fake()->image('old-image.jpg')->store($this->defaultImagesFolder, $this->fakeDisk);

        Storage::disk($this->fakeDisk)->assertExists($oldImageName);

        $user = User::factory()->create([
            'image' => $oldImageName
        ]);

        $this->imageField->setValue($this->fakeImage)->ofModel($user)->execute();

        Storage::disk($this->fakeDisk)->assertMissing($oldImageName);
    }

    /** @test */
    public function does_not_delete_old_image_if_null_is_passed_when_updating()
    {
        $oldImageName = UploadedFile::fake()->image('old-image.jpg')->store($this->defaultImagesFolder, $this->fakeDisk);

        Storage::disk($this->fakeDisk)->assertExists($oldImageName);

        $user = User::factory()->create([
            'image' => $oldImageName
        ]);

        $this->imageField->setValue(null)->ofModel($user)->execute();

        Storage::disk($this->fakeDisk)->assertExists($oldImageName);
    }

    /** @test */
    public function does_not_delete_old_image_if_string_is_passed_as_value_when_updating()
    {
        $oldImageName = UploadedFile::fake()->image('old-image.jpg')->store($this->defaultImagesFolder, $this->fakeDisk);

        Storage::disk($this->fakeDisk)->assertExists($oldImageName);

        $user = User::factory()->create([
            'image' => $oldImageName
        ]);

        $this->imageField->setValue('old-image.jpg')->ofModel($user)->execute();

        Storage::disk($this->fakeDisk)->assertExists($oldImageName);
    }
}
