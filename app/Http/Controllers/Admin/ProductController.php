<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('user')->latest();

        if ($request->filled('member_name')) {
            $memberName = trim($request->input('member_name'));
            $query->whereHas('user', function ($q) use ($memberName) {
                $q->where('name', 'like', "%{$memberName}%");
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->paginate(10)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function edit(Product $product): View
    {
        $categories = Product::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        $brands = Product::query()->whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand');

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'is_on_sale' => 'required|in:0,1',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'company_profile' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'string',
        ]);

        $uploadedImages = $request->file('images', []);
        $imagesToDelete = $request->input('delete_images', []);

        $oldImages = $product->imagePaths();
        $remainingImages = array_values(array_diff($oldImages, $imagesToDelete));

        if (count($remainingImages) + count($uploadedImages) > 5) {
            return back()->withInput()->withErrors(['images' => 'Tổng số ảnh của sản phẩm không được vượt quá 5.']);
        }

        $newImages = [];
        if ($uploadedImages !== []) {
            $newImages = $this->storeImages($uploadedImages, (int) $product->user_id);
        }

        $validated['image'] = array_merge($remainingImages, $newImages);
        $validated['is_on_sale'] = (bool) $validated['is_on_sale'];
        $validated['discount_percent'] = $validated['is_on_sale'] ? ($validated['discount_percent'] ?? 0) : null;

        unset($validated['images'], $validated['delete_images']);

        $product->update($validated);

        if ($imagesToDelete !== []) {
            $this->deleteStoredImages($imagesToDelete);
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteStoredImages($product->imagePaths());
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm thành công.');
    }

    /**
     * @param  array<int, UploadedFile>  $uploadedImages
     * @return array<int, string>
     */
    private function storeImages(array $uploadedImages, int $userId): array
    {
        $paths = [];

        foreach ($uploadedImages as $uploadedImage) {
            $path = $uploadedImage->store('products/'.$userId, 'public');

            if ($path === false) {
                if ($paths !== [] && ! Storage::disk('public')->delete($paths)) {
                    throw new RuntimeException('Unable to clean up product images after a failed upload.');
                }

                throw new RuntimeException('Unable to store an uploaded product image.');
            }

            $paths[] = $path;
        }

        return $paths;
    }

    /**
     * @param  array<int, string>  $imagePaths
     */
    private function deleteStoredImages(array $imagePaths): void
    {
        $ownedImagePaths = array_values(array_filter(
            $imagePaths,
            fn (string $path): bool => ! str_contains($path, '..'),
        ));

        if ($ownedImagePaths !== []) {
            Storage::disk('public')->delete($ownedImagePaths);
        }
    }
}
