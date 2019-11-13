<?php
namespace Langyi\Performance\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class AbstractBaseModel extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->connection = config('performance.connection');
        parent::__construct($attributes);
    }
    
    public static function getTableName(): string
    {
        return Str::snake(Str::pluralStudly(class_basename(static::class)));
    }
}
