# Cascades soft delete and restore operations in Laravel Models

[![Latest Version on Packagist](https://img.shields.io/packagist/v/litvinjuan/laravel-cascade-soft-deletes.svg?style=flat-square)](https://packagist.org/packages/litvinjuan/laravel-cascade-soft-deletes)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/litvinjuan/laravel-cascade-soft-deletes/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/litvinjuan/laravel-cascade-soft-deletes/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/litvinjuan/laravel-cascade-soft-deletes/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/litvinjuan/laravel-cascade-soft-deletes/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/litvinjuan/laravel-cascade-soft-deletes.svg?style=flat-square)](https://packagist.org/packages/litvinjuan/laravel-cascade-soft-deletes)

This package is for cascading soft delete and restore operations on your related models. When a model gets soft deleted, the configured related models get soft deleted as well. When the original model is restored, its soft-deleted related models algo get restored.

## Installation

You can install the package via composer:

```bash
composer require litvinjuan/laravel-cascade-soft-deletes
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-cascade-soft-deletes-config"
```

## Soft Deleting

Simply add the trait to your models and configure the cascading relations either via the `cascadeSofDeleteRelations` property or the `getRelationsForCascadeSoftDeletes` method.

If your cascading related model also has the `CascadeSoftDeletes` trait, its related models will also be soft-deleted.

In the following example, soft deleting a project, will also soft delete its tasks. And soft deleting a team, will soft delete its projects, and in turn, all their respective tasks will also be soft deleted.

```php
class Team extends Model
{
    use CascadeSoftDeletes;
    
    protected $cascadeSofDeleteRelations = ['projects'];

    public function projects() {
        return $this->hasMany(Project::class);
    }
}

class Project extends Model
{
    use CascadeSoftDeletes;
    
    protected $cascadeSofDeleteRelations = ['tasks'];
    
    public function team() {
        return $this->belongsTo(Team::class);
    }
    
    public function task() {
        return $this->hasMany(Task::class);
    }
}

class Task extends Model
{
    public function project() {
        return $this->belongsTo(Project::class);
    }
}
```

## Restoring

Restoration works very similarly. By default, it restores the same relations that were soft deleted, but you can customize the relations to be restored by setting the `cascadeRestoreRelations` or implementing the `getRelationsForCascadeRestore`. 

If you want to disable restoration all together, go into the `cascade-soft-deletes` config and set `cascade_restores` to `false`.

By default, when restoring related models, the package will only restore models that were deleted at the same time or after the parent model. This is to make sure we don't restore a child model that was soft deleted individually in a previous time. If you want to restore all models regardless of when they were soft deleted, you can go into the configuration file and set `ignore_deleted_at_when_restoring` to `true`.


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Juan Litvin](https://github.com/litvinjuan)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
