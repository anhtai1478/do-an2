<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductSearchController extends Controller
{
    /**
     * Tìm sản phẩm có tên chứa chuỗi người dùng nhập (SQL LIKE '%name%').
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $name = trim($validated['name'] ?? '');

        $products = Product::query()
            ->when($name !== '', fn ($query) => $query->where('name', 'like', '%'.$name.'%'))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('frontend.search.index', compact('products', 'name'));
    }
}
