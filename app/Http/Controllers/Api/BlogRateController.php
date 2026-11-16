<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;

class BlogRateController extends Controller
{
    //
    public function rate(Request $request, $id)
    {
        // 1. Kiểm tra dữ liệu gửi lên (user_id phải tồn tại, rating từ 1 đến 5)
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'rating'  => 'required|integer|min:1|max:5',
        ]);

        // 2. Tìm bài viết theo id
        $blog = Blog::find($id);
        if (!$blog) {
            return response()
                ->json([
                    'status'  => 'error',
                    'message' => 'Không tìm thấy bài viết',
                ], 404);
        }

        // 3. Lưu hoặc cập nhật đánh giá sao cho user này
        $blog->ratings()->updateOrCreate(
            ['user_id' => $request
                ->input('user_id')],

            ['rating'  => $request
                ->input('rating')]
        );

        // 4. Trả về kết quả JSON
        return response()->json([
            'status'        => 'success',
            'message'       => 'Đánh giá thành công',
            //điểm đánh giá trung bình của bài viết.
            'averageRating' => number_format($blog
                ->ratings()
                ->avg('rating'), 1),
            'ratingCount'   => $blog
                ->ratings()
                ->count(),
        ]);
    }
}
