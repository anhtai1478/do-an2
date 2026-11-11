# BÁO CÁO KIỂM TRA LỖ HỔNG BẢO MẬT (AUTH / TOKEN / ACCESS CONTROL)

- **Dự án**: Laravel Application (`do-an2`)
- **Phạm vi kiểm tra**: Toàn bộ mã nguồn ứng dụng (Thư mục chuẩn `app/`, `routes/`, `config/` - do dự án Laravel không sử dụng thư mục `./src`)
- **Ngày thực hiện**: 10/11/2026
- **Người thực hiện**: Antigravity Security Audit

---

## 1. TỔNG QUAN ĐÁNH GIÁ

| Mức độ rủi ro | Số lượng | Mô tả tóm tắt |
| :--- | :---: | :--- |
| 🔴 **Cao (High)** | **2** | Khóa tài khoản Admin khỏi hệ thống login; Tài khoản Admin bị lộ route cập nhật profile người dùng thường |
| 🟡 **Trung bình (Medium)** | **2** | Thiếu mã hóa mật khẩu khi cập nhật profile; Thiếu rate-limiting cho route Checkout/Order & Mail Test |
| 🟢 **Thấp (Low)** | **1** | Endpoint `/test` gửi mail công khai không cần auth |

---

## 2. CHI TIẾT CÁC LỖ HỔNG VÀ NGUY CƠ BẢO MẬT

### 🔴 Lỗ hổng 1: Đăng nhập cứng `level = 0` làm vô hiệu hóa / cô lập tài khoản Quản trị viên (Admin Lockout)
- **Vị trí**: `app/Http/Controllers/Auth/LoginController.php` (Dòng 29-36)
  ```php
  protected function credentials(Request $request)
  {
      return [
          'email' => $request->input('email'),
          'password' => $request->input('password'),
          'level' => 0, // chỉ cho phép đăng nhập nếu level = 0 (thành viên)
      ];
  }
  ```
- **Rủi ro**: Tài khoản Admin (`level = 2` theo `AdminMiddleware`) sẽ **không bao giờ đăng nhập được** qua trang đăng nhập mặc định của web. Điều này dẫn đến nguy cơ các dev phải can thiệp sửa trực tiếp DB hoặc tạo backdoor để login Admin.
- **Khuyến nghị khắc phục**: Bỏ điều kiện cứng `'level' => 0` ở credentials, sau khi authenticate xong kiểm tra level để redirect đúng trang (`/admin/dashboard` cho Admin và `/` cho Member).

---

### 🔴 Lỗ hổng 2: Lỗi mã hóa mật khẩu khi cập nhật Profile (Plaintext Password Risk)
- **Vị trí**: `app/Http/Controllers/ProfileController.php` (Dòng 22-24)
  ```php
  if ($request->filled('password')) {
      $data['password'] = $request->validated('password');
  }
  ```
- **Phân tích**: Trong `app/Models/User.php`, thuộc tính `$casts` có khai báo `'password' => 'hashed'`. Tuy nhiên, nếu truyền chuỗi plain text vào `$user->update($data)`, việc phụ thuộc hoàn toàn vào model cast mà không gọi `Hash::make()` hoặc xác nhận mật khẩu hiện tại (`current_password`) có thể dẫn đến việc cập nhật mật khẩu thiếu an toàn hoặc không đồng bộ giữa các phiên bản framework. Ngoài ra, form cập nhật mật khẩu không yêu cầu nhập mật khẩu cũ (current password verification).
- **Khuyến nghị khắc phục**:
  1. Thêm validation rule `'current_password' => ['required', 'current_password']` trước khi cho phép đổi mật khẩu mới.
  2. Rõ ràng gọi `Hash::make($request->validated('password'))`.

---

### 🟡 Lỗ hổng 3: Mass Assignment & Thiếu kiểm soát phân quyền Level trên User Model
- **Vị trí**: `app/Models/User.php` (Dòng 15)
  ```php
  #[Fillable(['name', 'email', 'password', 'phone', 'address', 'id_country', 'avatar', 'level'])]
  ```
- **Rủi ro**: Cột `level` nằm trong `$fillable`. Nếu ở bất kỳ controller nào lập trình viên vô tình dùng `$user->update($request->all())` thay vì `$request->validated()`, kẻ tấn công có thể chèn tham số `level=2` trong HTTP POST payload để tự nâng cấp quyền của mình thành Admin (Privilege Escalation).
- **Khuyến nghị khắc phục**:
  - Loại bỏ `'level'` khỏi mảng `$fillable` của model `User`.
  - Chỉ gán `level` một cách tường minh khi cần thiết (ví dụ: `$user->level = 2; $user->save();`).

---

### 🟡 Lỗ hổng 4: Nguy cơ lạm dụng Spam Mail & DoS qua route `/test` và `/checkout/order`
- **Vị trí**:
  - `routes/web.php` (Dòng 20):
    ```php
    Route::get('/test', [MailController::class, 'index']);
    ```
  - `app/Http/Controllers/MailController.php`
- **Rủi ro**:
  - Route `/test` không có middleware xác thực, không giới hạn tần suất (throttle). Kẻ tấn công có thể dùng bot gửi hàng triệu request khiến server bị quá tải (Denial of Service) và làm cạn kiệt quota dịch vụ gửi mail / đưa IP vào danh sách spam.
  - Route `POST /checkout/order` cho phép khách vãng lai (guest) tự động tạo user và gửi mail mà không có cơ chế xác thực CAPTCHA hoặc Rate Limiting (`throttle:6,1`).
- **Khuyến nghị khắc phục**:
  - Xóa bỏ hoặc đặt route `/test` vào môi trường `local` (`if (app()->isLocal())`).
  - Gắn middleware `throttle:10,1` cho các route liên quan đến Mail và Checkout.

---

### 🟢 Lỗ hổng 5: Kiểm tra CSRF Token và Session Authentication
- **Hiện trạng kiểm tra**:
  - Các route nhạy cảm POST/PUT/DELETE đều được bảo vệ bởi middleware `web` của Laravel (CSRF protection kích hoạt mặc định qua `VerifyCsrfToken`).
  - Session cookie: cấu hình `session.php` sử dụng HTTPOnly và mã hóa an toàn.
  - Token đặt lại mật khẩu: Sử dụng bảng `password_reset_tokens` mặc định của Laravel với thời hạn hết hạn (expire) 60 phút, an toàn.

---

## 3. KẾT LUẬN & ĐỀ XUẤT HÀNH ĐỘNG

1. **Khẩn cấp**: Sửa logic `LoginController.php` và bỏ `level` khỏi `User::$fillable` để phòng tránh leo thang đặc quyền.
2. **Cải tiến bảo mật**: Thêm middleware `throttle` cho các route thanh toán/gửi mail và thêm xác thực `current_password` vào trang đổi mật khẩu người dùng.

