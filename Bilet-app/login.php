<?php
session_start();
require_once __DIR__.'/db/db_connect.php';
$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $email=trim($_POST['email']); $password=$_POST['password'];
  $stmt=$db->prepare("SELECT * FROM users WHERE email=?"); $stmt->execute([$email]);
  $u=$stmt->fetch();
  if ($u && (password_verify($password,$u['password']) || $u['password']===$password)){
    $_SESSION['user_id']=$u['id']; $_SESSION['role']=$u['role']; $_SESSION['name']=$u['name']; $_SESSION['firm_id']=$u['firm_id'];
    if ($u['role']==='admin') header("Location: /admin/dashboard.php");
    elseif ($u['role']==='firma_admin') header("Location: /firma_admin/dashboard.php");
    else header("Location: /");
    exit;
  } else { $msg='❌ Hatalı email veya şifre'; }
}
?>
<!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><title>Giriş Yap</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body><?php include __DIR__.'/navbar.php'; ?>
<div class="container"><div class="card shadow" style="max-width:420px;margin:40px auto"><div class="card-body">
<h4 class="text-center mb-3">Giriş Yap</h4>
<?php if($msg): ?><div class="alert alert-danger text-center p-2"><?= $msg ?></div><?php endif; ?>
<form method="POST">
  <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Şifre</label><input type="password" name="password" class="form-control" required></div>
  <button class="btn btn-primary w-100">Giriş</button>
</form>
<p class="text-center mt-2">Hesabın yok mu? <a href="register.php">Kayıt Ol</a></p>
</div></div></div></body></html>
