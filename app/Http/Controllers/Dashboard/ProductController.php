<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tag;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    //useing global scopes
    public function index()
    {
        // eagerloading =>foreach=>one to many relationshop
        $products = Product::with(['category', 'store'])->paginate(100); // 3 select from database
        // select * from products
        // select * from categories where id in (..)
        // select * from stores where id in (..)
        return view('dashboard.products.index', compact('products'));
    }
    public function create()
    {
    }
    public function store(Request $request)
    {
    }
    public function show(Product $product)
    {
    }
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        // $this->authorize('update', $product);
        $tags = implode(',', $product->tags()->pluck('name')->toArray()); //string
        // pluk =>array of string not array of objects
        // tags()=>collection of relations
        return view('dashboard.products.edit', compact('product', 'tags'));
    }
    public function update(Request $request, Product $product)
    {
        // $this->authorize('update', $product);
        $product->update($request->except('tags'));

        // $tags=explode(',', $request->post('tags'));
        // $tag_ids=[];
        // foreach ($tags as $t_name) {
        //     $slug = Str::slug($t_name);
        //     $tag = Tag::firstOrCreate(['slug' => $slug], ['name' => $t_name]);
        //     $tag_ids[] = $tag->id;
        // }
        dd($request->post('tags')); // string علي صيغه json 
        $tags = json_decode($request->post('tags'));
        $tag_ids = [];

        $saved_tags = Tag::all();

        foreach ($tags as $item) {
            $slug = Str::slug($item->value);
            $tag = $saved_tags->where('slug', $slug)->first();
            if (!$tag) {
                $tag = Tag::create([
                    'name' => $item->value,
                    'slug' => $slug,
                ]);
            }
            $tag_ids[] = $tag->id;
        }

        // foreach($tags as $t_name)
        // {
        //     $slug=Str::slug($t_name);
        //     $tag=Tag::where('slug',$slug);
        //     if(!$tag){
        //         $tag=Tag::create([
        //             'name'=>$t_name,
        //             'slug'=>$slug
        //         ]);
        //     }

        //     $tag_ids[]=$tag->id;
        // }

        $product->tags()->sync($tag_ids);  // sync=>خاصه بعلاقات ال many to many =>تعمل علي الحزف والاضافه من الجدول الوسيط
        return redirect()->route('dashboard.products.index')
            ->with('success', 'Product updated');
    }
    public function destroy(Product $product)
    {
    }
}
