<?php

namespace Asdh\SaveModel\Commands;

use Illuminate\Console\Command;

class SaveModelConfigPublishCommand extends Command
{
    public $signature = 'save-model:publish';

    public $description = 'Publish save model config file';

    public function handle()
    {
        if (file_exists(config_path('save_model.php'))) {
            $this->error('save_model.php is already exist. config file publish failed.!');
            $answer = $this->ask('Are you sure you want to replace the save_model config file ? [y/N]', 'N');
            if ($answer == 'y' || $answer == 'Y') {
                copy(__DIR__ . '/../../config/save_model.php', config_path('save_model.php'));
                $this->info('Config file re-published successfully.!');
            }
            return;
        }
        $this->call('vendor:publish', ['--tag' => "savemodel-config"]);
    }
}
