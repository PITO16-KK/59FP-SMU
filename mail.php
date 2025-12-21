<?php
require __DIR__ . '/PHPMailer/PHPMailer.php';
require __DIR__ . '/PHPMailer/SMTP.php';
require __DIR__ . '/PHPMailer/Exception.php';

function env($key, $default = null)
{
    $env = @parse_ini_file(__DIR__ . '/.env');
    return $env[$key] ?? $default;
}

function sendActivationEmail($to_email, $activation_code)
{
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = env('EMAIL_USER');
        $mail->Password = env('EMAIL_PASS');
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom(env('EMAIL_USER'), 'Sistem Monitoring Udara');
        $mail->addAddress($to_email);

        $mail->isHTML(true);
        $mail->Subject = 'Aktivasi Akun';
        $mail->Body = "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        </head>
        <body style='font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f4f4;'>

        <div style='max-width:600px; margin:30px auto; background:white; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,.15);'>

            <div style='background:linear-gradient(135deg,#4e54c8,#8f94fb); padding:20px; text-align:center; color:white;'>
                <h2 style='margin:0; font-weight:600;'>Sistem Monitoring Kualitas Udara</h2>
                <p style='margin-top:6px; opacity:.9;'>Aktivasi Akun Anda</p>
            </div>

            <div style='padding:25px; text-align:center;'>
                <p style='font-size:16px; color:#444;'>Terima kasih telah mendaftar!<br>Silakan klik tombol di bawah untuk mengaktifkan akun Anda.</p>
                
                <a href='http://localhost:8000/activate?code=$activation_code' 
                style='display:inline-block; margin-top:20px; padding:14px 35px; background:#4e54c8; color:white; text-decoration:none; font-size:16px; border-radius:50px; font-weight:bold;'>
                    Aktivasi Sekarang
                </a>

                <p style='font-size:14px; color:#666; margin-top:25px;'>
                    Jika tombol tidak berfungsi, salin link berikut ke browser Anda:
                </p>

                <p style='font-size:13px; color:#4e54c8; word-break:break-all;'>
                    http://localhost:8000/activate?code=$activation_code
                </p>
            </div>

            <div style='background:#f4f4f4; padding:15px; text-align:center; font-size:12px; color:#888;'>
                © 2025 Sistem Monitoring Udara — Indonesia
            </div>

        </div>

        </body>
        </html>";

        return $mail->send();
    } catch (PHPMailer\PHPMailer\Exception $e) {
        return false;
    }
}

function sendNotif($to_email, $aqi, $status, $location)
{
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = env('EMAIL_USER');
        $mail->Password = env('EMAIL_PASS');
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom(env('EMAIL_USER'), 'Sistem Monitoring Udara');
        $mail->addAddress($to_email);

        $mail->isHTML(true);
        $mail->Subject = "Peringatan Kualitas Udara - $location";

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; background:#f7f7f7; padding:20px'>
            <div style='max-width:600px; margin:auto; background:white; border-radius:10px; padding:25px;'>
                <h2 style='text-align:center; color:#2b2d42;'>🚨 Peringatan Kualitas Udara</h2>
                <p style='font-size:15px; color:#333; text-align:center;'>
                    Berikut informasi terbaru kualitas udara di lokasi Anda.
                </p>

                <div style='text-align:center; margin:25px 0;'>
                    <div style='font-size:42px; font-weight:bold; color:#d90429;'>$aqi</div>
                    <div style='font-size:18px; font-weight:600; margin-top:5px;'>Status: $status</div>
                    <div style='font-size:16px; color:#6c757d; margin-top:5px;'>Lokasi: <b>$location</b></div>
                </div>

                <p style='color:#555; font-size:14px; line-height:1.6'>
                    Tingkat polusi udara saat ini berpotensi berdampak pada kesehatan. 
                    Disarankan untuk menggunakan masker, membatasi aktivitas luar ruangan, 
                    dan menjaga kondisi tubuh.
                </p>

                <div style='text-align:center; margin-top:30px;'>
                    <a href='http://localhost/sistem_monitoring_udara/' 
                        style='display:inline-block; padding:12px 20px; background:#2b2d42;
                        color:#fff; text-decoration:none; border-radius:8px; font-size:15px;'>
                        Lihat Dashboard
                    </a>
                </div>

                <p style='text-align:center; font-size:12px; color:#777; margin-top:20px;'>
                    Email ini dikirim otomatis oleh Sistem Monitoring Udara UNSIKA.
                </p>
            </div>
        </div>
        ";

        return $mail->send();
    } catch (PHPMailer\PHPMailer\Exception $e) {
        return false;
    }
}
