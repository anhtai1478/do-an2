<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function listBlogs()
    {
        $blogs = Blog::latest()
            ->paginate(3);

        return response()->json([
            "status" => 'success',
            "blogs" => $blogs
        ]);
    }

    public function detail($id)
    {
        // with: lấy dữ liệu từ model khác có liên quan 
        $blog = Blog::with(['comments.user', 'ratings'])
            ->find($id);

        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'không tìm thấy bài viết'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $blog
        ]);
    }
}
