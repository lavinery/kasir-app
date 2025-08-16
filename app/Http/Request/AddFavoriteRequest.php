<?php
// app/Http/Requests/AddFavoriteRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->level == 1;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('produk', 'id_produk'),
                Rule::unique('favorite_products', 'product_id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk harus dipilih.',
            'product_id.exists' => 'Produk tidak ditemukan.',
            'product_id.unique' => 'Produk sudah ada di favorit.',
        ];
    }
}

// app/Http/Requests/ReorderFavoritesRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderFavoritesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->level == 1;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:favorite_products,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Data urutan favorit diperlukan.',
            'items.*.id.required' => 'ID favorit diperlukan.',
            'items.*.id.exists' => 'Favorit tidak ditemukan.',
            'items.*.sort_order.required' => 'Urutan diperlukan.',
            'items.*.sort_order.integer' => 'Urutan harus berupa angka.',
        ];
    }
}