<?php

namespace App\Models;

use App\Models\Scopes\CookieIDScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Observers\CartObserver;

class Cart extends Model
{
    use HasFactory;
    public $incrementing = false;

    protected $fillable = [
        'cookie_id', 'user_id', 'product_id', 'quantity', 'options',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Anonymous',
        ]);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CookieIDScope);
    }

    // Events (Observers)
    // creating, created, updating, updated, saving, saved
    // deleting, deleted, restoring, restored, retrieved

    // protected static function boot(): void
    // {
    //     // Cart::observe(CartObserver::class);

    //     // single event
    //     // static::creating(function(Cart $cart) {
    //     //     $cart->id = Str::uuid();
    //     // });
    // }

    // public function boot(){
    //     Cart::observe(CartObserver::class);
    // }



}
