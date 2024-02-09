<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return TRUE; //صلاحيه اليوزر
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $id = $this->route('category'); // اسم البرامتر المبعوت ف الروت
        return Category::rules($id);
    }

    public function messages(): array
    {
        return [
            // 'name.required' => 'A name is required',
            // 'body.required' => 'A message is required',
        ];
    }
    // بغير رساله الايرور  ->lang->en->validate
}
