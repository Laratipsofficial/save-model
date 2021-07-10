<?php

namespace Asdh\SaveModel\Fields;

use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileField extends Field
{
    private ?string $folder = null;

    private ?string $disk = null;

    private ?Closure $fileNameClosure = null;

    private bool $deleteOldFileOnUpdate = true;

    public function execute(): mixed
    {
        if (!$this->value) {
            return $this->value;
        }

        if (!($this->value instanceof UploadedFile)) {
            return $this->value;
        }

        $this->deleteOldFileIfNecessary();

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

    public function dontDeleteOldFileOnUpdate(): self
    {
        $this->deleteOldFileOnUpdate = false;

        return $this;
    }

    private function diskName(): string
    {
        return $this->disk ?? config('filesystems.default');
    }

    private function folderName(): string
    {
        return $this->folder ?? config('save_model.file_upload_folder');
    }

    private function deleteOldFileIfNecessary(): void
    {
        $fileName = $this->model->getRawOriginal($this->column);

        if ($this->deleteOldFileOnUpdate && $this->isUpdateMode() && $fileName) {
            Storage::disk($this->diskName())->delete($fileName);
        }
    }
}
