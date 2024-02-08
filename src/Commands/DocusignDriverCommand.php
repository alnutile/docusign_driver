<?php

namespace AlNutile\DocusignDriver\Commands;

use Illuminate\Console\Command;

class DocusignDriverCommand extends Command
{
    public $signature = 'docusigndriver';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
