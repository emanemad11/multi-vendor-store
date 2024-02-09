<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;



class CategoryController extends Controller
{
    public function index()
    {
        $request = request();
        // $query = Category::query();
        // if ($name = $request->query('name')) // that query is for parameters i pass in url, it is different form the above query
        // {
        //     $query->where('name', 'like', "%{$name}%");
        // }
        // if ($status = $request->query('status')) {
        //     $query->where('status', $status);
        // }
        // used local scopes(filter)
        // $categories = $query->get();
        // $categories = Category::filter($request->query())->paginate(3);
        // SELECT a.*, b.name as parent_name
        // FROM categories as a
        // LEFT JOIN categories as b ON b.id = a.parent_id

        $categories = Category::with('parent')
            /*leftJoin('categories as parents', 'parents.id', '=', 'categories.parent_id')
            ->select([
                'categories.*',
                'parents.name as parent_name'
            ])*/
            //->select('categories.*')
            //->selectRaw('(SELECT COUNT(*) FROM products WHERE AND status = 'active' AND category_id = categories.id) as products_count')
            //->addSelect(DB::raw('(SELECT COUNT(*) FROM products WHERE category_id = categories.id) as products_count'))
            ->withCount([
                'products as products_count'
                // products=>name of relation
                //  => function($query) {
                //     $query->where('status', '=', 'active');
                // }
            ])
            ->filter($request->query())
            ->orderBy('categories.name')
            ->paginate(); // Return Collection object
        return view('dashboard.categories.index', compact('categories'));
    }
    public function create()
    {
        $parents = Category::all();
        $category = new Category();
        return view('dashboard.categories.create', compact('category', 'parents'));
    }

    public function store(CategoryRequest $request)
    {
        // التحقق من صحة البيانات المدخلة
        // $validated = $request->validate(Category::rules()); مش لازم اعمل الفاليديشن عشان انا مستخدمه category request بيعمل فاليديت لوحده

        // إنشاء فئة جديدة
        $data = $request->except('image');
        $slug = Str::slug($request->post('name'));
        $data['slug'] = $slug;

        // رفع الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request);
            if (!$imagePath) {
                return redirect()->back()->withInput()->with('error', 'Failed to upload image.');
            }
            $data['image'] = $imagePath;
        }

        // إنشاء الفئة وإعادة توجيه المستخدم
        $category = Category::create($data);
        if ($category) {
            return redirect()->route('dashboard.categories.index')->with('success', 'Category created.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create category.');
        }
    }

    public function show(Category $category)

    {
        // $category = Category::FindOrFail($id);
        return view('dashboard.categories.show', compact('category'));
        // return Redirect::route('dashboard.categories.show' ,compact('category'));
    }

    public function edit($id)
    {
        // جلب الفئة المعنية من قاعدة البيانات
        $category = Category::findorfail($id);

        // جلب جميع الفئات الأخرى كخيارات للفئة الرئيسية
        $parents = Category::where('id', '<>', $id)
            ->where(function ($query) use ($id) {
                $query->whereNull('parent_id')
                    ->orWhere('parent_id', '<>', $id);
            }) //الفانكشن دي بتعملي and مع ال query اللي قبلها
            // use id =>عشان   ال id مش global
            ->get();

        // عرض نموذج التحرير مع البيانات المستردة
        return view('dashboard.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate(Category::rules($id));

        $category = Category::findOrFail($id);
        $old_image = $category->image;

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request);
        }

        $category->update($data);

        if ($request->hasFile('image') && $old_image) {
            Storage::disk('public')->delete($old_image);
        }

        return redirect()->route('dashboard.categories.index')->with('success', 'Category updated!');
    }

    protected function uploadImage(Request $request)
    {
        if (!$request->hasFile('image')) {
            return;
        }
        $file = $request->file('image');
        $path = $file->store('uploads', ['disk' => 'public']);
        return $path;
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->delete()) {
            // حذف الفئة بنجاح، لذا قم بحذف الصورة إذا كانت موجودة
            // if ($category->image) {
            //     Storage::disk('public')->delete($category->image);
            // }

            return redirect()->route('dashboard.categories.index')->with('success', 'Category deleted!');
        } else {
            // فشل في حذف الفئة
            return redirect()->route('dashboard.categories.index')->with('error', 'Failed to delete category.');
        }
    }


    public function trash()
    {
        $categories = Category::onlyTrashed()->paginate();
        return view('dashboard.categories.trash', compact('categories'));
    }

    public function restore(Request $request, $id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('dashboard.categories.trash')
            ->with('succes', 'Category restored!');
    }

    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();


        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        return redirect()->route('dashboard.categories.trash')
            ->with('succes', 'Category deleted forever!');
    }
}
