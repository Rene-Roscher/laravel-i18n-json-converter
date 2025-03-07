<?php

namespace ReneRoscher\LaravelI18nJsonConverter;

use ReneRoscher\LaravelI18nJsonConverter\Commands\LaravelI18nJsonConverterCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelI18nJsonConverterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-i18n-json-converter')
            ->hasCommand(LaravelI18nJsonConverterCommand::class);
    }
}
