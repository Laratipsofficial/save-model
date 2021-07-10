<?php

namespace Asdh\SaveModel\Fields;

use Closure;
use Illuminate\Http\UploadedFile;

class ImageField extends Field
{
    private ?string $folder = null;

    private ?string $disk = null;

    private ?Closure $fileNameClosure = null;

    public function execute(): mixed
    {
        if (!$this->value) {
            return $this->value;
        }

        if (!($this->value instanceof UploadedFile)) {
            return $this->value;
        }

        if (!$this->fileNameClosure) {
            return $this->value->store($this->folderName(), $this->diskName());
        }

        $fileName = ($this->fileNameClosure)($this->value);

        return $this->value->storeAs($this->folderName(), $fileName, $this->diskName());
    }

    public function setFolder(string $folder): self
    {
        $this->folder = $folder;

        return $this;
    }

    public function setDisk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    public function setFileName(Closure $closure): self
    {
        $this->fileNameClosure = $closure;

        return $this;
    }

    private function diskName(): string
    {
        return $this->disk ?? config('filesystems.default');
    }

    private function folderName(): string
    {
        return $this->folder ?? config('save_model.image_upload_folder');
    }
}
