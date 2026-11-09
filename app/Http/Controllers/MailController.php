<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Mail\MailNotify;
use App\Models\History;
use App\Models\User;

class MailController extends Controller
{
    public function index()
    {
        // 1. Tạo mảng dữ liệu gồm tiêu đề (subject) và nội dung (body) để truyền vào MailNotify
        $data = [
            'subject' => 'Cambo Tutorial Mail',
            'body' => 'Hello This is my email delivery!'
        ];

        try {
            // 2. Gửi mail tới địa chỉ Gmail của bạn bằng class MailNotify
            Mail::to('anhtaihuynh19@gmail.com')->send(new MailNotify($data));

            // 3. Nếu gửi thành công thì trả về thông báo JSON như hình mẫu
            return response()->json(['Great check your mail box']);
        } catch (Exception $th) {
            // 4. Nếu cấu hình sai hoặc lỗi mạng thì nhảy vào catch và báo sorry
            return response()->json(['sorry']);
        }
    }

    /**
     * Hàm xử lý khi khách bấm nút Order (hoặc Đăng ký & Order) tại trang Checkout
     */
    public function order(Request $request)
    {
        // 1. Lấy giỏ hàng từ Session
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('checkout.index')->withErrors([
                'cart' => 'Giỏ hàng của bạn đang trống!'
            ]);
        }

        // 2. Kiểm tra user đã login hay chưa
        if (Auth::check()) {
            // TRƯỜNG HỢP 1: Đã login -> lấy luôn user hiện tại
            $user = Auth::user();
        } else {
            // TRƯỜNG HỢP 2: Chưa có tài khoản -> Đăng ký nhanh tại chỗ
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:6'],
                'phone' => ['required', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:255'],
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'level' => 0, // 0 là Member
            ]);

            // Đăng nhập luôn cho tài khoản vừa tạo
            Auth::login($user);
        }

        // 3. Tính tổng tiền đơn hàng
        $subTotal = 0;
        foreach ($cart as $item) {
            $subTotal += (float) $item['price'] * (int) $item['quantity'];
        }
        $ecoTax = count($cart) > 0 ? 2 : 0;
        $grandTotal = $subTotal + $ecoTax;

        // 4. Lưu lịch sử mua hàng vào bảng histories (id, email, phone, name, id_user, price)
        History::create([
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'name' => $user->name,
            'id_user' => $user->id,
            'price' => $grandTotal,
        ]);

        // 5. Đóng gói dữ liệu $data và gửi mail kèm giao diện (emails.index) cho khách hàng
        $data = [
            'subject' => 'Xác nhận đơn hàng - E-Shopper',
            'body' => 'Cảm ơn ' . $user->name . ' đã mua hàng tại E-Shopper! Dưới đây là thông tin đơn hàng của bạn:',
            'user' => $user,
            'cart' => $cart,
            'subTotal' => $subTotal,
            'ecoTax' => $ecoTax,
            'grandTotal' => $grandTotal,
        ];

        Mail::to($user->email)->send(new MailNotify($data));

        // 6. Xóa giỏ hàng sau khi đặt thành công và quay lại báo thành công
        session()->forget('cart');

        return redirect()->route('checkout.index')->with(
            'success',
            'Đặt hàng thành công! Đã gửi mail xác nhận tới ' . $user->email . ' và lưu vào lịch sử mua hàng.'
        );
    }
}
