<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait CanLoadRelationships
{

    public function lodeRelationships(Model|QueryBuilder|EloquentBuilder|HasMany $for, ?array $relations = null)
    : Model|QueryBuilder|EloquentBuilder|HasMany
    {
        $relations = $relations ?? $this->relations ?? [];

        foreach ($relations as $relation){
            $for->when(
                $this->shouldIncludeRelation($relation),
                fn($q)=> $for instanceof Model ? $for->load($relation) : $q->with($relation)
            );
        }

        return $for;
    }


    protected function shouldIncludeRelation(string $relation) : bool
    {
        $include = request()->query('include');

        if (! $include){
            return false;
        }

        //converting $include(string) to array
        //trim : deleted spaces
        //array_map : تمام فضاهای خالی برای هر عنصر از ارایه ایجاد شده را برش می دهد
        $relations = array_map('trim' , explode(',' , $include));

        return in_array($relation , $relations);
    }
}
