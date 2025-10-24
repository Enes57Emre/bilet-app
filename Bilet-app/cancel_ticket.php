<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location:/login.php"); exit; }
require_once __DIR__.'/db/db_connect.php';

$user_id = $_SESSION['user_id'];
$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($ticket_id <= 0) { header("Location:/biletlerim.php?msg=" . urlencode("Geçersiz bilet.")); exit; }

// Fetch ticket with sefer info and ownership
$sql = "SELECT b.id, b.user_id, b.sefer_id, b.price_paid, b.status, s.date, s.time
        FROM biletler b
        JOIN seferler s ON s.id = b.sefer_id
        WHERE b.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$ticket_id]);
$b = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$b) { header("Location:/biletlerim.php?msg=" . urlencode("Bilet bulunamadı.")); exit; }
if ((int)$b['user_id'] !== (int)$user_id) { header("Location:/biletlerim.php?msg=" . urlencode("Bu bileti iptal etme yetkiniz yok.")); exit; }
if ($b['status'] !== 'aktif') { header("Location:/biletlerim.php?msg=" . urlencode("Bilet zaten iptal edilmiş.")); exit; }

// Compose sefer datetime (assuming s.date=DATE, s.time=TIME, server timezone is correct)
$seferDateTime = new DateTime($b['date'] . ' ' . $b['time']);
$now = new DateTime();

$diffMinutes = ($seferDateTime->getTimestamp() - $now->getTimestamp()) / 60;

// Rule: cannot cancel if less than 60 minutes remain
if ($diffMinutes < 60) {
    header("Location:/biletlerim.php?msg=" . urlencode("Kalkışa 1 saatten az kaldığı için iptal edilemez."));
    exit;
}

// Proceed: mark as canceled and refund
$db->beginTransaction();
try {
    // Update ticket status
    $upd = $db->prepare("UPDATE biletler SET status='iptal', seat_no=NULL WHERE id=?");
    $upd->execute([$ticket_id]);

    // Refund to user's balance
    $ref = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $ref->execute([$b['price_paid'], $user_id]);

    $db->commit();
    header("Location:/biletlerim.php?msg=" . urlencode("Bilet iptal edildi. " . number_format($b['price_paid'],2,',','.') . " ₺ bakiyenize iade edildi."));
    exit;
} catch (Exception $e) {
    $db->rollBack();
    header("Location:/biletlerim.php?msg=" . urlencode("İşlem sırasında hata oluştu."));
    exit;
}
