<?php

namespace Asdh\SaveModel\Commands;

use Illuminate\Console\Command;

class SaveModelCommand extends Command
{
    public $signature = 'save-model';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
