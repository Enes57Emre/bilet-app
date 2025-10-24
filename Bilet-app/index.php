<?php
session_start();
require_once __DIR__.'/db/db_connect.php';

$from_city = $_GET['from_city'] ?? '';
$to_city = $_GET['to_city'] ?? '';
$seferler = [];

if (!empty($from_city) && !empty($to_city)) {
  $stmt = $db->prepare("SELECT s.*, f.name AS firm_name FROM seferler s JOIN firms f ON s.firm_id=f.id WHERE s.from_city LIKE ? AND s.to_city LIKE ?");
  $stmt->execute(["%$from_city%", "%$to_city%"]);
  $seferler = $stmt->fetchAll();
}
?>
<!DOCTYPE html><html lang="tr"><head>
<meta charset="UTF-8"><title>Bilet Arama</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f8f9fa}</style>
</head><body>
<?php include __DIR__.'/navbar.php'; ?>
<div class="container">
  <h3 class="mb-4">Otobüs Bileti Ara</h3>
  <form method="GET" class="row g-2 mb-4">
    <div class="col-md-4"><input class="form-control" type="text" name="from_city" placeholder="Kalkış (örn: İstanbul)" value="<?= htmlspecialchars($from_city) ?>" required></div>
    <div class="col-md-4"><input class="form-control" type="text" name="to_city" placeholder="Varış (örn: Ankara)" value="<?= htmlspecialchars($to_city) ?>" required></div>
    <div class="col-md-4"><button class="btn btn-primary w-100">Ara</button></div>
  </form>

  <?php if (!empty($seferler)): ?>
  <div class="card shadow"><div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="table-primary"><tr><th>Firma</th><th>Kalkış</th><th>Varış</th><th>Tarih</th><th>Saat</th><th>Fiyat</th><th>İşlem</th></tr></thead>
        <tbody>
        <?php foreach ($seferler as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['firm_name']) ?></td>
            <td><?= htmlspecialchars($s['from_city']) ?></td>
            <td><?= htmlspecialchars($s['to_city']) ?></td>
            <td><?= htmlspecialchars($s['date']) ?></td>
            <td><?= htmlspecialchars($s['time']) ?></td>
            <td><strong><?= number_format($s['price'],2) ?> ₺</strong></td>
            <td><?php if (isset($_SESSION['user_id'])): ?>
              <a class="btn btn-success btn-sm" href="bilet_al.php?sefer_id=<?= $s['id'] ?>">🎫 Satın Al</a>
            <?php else: ?>
              <a class="btn btn-outline-secondary btn-sm" href="login.php">Giriş Yap</a>
            <?php endif; ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div></div>
  <?php elseif ($from_city && $to_city): ?>
    <div class="alert alert-warning">❌ Bu kriterlere uygun sefer bulunamadı.</div>
  <?php endif; ?>
</div>
</body></html>
