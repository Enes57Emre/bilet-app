<?php
session_start();
require_once __DIR__.'/db/db_connect.php';
$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $name=trim($_POST['name']); $email=trim($_POST['email']); $pass=password_hash($_POST['password'], PASSWORD_BCRYPT);
  try{
    $db->prepare("INSERT INTO users(name,email,password,role,balance) VALUES (?,?,?,?,500)")->execute([$name,$email,$pass,'user']);
    $msg='✅ Kayıt başarılı! Giriş yapabilirsiniz.';
  }catch(PDOException $e){ $msg='❌ Bu email zaten kayıtlı!'; }
}
?>
<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><title>Kayıt Ol</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body><?php include __DIR__.'/navbar.php'; ?>
<div class="container"><div class="card shadow" style="max-width:460px;margin:40px auto"><div class="card-body">
<h4 class="text-center mb-3">Kayıt Ol</h4>
<?php if($msg): ?><div class="alert <?= strpos($msg,'✅')!==false?'alert-success':'alert-danger' ?> text-center p-2"><?= $msg ?></div><?php endif; ?>
<form method="POST">
  <div class="mb-3"><label class="form-label">Ad Soyad</label><input type="text" name="name" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Şifre</label><input type="password" name="password" class="form-control" required></div>
  <button class="btn btn-success w-100">Kayıt Ol</button>
</form>
</div></div></div></body></html>
