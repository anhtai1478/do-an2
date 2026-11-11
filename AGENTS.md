<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
## Nhật Ký Làm Việc (Work Journal - BẮT BUỘC)

- Trước khi bắt đầu làm việc hoặc trả lời câu hỏi mới trong mỗi phiên, **LUÔN LUÔN** đọc file `docs/WORK_LOG.md` để nắm lịch sử dự án và những việc đã làm trước đó.
- Sau mỗi lần giải đáp câu hỏi hoặc thực hiện thay đổi/sửa lỗi trong dự án, **BẮT BUỘC** cập nhật thêm mục mới vào `docs/WORK_LOG.md` (ghi rõ ngày giờ, câu hỏi/yêu cầu của người dùng, nguyên nhân lỗi nếu có, và chi tiết những gì đã thực hiện).
</laravel-boost-guidelines>
