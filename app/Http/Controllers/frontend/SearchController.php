<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;

class SearchController extends Controller
{
    //
    public function searchByName(Request $request)
    {
        $search = $request->input('search');
        $products = [];

        if ($search) {
            $products = Product::where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->get();
        }
        return view('frontend.products.search', compact('products', 'search'));
    }

    // public function search(Request $request)
    // {
    //     $search = $request->input('search');
    //     $products = [];
    //     if ($search) {
    //         $products = Product::where('name', 'LIKE', "%{$search}%")
    //             ->orWhere('description', 'LIKE', "%{$search}%")
    //             ->get();
    //     }

    //     return view('frontend.products.search', compact('products', 'search'));
    // }

    public function searchAdvanced(Request $request)
    {
        $q = Product::query();

        if ($request->filled('name')) {
            $q->where('name', 'like', '%' . $request->name . '%');
        }

        // khoảng giá (SQL BETWEEN)
        if ($request->filled('price') && str_contains($request->price, '-')) {
            [$min, $max] = explode('-', $request->price, 2);
            $q->whereBetween('price', [(float) $min, (float) $max]);
        }

        // danh mục
        if ($request->filled('category')) {
            $q->where('category', $request->category);
        }
        // thương hiệu sản phẩm
        if ($request->filled('brand')) {
            $q->where('brand', $request->brand);
        }
        // trạng thái 
        if ($request->filled('status')) {
            $q->where('is_on_sale', $request->status);
        }
        // 6. Sắp xếp mới nhất & phân trang
        $products = $q->orderBy('created_at', 'desc')
            ->paginate(6)
            ->appends($request->query());


        $categories = Product::whereNotNull('category')
            ->distinct()
            ->pluck('category');

        $brands = Product::whereNotNull('brand')
            ->distinct()
            ->pluck('brand');

        return view('frontend.products.search-advanced', compact('products', 'categories', 'brands'));
    }


    public function searchByPriceRange(Request $request)
    {
        $min = (float) $request->input('min', 0);
        $max = (float) $request->input('max', 600);

        // 2. Dùng SQL WHERE price BETWEEN min AND max để lấy sản phẩm trong khoảng giá
        $products = Product::whereBetween('price', [$min, $max])
            ->latest()
            ->get();

        $html = '';

        foreach ($products as $item) {
            $html .= view('frontend.products.card', compact('item'))
                ->render();
        }

        if ($products->isEmpty()) {
            $html = '<div class="col-sm-12"><p class="text-center" style="padding: 30px;">Không có sản phẩm nào trong khoảng giá $' . number_format($min, 2) . ' - $' . number_format($max, 2) . '.</p></div>';
        }

        return response()->json([
            'status' => 'success',
            'count' => $products->count(),
            'html' => $html,
        ]);
    }
}
