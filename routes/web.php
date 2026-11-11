<?php

use App\Http\Controllers\AccountProductController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\frontend\BlogController as FrontendBlogController;
use App\Http\Controllers\frontend\SearchController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ProfileController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// login, register, reset password routes
Auth::routes();

Route::get('/test', [MailController::class, 'index']);

Route::get('/', function () {
    $product = Product::query()
        ->latest()
        ->take(6)
        ->get();

    $categories = Product::whereNotNull('category')
        ->distinct()
        ->pluck('category');

    $brands = Product::whereNotNull('brand')
        ->distinct()
        ->pluck('brand');

    return view('frontend.home.home', compact('product', 'categories', 'brands'));
})->name('frontend.home.home');

Route::redirect('/home', '/');

// blog routes
Route::get('/blogs', [FrontendBlogController::class, 'index'])
    ->name('frontend.blogs.index');

Route::get('/blogs/{id}', [FrontendBlogController::class, 'show'])
    ->name('frontend.blogs.show');

Route::get('/products/{product}', function (Product $product) {
    return view('frontend.products.show', compact('product'));
})->whereNumber('product')
    ->name('frontend.products.show');

Route::get('/products/search', [SearchController::class, 'searchByName'])
    ->name('products.search');

Route::post('/products/search', [SearchController::class, 'searchByName'])
    ->name('products.search.post');

Route::post('/search-advanced', [SearchController::class, 'searchPost'])
    ->name('search.advanced.post');

Route::get('/search-advanced/{param?}', [SearchController::class, 'searchAdvanced'])
    ->name('search.advanced');
Route::get('/search-price-range',[SearchController::class,'searchByPriceRange'])
->name('search.price.range');

// 1. Route mở trang Giỏ hàng (Cho phép cả khách đã login và chưa login)
Route::get('/cart', function () {
    $rawCart = session()->get('cart', []);
    $cart = [];
    foreach ($rawCart as $item) {
        $id = $item['id'];
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += (int) $item['quantity'];
        } else {
            $cart[$id] = $item;
        }
    }
    session()->put('cart', $cart);

    // Tính tổng tiền tất cả sản phẩm trong giỏ
    $subTotal = 0;
    foreach ($cart as $item) {
        $subTotal += (float) $item['price'] * (int) $item['quantity'];
    }
    $ecoTax = count($cart) > 0 ? 2 : 0;
    $grandTotal = $subTotal + $ecoTax;

    return view('frontend.cart.index', compact('cart', 'subTotal', 'ecoTax', 'grandTotal'));
})->name('cart.index');

// 2. Route Thêm vào giỏ hàng (Lưu theo key $id để cộng dồn số lượng)
Route::post('/cart/add/{id}', function (Request $request, $id) {
    $validated = $request->validate([
        'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
    ]);

    $product = Product::findOrFail($id);
    $quantity = $validated['quantity'] ?? 1;
    $rawCart = session()->get('cart', []);
    $cart = [];
    foreach ($rawCart as $item) {
        $itemId = $item['id'];
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] += (int) $item['quantity'];
        } else {
            $cart[$itemId] = $item;
        }
    }

    // Nếu sản phẩm đã có trong giỏ thì cộng dồn số lượng, chưa có thì tạo mới
    if (isset($cart[$id])) {
        $cart[$id]['quantity'] += $quantity;
    } else {
        $cart[$id] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'image' => $product->primaryImage(),
            'quantity' => $quantity,
        ];
    }

    session()->put('cart', $cart);

    return response()->json([
        'count' => array_sum(array_column($cart, 'quantity')),
    ]);
})->name('cart.add');

// 3. Route xử lý AJAX Tăng (+), Giảm (-), Xóa (delete) trong trang Cart
Route::post('/cart/update-ajax', function (Request $request) {
    $validated = $request->validate([
        'id' => ['required'],
        'action' => ['required', 'in:up,down,delete'],
    ]);

    $id = $validated['id'];
    $action = $validated['action'];
    $rawCart = session()->get('cart', []);
    $cart = [];
    foreach ($rawCart as $item) {
        $itemId = $item['id'];
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] += (int) $item['quantity'];
        } else {
            $cart[$itemId] = $item;
        }
    }

    if (isset($cart[$id])) {
        if ($action === 'up') {
            $cart[$id]['quantity'] += 1;
        } elseif ($action === 'down') {
            $cart[$id]['quantity'] -= 1;
            // Nếu giảm xuống < 1 thì xóa luôn sản phẩm khỏi giỏ
            if ($cart[$id]['quantity'] < 1) {
                unset($cart[$id]);
            }
        } elseif ($action === 'delete') {
            unset($cart[$id]);
        }

        session()->put('cart', $cart);
    }

    $subTotal = 0;
    // Khởi tạo tổng tiền tạm tính ban đầu = 0
    foreach ($cart as $item) {
        // Cộng dồn: (Giá 1 sản phẩm * Số lượng sản phẩm đó) vào tổng tạm tính
        $subTotal += (float) $item['price'] * (int) $item['quantity'];
    }
    // Thuế môi trường (Eco Tax): Nếu trong giỏ còn sản phẩm (> 0) thì tính $2, nếu giỏ trống thì = $0
    $ecoTax = count($cart) > 0 ? 2 : 0;
    // Tổng tiền cuối cùng khách phải trả = Tổng tạm tính + Thuế Eco Tax
    $grandTotal = $subTotal + $ecoTax;

    // Trả dữ liệu dạng JSON về cho JavaScript (AJAX) để cập nhật lại giao diện mà không cần load lại trang
    return response()->json([
        'status' => 'success',
        // Báo trạng thái xử lý thành công
        'item_exists' => isset($cart[$id]),
        // Kiểm tra sản phẩm vừa bấm còn trong giỏ không (true: còn, false: đã bị xóa/về 0)
        'item_quantity' => $cart[$id]['quantity'] ?? 0,
        // Số lượng mới của riêng sản phẩm vừa bấm (+ / -)
        'item_total' => isset($cart[$id]) ? round($cart[$id]['price'] * $cart[$id]['quantity'], 2) : 0,
        // Thành tiền mới của riêng dòng sản phẩm đó (Giá * Số lượng mới)
        'cart_count' => array_sum(array_column($cart, 'quantity')),
        // Tổng số lượng tất cả món hàng để cập nhật chữ Cart(x) trên Header
        'sub_total' => round($subTotal, 2),
        // Tổng tiền tạm tính của toàn bộ giỏ hàng (Cart Sub Total)
        'eco_tax' => $ecoTax,
        // Tiền thuế Eco Tax ($2 hoặc $0)
        'grand_total' => round($grandTotal, 2),
        // Tổng tiền cuối cùng của cả giỏ hàng (Total bên dưới)
    ]);
})->name('cart.update.ajax');

// 4. Route mở trang Checkout
Route::get('/checkout', function () {
    $cart = session()->get('cart', []);

    return view('frontend.checkout.index', compact('cart'));
})->name('checkout.index');

// 5. Route xử lý nút Order (gửi mail + lưu bảng histories)
Route::post('/checkout/order', [MailController::class, 'order'])->name('checkout.order');

Route::middleware(['auth'])->group(function () {
    Route::post('/blog/rate/ajax', [FrontendBlogController::class, 'rate'])
        ->name('frontend.blogs.rate.ajax');

    Route::post('/blog/comment/ajax', [FrontendBlogController::class, 'comment'])
        ->name('frontend.blogs.comment.ajax');

    // account
    Route::get('/account/update', [ProfileController::class, 'edit'])
        ->name('account.edit');

    Route::put('/account/update', [ProfileController::class, 'update'])
        ->name('account.update');

    Route::get('/account/my-product', [AccountProductController::class, 'index'])
        ->name('account.my-product');

    Route::get('/account/add-product', [AccountProductController::class, 'create'])
        ->name('account.add-product');

    Route::post('/account/my-product', [AccountProductController::class, 'store'])
        ->name('account.products.store');

    Route::get('/account/my-product/{product}/edit', [AccountProductController::class, 'edit'])
        ->whereNumber('product')
        ->name('account.products.edit');

    Route::put('/account/my-product/{product}', [AccountProductController::class, 'update'])
        ->whereNumber('product')
        ->name('account.products.update');

    Route::delete('/account/my-product/{product}', [AccountProductController::class, 'destroy'])
        ->whereNumber('product')
        ->name('account.products.destroy');

    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/profile', [UserController::class, 'profile'])->name('admin.profile');
        Route::post('/admin/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');

        Route::get('/admin/country', [CountryController::class, 'index'])->name('admin.country.index');
        Route::post('/admin/country', [CountryController::class, 'store'])->name('admin.country.store');
        Route::delete('/admin/country/{id}', [CountryController::class, 'delete'])->name('admin.country.delete');

        Route::get('/admin/blogs', [BlogController::class, 'index'])->name('admin.blogs.index');
        Route::get('/admin/blogs/create', [BlogController::class, 'create'])->name('admin.blogs.create');
        Route::post('/admin/blogs', [BlogController::class, 'store'])->name('admin.blogs.store');

        Route::get('/admin/blogs/{id}/edit', [BlogController::class, 'edit'])->name('admin.blogs.edit');
        Route::put('/admin/blogs/{id}', [BlogController::class, 'update'])->name('admin.blogs.update');
        Route::delete('/admin/blogs/{id}', [BlogController::class, 'delete'])->name('admin.blogs.delete');
    });

    Route::get('/frontend/home', function () {
        return view('frontend.home.home');
    });
});
