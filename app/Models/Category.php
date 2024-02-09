<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;


class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name', 'parent_id', 'description', 'image', 'status', 'slug'
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id')
            ->withDefault([
                'name' => '-'
                //في حاله انه م فيش relation =>null
                // <td>{{ $category->parent?? $category->parent->name  : '' }}</td>
            ]);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function scopeFilter(Builder $builder, $filters)
    {
        if ($filters['name'] ?? false) {
            $builder->where('categories.name', 'LIKE', "%{$filters['name']}%");
        }
        if ($filters['status'] ?? false) {
            $builder->where('categories.status', '=', $filters['status']);
        }

        // $builder->when($filters['name'] ?? false, function ($builder, $value) {
        //     $builder->where('categories.name', 'LIKE', "%{$value}%");
        // });

        // $builder->when($filters['status'] ?? false, function ($builder, $value) {
        //     $builder->where('categories.status', '=', $value);
        // });
    }


    public static function rules($id = 0)
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                "unique:categories,name,$id", // Rule::unique('categories','name')->ignore($id)
                'filter:php,laravel',
                // new Filter(['admin', 'superuser', 'root']),
                // function ($attribute, $value, $fails) {
                //     if (strtolower($value) == 'laravel') {
                //         $fails('This name is forbidden!');
                //     }
                // },
                // function ($attribute, $value, $fail){
                //     if (in_array(strtolower($value), ['laravel','html','php'])) {
                //         $fail("This value for $attribute is forbidden");
                //     }
                // }
            ],
            'parent_id' => [
                'nullable', 'int', 'exists:categories,id'
            ],
            'image' => [
                'image', 'max:1048576', 'dimensions:min_width=100,min_height=100',
            ],
            'status' => 'required|in:active,archived',
        ];
    }
}
