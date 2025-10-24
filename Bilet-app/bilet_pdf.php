<?php
require_once __DIR__.'/vendor/autoload.php';
use Dompdf\Dompdf;

session_start();
if (!isset($_GET['id'])) die('Bilet ID yok');
require_once __DIR__.'/db/db_connect.php';

$stmt=$db->prepare("SELECT b.*, u.name AS user_name, s.from_city, s.to_city, s.date, s.time, f.name AS firm_name
  FROM biletler b
  JOIN users u ON u.id=b.user_id
  JOIN seferler s ON s.id=b.sefer_id
  JOIN firms f ON f.id=s.firm_id
  WHERE b.id=?");
$stmt->execute([ (int)$_GET['id'] ]);
$ticket=$stmt->fetch();
if(!$ticket) die('Bilet bulunamadı');

$html = '<h2 style="text-align:center">Bilet Bilgisi</h2>
<table width="100%" border="1" cellspacing="0" cellpadding="8">
<tr><td>Yolcu</td><td>'.htmlspecialchars($ticket['user_name']).'</td></tr>
<tr><td>Firma</td><td>'.htmlspecialchars($ticket['firm_name']).'</td></tr>
<tr><td>Rota</td><td>'.htmlspecialchars($ticket['from_city']).' → '.htmlspecialchars($ticket['to_city']).'</td></tr>
<tr><td>Tarih/Saat</td><td>'.$ticket['date'].' '.$ticket['time'].'</td></tr>
<tr><td>Koltuk</td><td>'.$ticket['seat_no'].'</td></tr>
<tr><td>Ödenen</td><td>'.number_format($ticket['price_paid'],2).' ₺</td></tr>
<tr><td>Durum</td><td>'.$ticket['status'].'</td></tr>
</table>
<p style="text-align:center;margin-top:20px">İyi yolculuklar dileriz! 🚍</p>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('bilet-'.$ticket['id'].'.pdf', ['Attachment' => true]);
