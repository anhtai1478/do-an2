<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>{{ $data['subject'] ?? 'Thông tin đơn hàng' }}</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333;">
    <!-- Lời nhắn mở đầu (dùng chung cho cả /test và khi đặt hàng) -->
    <p>{{ $data['body'] ?? '' }}</p>

    <!-- Nếu có giỏ hàng ($data['cart']) truyền sang thì vẽ bảng thông tin mua hàng -->
    @if (!empty($data['cart']) && isset($data['user']))
        <h3>Thông tin khách hàng:</h3>
        <ul>
            <li><strong>Họ tên:</strong> {{ $data['user']->name }}</li>
            <li><strong>Email:</strong> {{ $data['user']->email }}</li>
            <li><strong>Số điện thoại:</strong> {{ $data['user']->phone ?? 'Chưa cập nhật' }}</li>
        </ul>

        <h3>Chi tiết đơn hàng:</h3>
        <table border="1" cellpadding="10" cellspacing="0"
            style="border-collapse: collapse; width: 100%; max-width: 700px;">
            <thead>
                <tr style="background-color: #FE980F; color: white; text-align: left;">
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['cart'] as $item)
                    <tr>
                        <td>{{ $item['name'] }} (ID: {{ $item['id'] }})</td>
                        <td>${{ number_format((float) $item['price'], 2) }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>${{ number_format((float) $item['price'] * (int) $item['quantity'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" align="right"><strong>Cart Sub Total:</strong></td>
                    <td>${{ number_format($data['subTotal'], 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" align="right"><strong>Eco Tax:</strong></td>
                    <td>${{ number_format($data['ecoTax'], 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" align="right"><strong>Total:</strong></td>
                    <td><strong style="color: #FE980F;">${{ number_format($data['grandTotal'], 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    @endif
</body>

</html>