<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CartModelRepository implements CartRepository
{
    protected $items;

    public function __construct()
    {
        $this->items = collect([]);
    }

    public function get(): Collection
    {
        // Cart::where('cookie_id','=',$this->getCookieId())->with('product')->get();
        if (!$this->items->count()) {
            $this->items = Cart::with('product')->get();
        }

        return $this->items;
        // return Cart::where('cookie_id','=',$this->getCookieId())->get();
    }
    public function add(Product $product, $quantity = 1)
    {
        $item =  Cart::where('product_id', '=', $product->id)
        // ->where('cookie_id','=',$this->getCookieId())
            ->first();

        if (!$item) {
            $cart = Cart::create([
                 // 'id'=>Str::uuid(), عملته تلقاءي ب ال observer
                'user_id' => Auth::id(), // if not auth return null
                'product_id' => $product->id,
                'quantity' => $quantity,
                'cookie_id'=>$this->getCookieId(),
            ]);
            $this->get()->push($cart);
            return $cart;
        }

        return $item->increment('quantity', $quantity);
    }



    public function update($id, $quantity)
    {
        Cart::where('id', '=', $id)
        // -> where('cookie_id','=',$this->getCookieId())
            ->update([
                'quantity' => $quantity,
            ]);

    }

    public function delete($id)
    {
        Cart::where('id', '=', $id)
            ->delete();
            // where('cookie_id','=',$this->getCookieId())
    }

    public function empty()
    {
        Cart::query()->delete();
        // Cart::where('cookie_id','=',$this->getCookieId())->delete();
    }

    public function total(): float
    {

        /*return (float) Cart::where('cookie_id','=',$this->getCookieId())
        ->join('products', 'products.id', '=', 'carts.product_id')
            ->selectRaw('SUM(products.price * carts.quantity) as total')
            ->value('total');*/

        return $this->get()->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
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
