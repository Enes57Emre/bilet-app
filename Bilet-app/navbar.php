<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="/index.php">🚍 Bilet Platformu</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="/biletlerim.php">🎫 Biletlerim</a></li>
          <?php if ($_SESSION['role']==='admin'): ?>
            <li class="nav-item"><a class="nav-link" href="/admin/dashboard.php">⚙️ Admin</a></li>
          <?php endif; ?>
          <?php if ($_SESSION['role']==='firma_admin'): ?>
            <li class="nav-item"><a class="nav-link" href="/firma_admin/dashboard.php">🏢 Firma Paneli</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="/logout.php">🚪 Çıkış</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/login.php">Giriş</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
