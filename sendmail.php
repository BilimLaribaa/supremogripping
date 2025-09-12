<?php
// Force JSON output only
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);
ob_clean();

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email   = htmlspecialchars(trim($_POST['mail'] ?? ''));
    $comment = htmlspecialchars(trim($_POST['comment'] ?? ''));

    // Validation
    if (empty($name) || empty($email) || empty($comment)) {
        $response['message'] = 'All fields are required.';
        echo json_encode($response);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP Config
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '';   // Your Gmail
        $mail->Password   = '';         // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender
        $mail->setFrom('khan123personal@gmail.com', 'Website Contact Form');
        // Receiver
        $mail->addAddress('helloaak065@gmail.com');
        // Reply-To (visitor)
        $mail->addReplyTo($email, $name);

        // Message
        $mail->isHTML(false);
        $mail->Subject = "New Contact Form Submission from $name";
        $mail->Body    = "You have a new enquiry:\n\n"
            . "Name: $name\n"
            . "Email: $email\n"
            . "Comment:\n$comment\n";

        // Send
        if ($mail->send()) {
            $response['success'] = true;
            $response['message'] = 'Your enquiry has been sent successfully.';
        } else {
            $response['message'] = 'Mailer Error: ' . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        $response['message'] = 'Mailer Exception: ' . $mail->ErrorInfo;
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
