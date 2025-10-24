<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location:/login.php"); exit; }
require_once __DIR__.'/db/db_connect.php';
$user_id=$_SESSION['user_id'];
$stmt=$db->prepare("SELECT b.*, s.from_city, s.to_city, s.date, s.time, f.name AS firm_name FROM biletler b JOIN seferler s ON b.sefer_id=s.id JOIN firms f ON s.firm_id=f.id WHERE b.user_id=? ORDER BY b.id DESC");
$stmt->execute([$user_id]); $rows=$stmt->fetchAll();
?>
<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><title>Biletlerim</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body><?php include __DIR__.'/navbar.php'; ?>
<div class="container mt-3">
  <div class="card shadow"><div class="card-body"><?php if(isset($_GET['msg'])): ?><div class='alert alert-info'><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
    <h4 class="mb-3">🎫 Biletlerim</h4>
    <?php if($rows): ?>
    <div class="table-responsive"><table class="table table-striped align-middle">
      <thead class="table-primary"><tr><th>Firma</th><th>Rota</th><th>Tarih</th><th>Koltuk</th><th>Tutar</th><th>Durum</th><th>İşlemler</th></tr></thead>
      <tbody>
      <?php foreach($rows as $b): ?>
        <tr>
          <td><?= htmlspecialchars($b['firm_name']) ?></td>
          <td><?= htmlspecialchars($b['from_city']) ?> → <?= htmlspecialchars($b['to_city']) ?></td>
          <td><?= $b['date'] ?> <?= $b['time'] ?></td>
          <td><?= $b['seat_no'] ?></td>
          <td><strong><?= number_format($b['price_paid'],2) ?> ₺</strong></td>
          <td><?= $b['status']=='aktif' ? "<span class='badge bg-success'>Aktif</span>" : "<span class='badge bg-danger'>İptal</span>" ?></td>
          <td>
            <?php if($b['status']=='aktif'): ?>
              <a class="btn btn-outline-secondary btn-sm" href="/bilet_pdf.php?id=<?= $b['id'] ?>">📄 PDF</a>
            <?php endif; ?>
          </td>
          <td>
              <?php if($b['status']=='aktif'): ?>
                <a class="btn btn-outline-danger btn-sm" href="/cancel_ticket.php?id=<?= $b['id'] ?>" onclick="return confirm('Bu bileti iptal etmek istediğinize emin misiniz?');">❌ İptal Et</a>
              <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <?php else: ?><div class="alert alert-warning text-center">Henüz biletiniz yok.</div><?php endif; ?>
  </div></div>
</div>
</body></html>
