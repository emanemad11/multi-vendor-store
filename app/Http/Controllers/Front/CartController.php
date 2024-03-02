<?php


namespace App\Http\Controllers\Front;

use App\Models\Cart;
use App\Http\Controllers\Controller;
use App\Repositories\Cart\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\Product;

class CartController extends Controller
{
    protected $cart;
    public $items;

    public $total;

    public function __construct(CartRepository $cart)
    {
        $this->cart = $cart;
        $this->items = $cart->get();
        $this->total = $cart->total();
    }

    public function index()
    {
        // $items=$this->items;
        // $total=$this->total;
        // dd([$items,$total]);
        // $repositry= new CartModelRepository();
        // $repositry->get();
        // CartRepository $cart =>لارافيل بتعتبره موجود ف ال servicecontainer
        // $repositry = App::make('cart');
        // $items = $cart->get();
        return view('front.cart', [
            'cart' => $this->cart,
            'items' => $this->items,
            'total' => $this->total,
        ]);
    }

    public function store(Request $request)
    {
        // $repositry= new CartModelRepository();
        // $repositry->add($product);
        $request->validate([
            'product_id' => ['required', 'int', 'exists:products,id'],
            'quantity' => ['nullable', 'int', 'min:1'],
        ]);
        $product = Product::findOrFail($request->post('product_id'));
        $this->cart->add($product, $request->post('quantity'));
        return redirect()->route('cart.index')->with('sucess', 'Product added suceesfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => ['required', 'int', 'min:1'],
        ]);

        $this->cart->update($id, $request->post('quantity'));
    }


    public function destroy($id)
    {
        $this->cart->delete($id);
        return [
            'message' => 'Item deleted!',
        ];
    }
}
