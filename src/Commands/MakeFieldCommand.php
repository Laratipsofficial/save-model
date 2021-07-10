<?php

namespace Asdh\SaveModel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeFieldCommand extends Command
{
    public $signature = 'make:field {field}';

    public $description = 'Create a new model field class';

    private string $field;

    private string $fieldClass;

    public function handle()
    {
        $this->field = (string) Str::of($this->argument('field'))->trim()->replace('\\', '/');

        $fieldName = Str::afterLast($this->field, '/');

        if (! Str::slug($fieldName)) {
            $this->error('Invalid field name.');

            return;
        }

        $this->fieldClass = Str::studly($fieldName);

        (new Filesystem())->ensureDirectoryExists(app_path($this->destinationDirectory()));

        if ($this->classAlreadyExists()) {
            $this->error("{$this->fieldClass} already exists!");

            return;
        }

        $this->generateClass();

        $this->comment('Model field created successfully.');
    }

    private function destinationDirectory(): string
    {
        $directory = 'ModelFields';

        if (str_contains($this->field, '/')) {
            $directory .= '/' . Str::beforeLast($this->field, '/');
        }

        return $directory;
    }

    private function destination(): string
    {
        return app_path("{$this->destinationDirectory()}/{$this->fieldClass}.php");
    }

    private function source(): string
    {
        return __DIR__ . '/../../resources/stubs/field.php.stub';
    }

    private function classAlreadyExists(): bool
    {
        return (new Filesystem())->exists($this->destination());
    }

    private function generateClass(): void
    {
        (new Filesystem())->put(
            $this->destination(),
            str_replace([
                '{{FieldClass}}',
                '{{namespace}}',
            ], [
                $this->fieldClass,
                $this->namespace(),
            ], file_get_contents($this->source()))
        );
    }

    private function namespace(): string
    {
        return 'App\\' . str_replace('/', '\\', $this->destinationDirectory());
    }
}
