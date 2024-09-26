<?php

namespace Litvinjuan\LaravelCascadeSoftDeletes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

trait CascadeSoftDeletes
{
    use SoftDeletes;

    public function getRelationsForCascadeSoftDeletes(): array
    {
        if (!property_exists($this, 'cascadeSofDeleteRelations')) {
            return [];
        }
        return $this->cascadeSofDeleteRelations;
    }

    public function getRelationsForCascadeRestore(): array
    {
        if (!property_exists($this, 'cascadeRestoreRelations')) {
            return $this->getRelationsForCascadeSoftDeletes();
        }
        return $this->cascadeRestoreRelations;
    }

    public static function bootCascadeSoftDeletes()
    {
        static::deleted(function (Model $model) {
            foreach ($model->getRelationsForCascadeSoftDeletes() as $relationName) {
                if ($model->isRelation($relationName)) {
                    $relation = $model->{$relationName}();

                    $relatedModels = $relation->get();

                    foreach ($relatedModels as $relatedModel) {
                        if ($relatedModel instanceof Model) {
                            $relatedModel->delete();
                        }
                    }
                }
            }
        });

        if (config('cascade-soft-deletes.cascade_restores')) {
            static::restoring(function ($model) {
                $modelDeletedAt = $model->{$model->getDeletedAtColumn()};

                foreach ($model->getRelationsForCascadeRestore() as $relationName) {
                    if ($model->isRelation($relationName) && method_exists($model, $relationName)) {
                        $relation = $model->{$relationName}();
                        if (!$relation instanceof Relation) {
                            continue;
                        }

                        $relatedModel = $relation->getRelated();
                        $deletedAtColumn = $relatedModel->getQualifiedDeletedAtColumn();

                        $restorableModelsQuery = $relation->onlyTrashed();

                        if (!config('cascade-soft-deletes.ignore_deleted_at_when_restoring')) {
                            $restorableModelsQuery->where($deletedAtColumn, '>=', $modelDeletedAt);
                        }

                        $restorableModels = $restorableModelsQuery->get();

                        foreach ($restorableModels as $restorableModel) {
                            $restorableModel->restore();
                        }
                    }
                }
            });
        }
    }
}
