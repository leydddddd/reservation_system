<?php
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/../vendor/autoload.php'; // PHPMailer & Dompdf

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// 1. Validate
if (!isset($_GET['booking_id'])) {
    http_response_code(400);
    die("booking_id missing");
}

$booking_id = intval($_GET['booking_id']);

// 2. Fetch booking info
$stmt = $ineedthis->prepare("SELECT name, email, gateway_order_id FROM bookings WHERE id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    http_response_code(404);
    die("Booking not found");
}

$customerEmail = $data['email'];

// Basic email validation
if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(500);
    die('Invalid customer email');
}

// Generate invoice HTML by including the invoice script and capturing output.
// This avoids relying on external HTTP requests.
$invoiceHtml = '';
ob_start();
// Make booking_id available to the included script
$origGet = $_GET;
$_GET['booking_id'] = $booking_id;
try {
    include __DIR__ . '/../pages/invoice.php';
    $invoiceHtml = ob_get_clean();
} catch (\Throwable $e) {
    ob_end_clean();
    $_GET = $origGet;
    http_response_code(500);
    die('Failed to generate invoice HTML: ' . $e->getMessage());
}
$_GET = $origGet;

// 3. Generate PDF
$pdfOutput = null;
try {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($invoiceHtml);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $pdfOutput = $dompdf->output();
} catch (\Throwable $e) {
    http_response_code(500);
    die('PDF generation failed: ' . $e->getMessage());
}

// Save to a secure temp file
try {
    $pdfPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . sprintf('invoice_%d_%s.pdf', $booking_id, bin2hex(random_bytes(6)));
    if (file_put_contents($pdfPath, $pdfOutput) === false) {
        throw new \RuntimeException('Failed to write PDF to disk');
    }
} catch (\Throwable $e) {
    http_response_code(500);
    die('Failed to save PDF: ' . $e->getMessage());
}

// 4. Email PDF to customer
$mail = new PHPMailer(true);
try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    // TODO: move credentials to secure config/env
    $mail->Username   = ;
    $mail->Password   = ;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recommended for debugging only
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    $mail->setFrom('support@tekape.space', 'Tekape Workspace');
    $mail->addAddress($customerEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Invoice Booking - Tekape Workspace';
    $mail->Body    = '<p>Halo, berikut invoice pemesanan Anda. Terima kasih sudah menggunakan Tekape Workspace!</p>';
    $mail->AltBody = 'Halo, berikut invoice pemesanan Anda. Terima kasih sudah menggunakan Tekape Workspace!';
    $mail->addAttachment($pdfPath);

    $sent = $mail->send();
    if (!$sent) {
        throw new \RuntimeException('Mail send failed: ' . $mail->ErrorInfo);
    }

    // Delete temp file after successful send
    @unlink($pdfPath);
    echo 'Invoice sent';
} catch (\Throwable $e) {
    // Keep the PDF for inspection on error
    http_response_code(500);
    echo 'Failed to send invoice: ' . $e->getMessage();
}
