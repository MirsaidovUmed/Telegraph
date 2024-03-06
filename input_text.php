<?php

use App\Entities\TelegraphText;
use App\Entities\FileStorage;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'Entities\TelegraphText.php';
require_once 'Entities\FileStorage.php';
require_once 'vendor\autoload.php';

$message = '';
$success = false;

if (isset($_POST['author']) && isset($_POST['text'])) {
    $telegraphText = new TelegraphText($_POST['author'], $_POST['text']);
    $storage = new FileStorage();
}

$mail = new PHPMailer(true);
if (!empty($_POST['email'])) {
    try {
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Host       = 'smtp.mail.ru';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mail@mail.ru';
        $mail->Password   = 'password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('mail@mail.ru');
        $mail->addAddress($_POST['email']);

        $mail->Subject = $_POST['text'];
        $mail->Body = $_POST['text'];

        $mail->send();
        $success = true;
        $message = 'Письмо успешно отправлено';
    } catch (Exception $e) {
        $message = 'Ошибка при отправке письма: ' . $mail->ErrorInfo;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Отправить текст в «Телеграф»</title>
</head>
<body>
<form action="input_text.php" method="post">
    <label for="author">Автор:</label><br>
    <input type="text" id="author" name="author"><br>
    <label for="text">Текст:</label><br>
    <textarea id="text" name="text"></textarea><br>
    <label for="email">Email:</label><br>
    <input type="text" id="email" name="email"><br>
    <input type="submit" value="Отправить">
</form>
</body>
</html>