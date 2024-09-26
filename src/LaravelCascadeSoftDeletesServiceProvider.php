<?php

namespace Litvinjuan\LaravelCascadeSoftDeletes;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCascadeSoftDeletesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-cascade-soft-deletes')
            ->hasConfigFile();
    }
}
