<?php session_start(); if(($_SESSION['role']??'')!=='admin'){ header("Location:/login.php"); exit; } ?>
<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><title>Admin Paneli</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body><?php include __DIR__.'/../navbar.php'; ?>
<div class="container mt-3">
  <h3 class="fw-bold">⚙️ Admin Paneli</h3>
  <p class="text-muted">Hoş geldiniz, <strong><?= htmlspecialchars($_SESSION['name']??'Admin') ?></strong></p>
  <div class="alert alert-info">Demo amaçlı minimal panel. Firma & kupon yönetimi kullanıcı tarafında test için hazır.</div>
  <a class="btn btn-secondary" href="/">⬅ Site</a>
</div></body></html>
