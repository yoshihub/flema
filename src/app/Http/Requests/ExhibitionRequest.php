<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'explanation' => ['required', 'max:255'],
            'exhibition_image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png'
            ],
            'categories' => ['required', 'array', 'min:1'],
            'condition' => ['required'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'explanation.required' => '商品説明を入力してください',
            'explanation.max' => '商品説明は255文字以内で入力してください',
            'exhibition_image.required' => '商品画像を選択してください',
            'exhibition_image.image' => '画像ファイルをアップロードしてください',
            'exhibition_image.mimes' => '画像はJPEGまたはPNG形式でアップロードしてください',
            'categories.required' => 'カテゴリーを選択してください',
            'categories.array' => 'カテゴリーの形式が不正です',
            'categories.min' => '少なくとも1つのカテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '価格を入力してください',
            'price.numeric' => '価格は数値で入力してください',
            'price.min' => '価格は0円以上にしてください',
        ];
    }
}
