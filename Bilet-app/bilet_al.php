<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location:/login.php"); exit; }
require_once __DIR__.'/db/db_connect.php';

if (!isset($_GET['sefer_id'])) die("Sefer ID yok");
$sefer_id=(int)$_GET['sefer_id'];

$stmt=$db->prepare("SELECT s.*, f.name AS firm_name FROM seferler s JOIN firms f ON f.id=s.firm_id WHERE s.id=?");
$stmt->execute([$sefer_id]); $s=$stmt->fetch();
if (!$s) die("Sefer bulunamadı");

$stmt=$db->prepare("SELECT seat_no FROM biletler WHERE sefer_id=? AND status='aktif'");
$stmt->execute([$sefer_id]); $dolu=array_column($stmt->fetchAll(), 'seat_no');

$msg=''; $price=$s['price']; $final=$price; $kupon=null;

if ($_SERVER['REQUEST_METHOD']==='POST'){
  $seat=(int)$_POST['seat_no']; $code=trim($_POST['coupon_code'] ?? '');
  if (in_array($seat, $dolu)) { $msg="❌ Bu koltuk dolu!"; }
  else {
    if ($code!==''){
      $q=$db->prepare("SELECT * FROM coupons WHERE code=? AND usage_limit>0 AND valid_until>=CURDATE() AND (firm_id IS NULL OR firm_id=?)");
      $q->execute([$code, $s['firm_id']]); $kupon=$q->fetch();
      if ($kupon){ $final=$price - ($price*$kupon['discount_rate']/100); } else { $msg="⚠️ Kupon geçersiz"; }
    }
    $bal=$db->prepare("SELECT balance FROM users WHERE id=?"); $bal->execute([$_SESSION['user_id']]); $balance=$bal->fetchColumn();
    if ($balance < $final){ $msg="❌ Yetersiz bakiye!"; }
    else {
      $db->prepare("INSERT INTO biletler(user_id,sefer_id,seat_no,price_paid,coupon_code) VALUES (?,?,?,?,?)")
         ->execute([$_SESSION['user_id'],$sefer_id,$seat,$final, $kupon? $code: NULL]);
      $db->prepare("UPDATE users SET balance=balance-? WHERE id=?")->execute([$final,$_SESSION['user_id']]);
      if ($kupon){ $db->prepare("UPDATE coupons SET usage_limit=usage_limit-1 WHERE id=?")->execute([$kupon['id']]); }
      header("Location:/biletlerim.php"); exit;
    }
  }
}
?>
<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><title>Bilet Satın Al</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>.seat-grid{display:flex;flex-wrap:wrap;max-width:260px;gap:8px}.seat-btn{width:50px;height:50px;border:none;border-radius:6px;color:#fff;font-weight:700}.bos{background:#198754}.dolu{background:#dc3545}.selected{background:#ffc107!important;color:#000!important}</style>
</head><body><?php include __DIR__.'/navbar.php'; ?>
<div class="container"><div class="card shadow p-4">
<h3 class="mb-2">🎫 Bilet Satın Al</h3>
<h5><?= htmlspecialchars($s['firm_name']) ?></h5>
<p><strong><?= htmlspecialchars($s['from_city']) ?> → <?= htmlspecialchars($s['to_city']) ?></strong></p>
<p>🗓 <?= $s['date'] ?> — 🕘 <?= $s['time'] ?></p>
<p class="fw-bold text-success">Fiyat: <?= number_format($price,2) ?> ₺</p>
<?php if($msg): ?><div class="alert alert-warning text-center"><?= $msg ?></div><?php endif; ?>
<form method="POST">
  <h5 class="mt-3">Koltuk Seçin</h5>
  <div class="seat-grid mb-3">
    <?php for($i=1; $i<=$s['total_seats']; $i++): $full=in_array($i,$dolu); ?>
      <button type="button" class="seat-btn <?= $full?'dolu':'bos' ?>" <?= $full?'disabled':"onclick='sel(this,$i)'" ?>><?= $i ?></button>
    <?php endfor; ?>
  </div>
  <input type="hidden" name="seat_no" id="seat_no" required>
  <div class="row g-2 align-items-end">
    <div class="col-md-4">
      <label class="form-label">Kupon Kodu</label>
      <input type="text" name="coupon_code" class="form-control">
    </div>
    <div class="col-md-4"><button class="btn btn-primary w-100">Satın Al</button></div>
  </div>
</form>
<a href="/" class="btn btn-secondary mt-3">⬅ Geri</a>
</div></div>
<script>
function sel(el,n){ document.querySelectorAll('.seat-btn.bos').forEach(b=>b.classList.remove('selected')); el.classList.add('selected'); document.getElementById('seat_no').value=n; }
</script>
</body></html>
