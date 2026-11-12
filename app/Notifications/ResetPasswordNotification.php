<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('[E-Shopper] Yêu cầu đặt lại mật khẩu')
            ->greeting('Xin chào '.$notifiable->name.'!')
            ->line('Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại E-Shopper.')
            ->action('Đặt lại mật khẩu', $resetUrl)
            ->line('Liên kết đặt lại mật khẩu này sẽ hết hạn trong vòng 60 phút.')
            ->line('Nếu bạn không gửi yêu cầu đặt lại mật khẩu, bạn có thể yên tâm bỏ qua email này, tài khoản của bạn vẫn an toàn.')
            ->salutation('Trân trọng, Đội ngũ E-Shopper');
    }
}
