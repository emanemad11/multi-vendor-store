<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;

class CookieIDScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('cookie_id','=', $this->getCookieId()
        // App::make('CartRepository')->getCookieId()
    );
    }
    protected function getCookieId()
    {
        // Retrieve the value of the 'cart_id' cookie, if it exists
        $cookie_id = Cookie::get('cart_id');

        // Check if the 'cart_id' cookie does not exist
        if (!$cookie_id) {
            // Generate a new UUID using Laravel's Str::uuid() method
            $cookie_id = Str::uuid();

            // Queue a new cookie with the name 'cart_id', the generated UUID as its value,
            // and an expiration time set to 30 days from the current time
            Cookie::queue('cart_id', $cookie_id, 30*60*24);
        }

        // Return the retrieved or newly generated 'cart_id'
        return $cookie_id;
    }
}
