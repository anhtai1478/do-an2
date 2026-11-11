# Nhật Ký Làm Việc & Lịch Sử Dự Án (Work Journal)

> **Hướng dẫn bắt buộc cho AI Agent**:
> - Mỗi khi bắt đầu phiên làm việc mới (hoặc mỗi ngày khi người dùng mở lại dự án), hãy đọc file `docs/WORK_LOG.md` này đầu tiên để nắm bắt ngữ cảnh, lịch sử câu hỏi của người dùng và các thay đổi đã thực hiện.
> - Sau mỗi câu hỏi của người dùng hoặc mỗi khi thực hiện bất kỳ thay đổi/sửa lỗi nào trong dự án, **BẮT BUỘC** phải cập nhật thêm một mục mới vào file này (ghi rõ ngày giờ, yêu cầu/câu hỏi của người dùng, nguyên nhân lỗi nếu có, và chi tiết những việc đã xử lý).

---

## Ngày 06/11/2026

### 1. Tải mã nguồn dự án từ GitHub
- **Thời gian**: 14:51
- **Yêu cầu của người dùng**: Tải repository `https://github.com/anhtai1478/do-an2` về máy.
- **Đã thực hiện**:
  - Clone toàn bộ mã nguồn từ `https://github.com/anhtai1478/do-an2.git` vào thư mục `c:\xampp\htdocs\do-an2`.
  - Kiểm tra môi trường máy: PHP 8.3.35, Composer 2.10.3, Node.js v24.19.0, NPM 11.17.0.

### 2. Hỏi về lỗi khi chạy `php artisan seve` / `php artisan sev`
- **Thời gian**: 14:54
- **Câu hỏi của người dùng**: Chạy `php artisan seve` và `php artisan sev` bị báo lỗi `Failed opening required 'C:\xampp\htdocs\do-an2/vendor/autoload.php'`.
- **Nguyên nhân**:
  1. Thư mục `vendor/` chưa được cài đặt xong (do mới clone từ GitHub về và quá trình `composer install` bị ngắt giữa chừng khi tắt phiên).
  2. Gõ sai chính tả lệnh (`seve` / `sev` thay vì `serve`).

---

## Ngày 07/11/2026

### 1. Sửa dứt điểm lỗi thiếu `vendor/autoload.php`, lỗi PSR-4, Database và thiết lập hệ thống Nhật ký (`docs/WORK_LOG.md`)
- **Thời gian**: 11:55 - 12:01
- **Yêu cầu của người dùng**:
  1. Sửa hết lỗi `Failed opening required 'C:\xampp\htdocs\do-an2/vendor/autoload.php'` khi chạy `artisan`.
  2. Tạo một cuốn nhật ký lưu trong thư mục `docs` của dự án để ghi lại toàn bộ lịch sử hỏi đáp và các việc AI đã làm, giúp mỗi ngày mở lên AI có thể đọc lại lịch sử và làm tiếp.
- **Nguyên nhân lỗi phát hiện**:
  1. Quá trình `composer install` hôm trước bị ngắt giữa chừng nên thư mục `vendor/` chỉ có một vài gói lẻ và chưa sinh ra file `vendor/autoload.php`.
  2. File `app/Http/Controllers/frontend/UserController.php` khai báo sai chuẩn PSR-4 (`namespace App\Http\Controllers\Frontend;` với chữ `F` viết hoa trong khi tên thư mục là `frontend` viết thường), gây cảnh báo khi `composer dump-autoload`.
  3. Dịch vụ MySQL của XAMPP chưa được bật khi kiểm tra database (`SQLSTATE[HY000] [2002]`).
  4. Chưa cài `node_modules` và chưa chạy `npm run build` cho Vite manifest (`public/build/manifest.json`).
- **Đã thực hiện & Sửa xong**:
  - Chạy hoàn tất `composer install` (cài đặt đầy đủ 117 packages và tạo `vendor/autoload.php`).
  - Sửa namespace trong `app/Http/Controllers/frontend/UserController.php` thành `namespace App\Http\Controllers\frontend;` để đồng bộ với thư mục `frontend` và `BlogController.php`, không còn cảnh báo PSR-4.
  - Khởi chạy MySQL (`C:\xampp\mysql\bin\mysqld.exe`) và kiểm tra database `laravel` (đã có đầy đủ 12 migrations được chạy).
  - Chạy `npm install` và `npm run build` để tạo `public/build/manifest.json` và biên dịch CSS/JS hoàn chỉnh.
  - Tạo file nhật ký `docs/WORK_LOG.md` và thêm quy tắc bắt buộc đọc/ghi nhật ký vào cả `AGENTS.md` và `CLAUDE.md`.

---

## Ngày 09/11/2026

### 1. Sửa lỗi `Table 'laravel.histories' doesn't exist` khi đặt hàng (`POST /checkout/order`)
- **Thời gian**: 14:04
- **Yêu cầu của người dùng**: Gửi ảnh chụp màn hình lỗi `SQLSTATE[42S02]: Base table or view not found: 1146 Table 'laravel.histories' doesn't exist` tại `app/Http/Controllers/MailController.php:85` khi bấm đặt hàng ở trang Checkout.
- **Nguyên nhân lỗi**:
  - Đã có file migration `database/migrations/2026_11_08_110506_create_histories_table.php` và model `App\Models\History`, nhưng migration này đang ở trạng thái `Pending` (chưa được chạy vào database `laravel`).
- **Đã thực hiện & Sửa xong**:
  - Chạy lệnh `php artisan migrate --force` để thực thi migration `2026_11_08_110506_create_histories_table`, tạo bảng `histories` (`id`, `email`, `phone`, `name`, `id_user`, `price`, `timestamps`) trong database `laravel`.


### 2. Chức năng tìm kiếm sản phẩm theo tên (SQL LIKE)
- **Thời gian**: 14:08
- **Yêu cầu của người dùng**: Nhập tên vào ô Search trên header → chuyển sang trang search và hiển thị tất cả sản phẩm có tên chứa chuỗi đó (VD: sản phẩm `test`, gõ `e` thì phải hiện). Dùng SQL `LIKE`.
- **Đã thực hiện**:
  - Tạo `app/Http/Controllers/frontend/ProductSearchController.php` (method `index`): validate `name`, query `Product::where('name', 'like', '%'.$name.'%')`, phân trang 9 sản phẩm/trang, giữ query string. Không nhập gì thì hiện tất cả sản phẩm.
  - Thêm route `GET /search` tên `frontend.search` trong `routes/web.php`.
  - Sửa ô Search trong `resources/views/frontend/layouts/header.blade.php` thành form `GET` gửi tới `route('frontend.search')` với `name="name"` (bấm Enter là tìm, giữ lại từ khóa đã nhập).
  - Tạo view `resources/views/frontend/search/index.blade.php` (tiêu đề kết quả + số lượng, thông báo khi không có kết quả, phân trang).
  - Tách thẻ sản phẩm thành partial `resources/views/frontend/products/card.blade.php`, dùng chung cho trang chủ (`frontend/home/home.blade.php`) và trang search.
  - Thêm test `tests/Feature/ProductSearchTest.php` (3 test, đều pass).
- **Ghi chú**:
  - PHP CLI của XAMPP chưa bật `pdo_sqlite` nên khi chạy test phải dùng: `php -d extension=pdo_sqlite -d extension=sqlite3 vendor\bin\phpunit`.
  - `tests/Feature/ExampleTest.php` đã lỗi từ trước (thiếu `RefreshDatabase` → không có bảng `products`), không liên quan đến chức năng search.


### 3. Kích hoạt và cấu hình extension SQLite & sửa test
- **Thời gian**: 14:14
- **Yêu cầu của người dùng**: Bật extension SQLite để chạy test trơn tru, không bị lỗi.
- **Đã kiểm tra & thực hiện**:
  - Kiểm tra `php.ini` và `php -m`: extension `pdo_sqlite` và `sqlite3` đã được bật sẵn trong PHP của XAMPP (`extension=pdo_sqlite`, `extension=sqlite3`).
  - Sửa test `tests/Feature/ExampleTest.php`: thêm trait `use RefreshDatabase;` để test trang chủ `/` không bị lỗi thiếu bảng `products`.
  - Cập nhật test `tests/Feature/ProductSearchTest.php`: đổi tên mẫu thành `Xyzwq Product` và `Qwvx Item` để không bị trùng với chữ `T-Shirt` ở footer tĩnh của template.
  - Chạy `vendor\bin\phpunit tests/Feature/ProductSearchTest.php tests/Feature/ExampleTest.php`: Đều chạy thành công (4 passed, 12 assertions).
  - Chạy `vendor\bin\pint`: pass formatting style.


---

## Ngày 10/11/2026

### 1. Hướng dẫn và cài đặt Aider cho dự án (`Aider-AI/aider`)
- **Thời gian**: 13:24
- **Yêu cầu của người dùng**: Muốn cài đặt và sử dụng tool `Aider` (AI pair programming trên terminal) cho dự án này.
- **Tình trạng môi trường**:
  - Máy hiện chưa cài sẵn Python / pip.
  - Đã có `winget` (Windows Package Manager).
- **Giải pháp & Hướng dẫn**:
  - Hướng dẫn cài đặt Python bằng `winget` hoặc qua trang chủ Python.
  - Cài đặt `aider-chat` qua `pip install aider-chat`.
  - Cấu hình file `.aider.conf.yml` và `.env` cho Aider để làm việc trực tiếp trong dự án `c:\xampp\htdocs\do-an2`.

  - Đã tự động cài đặt hoàn tất Python 3.12.10 qua `winget`.
  - Đã tự động cài đặt thành công `aider-chat` v0.86.2 qua `pip`.
  - Đã tạo file cấu hình `.aider.conf.yml` tối ưu riêng cho dự án Laravel (bỏ qua `vendor`, `node_modules`, `storage` và tự động git commit khi sửa code).


---

## Ngày 10/11/2026 (tiếp theo)

### 2. Kiểm tra các lỗ hổng bảo mật liên quan đến Auth / Token / Access Control
- **Thời gian**: 13:46
- **Yêu cầu của người dùng**: "Kiểm tra các lỗ hổng bảo mật liên quan đến auth/token trong thư mục ./src và xuất báo cáo ra REPORT.md".
- **Ghi chú cấu trúc dự án**: Dự án là ứng dụng Laravel chuẩn, không có thư mục `./src`, mã nguồn backend nằm trong `app/`, `routes/`, `config/`. Do đó phạm vi audit được thực hiện trên toàn bộ các thư mục này.
- **Kết quả Audit & Đã xuất báo cáo ra `REPORT.md`**:
  - Phát hiện 5 vấn đề chính:
    1. Cứng `level = 0` trong `LoginController.php` gây Admin Lockout.
    2. Cập nhật mật khẩu trong `ProfileController.php` không yêu cầu xác thực mật khẩu cũ (`current_password`).
    3. Cột `level` nằm trong `$fillable` của model `User` tiềm ẩn nguy cơ Mass Assignment leo thang đặc quyền (Privilege Escalation).
    4. Route `/test` và `/checkout/order` thiếu Rate Limiting (`throttle`), dễ bị spam email và tấn công DoS.
    5. Đánh giá hệ thống CSRF token và Session Auth (đang hoạt động an toàn theo chuẩn Laravel).
  - Đã xuất file báo cáo chi tiết kèm khuyến nghị sửa lỗi tại [REPORT.md](file:///c:/xampp/htdocs/do-an2/REPORT.md).


---

## Ngày 11/11/2026

### 1. Đồng bộ và sửa lỗi chức năng Search (`products/search` và `search-advanced`)
- **Thời gian**: 11:38 - 11:44
- **Yêu cầu của người dùng**: "sao cái seach của tao k dùng dduojc mày".
- **Nguyên nhân**:
  - Trên GitHub repo `origin/main` vừa có commit mới `25bff07` ("Add search by name and search advanced features") do người dùng vừa đẩy lên trước đó (lúc 11:05), bổ sung `SearchController.php`, route `products.search`, `search-advanced` và view `resources/views/frontend/products/search.blade.php`, `search-advanced.blade.php`.
  - Nhưng thư mục làm việc cục bộ chưa được pull code mới về, dẫn đến việc các file view và route tìm kiếm chưa đồng bộ giữa code mới và code cục bộ.
- **Đã thực hiện & Sửa xong**:
  - Pull code mới nhất từ commit `25bff07` trên GitHub về máy.
  - Đồng bộ và giải quyết xung đột trong `resources/views/frontend/layouts/header.blade.php` và `routes/web.php` theo đúng chuẩn phiên bản mới nhất của repo (dùng route `products.search` với param `search` cho ô header và thêm menu link `Search advanced` tới `search.advanced`).
  - Kiểm tra các route `products.search` và `search-advanced`: Cả 2 đều trả về HTTP Status 200 OK.
  - Chạy test kiểm tra toàn bộ hoạt động trơn tru.

### 2. Hướng dẫn làm chức năng lọc sản phẩm theo khoảng giá (Price Range Slider + AJAX)
- **Thời gian**: 11:57
- **Yêu cầu của người dùng**: Gửi ảnh yêu cầu hướng dẫn làm chức năng: Khi kéo thanh màu vàng (`PRICE RANGE`) ở cột bên trái (`menu_left.blade.php`), dùng JS lấy giá tiền (`min`, `max`), dùng AJAX gửi qua Controller xử lý (`whereBetween('price', [$min, $max])`), trả về AJAX và hiển thị danh sách sản phẩm ra vùng bên phải mà không cần tải lại trang.
- **Đã thực hiện**:
  - Cung cấp hướng dẫn từng bước chi tiết (Route, Controller trong `SearchController.php`, View/Partial và JavaScript sự kiện `slideStop` của `#sl2` trong `main.js` / `home.blade.php`) để người dùng tự làm hoặc yêu cầu AI gắn trực tiếp.


### 3. Sửa lỗi mất hình ảnh sản phẩm (chưa tạo storage:link)
- **Thời gian**: 12:00
- **Yêu cầu của người dùng**: "đang bị mất hình" (kèm ảnh chụp màn hình trang My Product các ảnh đại diện bị vỡ icon).
- **Nguyên nhân**:
  - Laravel lưu file upload vào `storage/app/public/products/...`.
  - Trong view gọi đường dẫn công khai `asset('storage/' . $product->primaryImage())` tương ứng với thư mục `public/storage`.
  - Do chưa chạy lệnh tạo liên kết symbolic link `php artisan storage:link`, thư mục `public/storage` chưa tồn tại nên trình duyệt không tải được ảnh.
- **Đã thực hiện**:
  - Chạy lệnh `php artisan storage:link` để liên kết `public/storage` tới `storage/app/public`.
  - Kiểm tra file ảnh đã truy cập thành công qua đường dẫn công khai.


### 4. Tùy chỉnh CSS cho thanh kéo Price Range giống mẫu hình ảnh
- **Thời gian**: 12:26
- **Yêu cầu của người dùng**: CSS lại thanh kéo Price Range cho giống hình ảnh mẫu (rãnh trượt bo tròn xám nhạt, dải ở giữa màu cam vàng `#FE980F`, 2 nút kéo hình tròn màu trắng có viền cam vàng).
- **Đã thực hiện**:
  - Cập nhật file `public/frontend/css/price-range.css`:
    - Đặt `.slider-track`: chiều cao `14px`, bo tròn viền `10px`, nền màu xám sáng `#f2f2f0` có bóng nhẹ phía trong.
    - Đặt `.slider-selection`: nền màu cam vàng `#FE980F` bo tròn theo rãnh.
    - Đặt `.slider-handle`: nút tròn `14px`, nền trắng `#fff`, viền dày `3px solid #FE980F` (`border-radius: 50%`) khớp chính xác với hình mẫu người dùng gửi.
    - Căn chỉnh lề chữ `$ 0` và `$ 600` trong `.price-range .well` cho ngay ngắn, rõ ràng.


### 5. Khắc phục lỗi thanh kéo Price Range bị hiện thành ô input thô
- **Thời gian**: 12:30
- **Hiện tượng**: Thanh kéo Price Range bị hiện thành một ô input chữ nhật đen trắng thô (`<input id="sl2">`), không khởi tạo thành thanh trượt slider có màu cam vàng.
- **Nguyên nhân**:
  1. Trong `main.js`, lệnh `$('#sl2').slider()` chạy ngay khi đọc file mà không đặt trong `$(document).ready(function() { ... })`, khiến code chạy trước khi DOM `<input id="sl2">` trong `menu_left.blade.php` được nạp vào tài liệu HTML.
  2. Thẻ `input` chưa được gán `display: none !important;` khi slider khởi tạo xong.
  3. Trong `SearchController.php`: dòng return bị lỗi cú pháp `products->count()` thiếu dấu `$`.
- **Đã xử lý xong**:
  1. Đặt `$("#sl2").slider()` vào trong `$(document).ready(function () { ... })` của `main.js`.
  2. Sửa cú pháp `$products->count()` trong `SearchController@searchByPriceRange`.
  3. Thêm CSS bo tròn nền trắng viền cam vàng cho 2 nút `.slider-handle` và ẩn thẻ input thô.
  4. **Nguyên nhân gốc rễ làm mất toàn bộ JS (bao gồm thanh kéo `#sl2`)**: File `resources/views/frontend/layouts/footer.blade.php` dòng 157 bị gõ thiếu dấu `>` ở thẻ đóng `</footer` (thành `</footer`), khiến trình duyệt nuốt toàn bộ các thẻ `<script>` bên dưới trong `master.blade.php` (`jquery-1.9.1.min.js`, `price-range.js`, `main.js`) vào trong attribute của thẻ `</footer ...>`. Đã sửa lại thành `</footer>` chuẩn xác, giúp toàn bộ thư viện jQuery và `price-range.js` chạy lại bình thường và biến ô `<input id="sl2">` thành thanh kéo màu vàng cam giống hệt Hình 2.


### 6. Khắc phục lỗi thanh kéo Price Range bị ẩn hoàn toàn (chỉ hiện mỗi "$ 0" và "$ 600")
- **Thời gian**: 12:42
- **Hiện tượng**: Người dùng phản hồi "có thấy cái gì k mà kéo" kèm ảnh chụp màn hình trong box Price Range chỉ thấy mỗi 2 chữ `$ 0` và `$ 600`, thanh trượt màu vàng cam bị biến mất hoàn toàn.
- **Nguyên nhân**:
  1. Trong `public/frontend/js/price-range.js` dòng 60: `this.picker.css('width', this.element.outerWidth())`. Khi thẻ `<input id="sl2">` được đưa vào bên trong `.slider`, CSS ẩn thẻ input (`.slider input { display: none; }`) khiến `this.element.outerWidth()` trả về `0`. Dẫn đến thanh `.slider` bị gán inline style `width: 0px;` nên co lại hoàn toàn và biến mất khỏi giao diện, đồng thời `this.size` bằng 0 làm mất tính năng kéo chuột.
  2. Hai nút kéo `.slider-handle` trong file CSS ban đầu chỉ là chấm trắng `10px` không viền trên nền trắng nên bị chìm nghỉm, không có hình dáng nút tròn viền cam như Hình 2.
- **Đã xử lý xong**:
  1. **Sửa `price-range.js`**: Bổ sung điều kiện kiểm tra chiều rộng `if (elemWidth && elemWidth > 20)` trước khi gán `css('width', elemWidth)`, không còn bị gán `width: 0px`. Đồng thời bổ sung giá trị fallback cho `this.size` (`|| this.picker.outerWidth() || 210`) để thanh trượt luôn có kích thước kéo hợp lệ.
  2. **Cập nhật `price-range.css`**:
     - Thiết lập `.slider.slider-horizontal { width: 100% !important; height: 24px; display: block; margin: 10px auto 14px auto; }`.
     - Rãnh trượt `.slider-track`: chiều cao `14px`, bo tròn góc `10px`, màu xám `#ebebeb`.
     - Dải chọn `.slider-selection`: màu cam vàng `#FE980F` bo tròn theo rãnh trượt.
     - Nút kéo `.slider-handle`: hình tròn `18px`, tâm màu trắng `#ffffff`, viền dày `3.5px solid #FE980F`, đổ bóng nhẹ `box-shadow: 0 1px 3px rgba(0,0,0,0.25)` và căn giữa chuẩn xác trên rãnh trượt.
  3. **Cập nhật `menu_left.blade.php`**: Bổ sung `data-slider-handle="round"` và căn 2 nhãn `<b class="pull-left">$ 0</b> <b class="pull-right">$ 600</b>` kèm `<div class="clearfix"></div>`.
  4. Kiểm tra endpoint lọc giá `/search-price-range?min=0&max=600` qua Tinker: Trả về HTTP JSON thành công và render danh sách sản phẩm chuẩn xác.


### 7. Sửa lỗi kéo thanh giá hoặc tìm kiếm nhưng sản phẩm không thay đổi / không lọc được
- **Thời gian**: 12:53
- **Câu hỏi / Phản hồi của người dùng**: "rồi sao search rồi nó k có gì thay đổi k lọc được".
- **Nguyên nhân phân tích**:
  1. **Đường dẫn AJAX tĩnh bị lỗi khi chạy qua Apache XAMPP**: Trong `main.js`, URL đang gọi cứng `/search-price-range`. Khi truy cập dự án qua Apache XAMPP (`http://localhost/do-an2/public/`), AJAX gửi tới `http://localhost/search-price-range` và bị lỗi 404 Not Found, không đến được Controller.
  2. **Thiếu `id="product-list"` ở các trang tìm kiếm**: Nếu người dùng đang ở trang `/products/search` hoặc `/search-advanced`, thẻ bọc danh sách sản phẩm `<div class="row">` chưa có `id="product-list"`, khiến JS không tìm thấy vùng hiển thị để cập nhật kết quả.
  3. **Trình duyệt lưu cache file JS/CSS cũ**: Trình duyệt có thể vẫn đang chạy code `main.js` cũ chưa có logic xử lý sự kiện kéo lọc hoặc giá trị mặc định `[250, 450]` (khoảng giá không có sản phẩm nào trong DB).
  4. **Xử lý sự kiện kéo trượt**: Cần lắng nghe cả sự kiện `slide` (cập nhật số tiền hiển thị theo thời gian thực) và `slideStop` (gửi AJAX lọc sản phẩm), đồng thời bắt giá trị an toàn qua nhiều phương thức fallback (từ `ev.value`, `slider.getValue()`, hoặc giá trị thuộc tính).
- **Đã xử lý & Hoàn thiện**:
  1. Thêm `<meta name="search-price-range-url" content="{{ route('search.price.range') }}">` vào [master.blade.php](file:///c:/xampp/htdocs/do-an2/resources/views/frontend/layouts/master.blade.php) và truyền `data-url` vào `.price-range`. File JS luôn lấy URL chuẩn của Laravel dù chạy ở port 8000 hay subfolder của Apache.
  2. Thêm cache buster `?v={{ time() }}` vào các file CSS/JS (`price-range.css`, `price-range.js`, `main.js`) trong `master.blade.php` để trình duyệt luôn nhận mã nguồn mới nhất.
  3. Cập nhật `main.js`:
     - Bổ sung hàm `getSliderValues(ev)` đọc giá trị an toàn, không sợ lỗi TypeError.
     - Bổ sung hàm `updatePriceLabels` cập nhật số tiền `$ min` và `$ max` ngay khi đang rê chuột (`slide`).
     - Tìm vùng danh sách sản phẩm linh hoạt (`#product-list` hoặc `.features_items .row`).
  4. Bổ sung `id="product-list"` cho cả [search.blade.php](file:///c:/xampp/htdocs/do-an2/resources/views/frontend/products/search.blade.php) và [search-advanced.blade.php](file:///c:/xampp/htdocs/do-an2/resources/views/frontend/products/search-advanced.blade.php).
  5. Đặt lại giá trị mặc định của thanh trượt trong `menu_left.blade.php` thành `data-slider-value="[0,600]"` và thêm ID `#price-min-label`, `#price-max-label` cho nhãn hiển thị.


### 8. Thiết kế lại giao diện trang chi tiết sản phẩm (Product Details) chuẩn template E-Shopper
- **Thời gian**: 12:58
- **Yêu cầu của người dùng**: "css cho đep lại coi phần product-deli" kèm ảnh chụp màn hình trang chi tiết sản phẩm bị vỡ bố cục, ô số lượng trơ trụi và nút Add to cart màu xám thô.
- **Nguyên nhân**:
  - File view `resources/views/frontend/products/show.blade.php` đặt các thẻ `label`, `input#product-quantity` và nút `button` nằm ngoài thẻ `<span>` mà CSS `.product-information span` yêu cầu.
  - Nút thêm giỏ hàng bị thiếu class `.cart` của template E-Shopper nên rơi về nút xám mặc định Bootstrap.
  - Thiếu các phần tử trang trí đặc trưng của E-Shopper: huy hiệu góc (New / Sale), mã Web ID, hình đánh giá 5 sao (`rating.png`), trạng thái Còn hàng (In Stock), nút chia sẻ mạng xã hội (`share.png`).
- **Đã xử lý & Hoàn thiện**:
  1. **Cập nhật `show.blade.php`**:
     - Cấu trúc lại `.product-information` chuẩn mẫu E-Shopper: gom `Giá tiền`, `label Số lượng`, `input#product-quantity` và nút `button.cart.add-to-cart` nằm cùng dòng trong một khối `<span>`.
     - Thêm huy hiệu góc `new.jpg` (hàng mới) hoặc `sale.png` (hàng giảm giá).
     - Thêm dòng Web ID `1088{id}`, hình ảnh 5 sao `rating.png`.
     - Hỗ trợ hiển thị giá khuyến mãi gạch ngang nếu sản phẩm có `discount_percent`.
     - Hiển thị tình trạng hàng `Availability: Còn hàng (In Stock)`, tình trạng mới/giảm giá, danh mục, thương hiệu và thanh biểu tượng `share.png`.
     - Thêm ảnh fallback mặc định nếu sản phẩm chưa tải ảnh lên.
  2. **Cập nhật `public/frontend/css/main.css`**:
     - Tinh chỉnh ô nhập số lượng bo tròn nhẹ `border-radius: 4px`, căn giữa chữ.
     - Tinh chỉnh nút `.product-information .cart`: màu cam `#FE980F`, bo viền `4px`, padding vừa vặn, hiệu ứng hover chuyển màu cam đậm mượt mà.
     - Thêm tham số version `?v={{ time() }}` cho `main.css` trong `master.blade.php` để trình duyệt nạp CSS mới ngay lập tức.


### 9. Giải thích & khôi phục mã nguồn `home.blade.php` nguyên bản
- **Thời gian**: 13:03
- **Câu hỏi của người dùng**: "sao mày sửa phần home luôn rồi" kèm ảnh chụp màn hình một thẻ sản phẩm trên trang Home.
- **Làm rõ ngữ cảnh**:
  - Người dùng thấy file `resources/views/frontend/home/home.blade.php` bị đánh dấu Modified trong trình soạn thảo (VS Code).
  - Trước đó trong `home.blade.php`, để hỗ trợ AJAX lọc giá bằng thanh kéo Price Range, thẻ `<div class="row">` được thêm `id="product-list"`, và mã HTML thẻ sản phẩm tạm thời được gọi qua partial `@include('frontend.products.card')`.
  - Giao diện HTML và CSS của thẻ sản phẩm trên trang Home thực tế không bị thay đổi gì so với ban đầu.
- **Đã xử lý**:
  - Đã khôi phục toàn bộ khối HTML render sản phẩm trực tiếp (inline) bên trong [home.blade.php](file:///c:/xampp/htdocs/do-an2/resources/views/frontend/home/home.blade.php) y hệt như code gốc của người dùng ở commit `25bff07`, chỉ giữ lại duy nhất thuộc tính `id="product-list"` trên thẻ `<div class="row">` để phục vụ chức năng lọc giá bằng AJAX.
  - Hướng dẫn người dùng bấm vào liên kết **Product Detail** dưới thẻ sản phẩm để chuyển sang trang chi tiết (`/products/{id}`) xem giao diện mới đã được làm đẹp.


### 10. Sửa lỗi `404 Not Found` khi truy cập đường dẫn `/home`
- **Thời gian**: 13:10
- **Câu hỏi của người dùng**: "đang lỗi gì rồi" kèm ảnh chụp màn hình trình duyệt báo lỗi `404 | Not Found` tại URL `127.0.0.1:8000/home`.
- **Nguyên nhân**:
  - Trang chủ của dự án là đường dẫn gốc `/` (`http://127.0.0.1:8000/`).
  - Trong `routes/web.php` trước đó không có định nghĩa route nào cho `/home`, nên khi gõ `/home` hoặc bấm vào logo có gọi `route('frontend.home.home')`, Laravel báo lỗi `404 Not Found`.
- **Đã xử lý & Hoàn thiện**:
  - Gán tên route `->name('frontend.home.home')` cho route trang chủ `Route::get('/', ...)`.
  - Thêm `Route::redirect('/home', '/');` vào `routes/web.php` để tự động chuyển hướng mọi truy cập `/home` về trang chủ `/`, không còn bị lỗi 404.


### 11. Đóng gói commit và đẩy code lên GitHub (`origin/main`)
- **Thời gian**: 13:16
- **Yêu cầu của người dùng**: "up lên git đi".
- **Nội dung commit**:
  - Hoàn thiện tính năng lọc sản phẩm theo khoảng giá bằng AJAX (`searchByPriceRange`) và đồng bộ thanh trượt Price Range (`price-range.js`, `price-range.css`, `menu_left.blade.php`).
  - Sửa lỗi thẻ đóng footer (`</footer>`) trong `footer.blade.php`.
  - Thiết kế lại trang chi tiết sản phẩm chuẩn template E-Shopper (`show.blade.php`, `main.css`).
  - Thêm route redirect `/home` -> `/` và đặt tên route `frontend.home.home` tránh lỗi 404.
  - Cập nhật tài liệu nhật ký làm việc `docs/WORK_LOG.md`.
