<?php

namespace AlNutile\DocusignDriver;

use AlNutile\DocusignDriver\Commands\DocusignDriverCommand;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DocusignDriverServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('docusigndriver')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('api')
            ->hasMigration('create_docusigndriver_table')
            ->hasCommand(DocusignDriverCommand::class);
    }
}
