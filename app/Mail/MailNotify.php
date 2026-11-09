<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailNotify extends Mailable
{
    use Queueable, SerializesModels;

    // 1. Biến $data dùng để chứa mọi dữ liệu (tiêu đề, nội dung, giỏ hàng...) truyền từ Controller qua
    private $data = [];

    /**
     * 2. Hàm khởi tạo (__construct): Khi bên Controller gọi new MailNotify($data),
     * dữ liệu $data sẽ được nhận vào đây và gán vào $this->data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * 3. Hàm build(): Cấu hình bức thư trước khi gửi đi
     * - from(): Địa chỉ email và tên người gửi
     * - subject(): Tiêu đề của email (lấy từ $this->data['subject'])
     * - view('emails.index'): Chỉ định file giao diện HTML của bức thư nằm ở resources/views/emails/index.blade.php
     * - with('data', $this->data): Truyền biến $data sang file giao diện emails/index.blade.php để hiển thị
     */
    public function build()
    {
        return $this->from(config('mail.from.address', 'thienbaoit@gmail.com'), 'E-Shopper')
            ->subject($this->data['subject'])
            ->view('emails.index')
            ->with('data', $this->data);
    }
}
