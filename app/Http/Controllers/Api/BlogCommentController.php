<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;

class BlogCommentController extends Controller
{
    //
    public function comment(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|integer|exists:blog_comments,id',
        ]);

        $blog = Blog::find($id);

        if (!$blog) {
            return response()
                ->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài viết',
                ], 404);
        }

        $comment = $blog
            ->comments()
            ->create([
                'user_id' => $request->input('user_id'),
                'content' => $request->input('content'),
                'parent_id' => $request->input('parent_id'),
            ]);
        return response()
            ->json([
                'status' => 'success',
                'message' => 'Bình Luận Thành Công',
                'data' => $comment->load('user:id,name,avatar'),
            ]);
    }
}
