<?php
require_once 'includes/functions.php';require_once 'includes/db.php';
if(!empty($_SESSION['user_id'])){header('Location: dashboard.php');exit;}
$error='';$email='';$success=isset($_GET['registered'])?'Тіркелу сәтті аяқталды. Енді кіріңіз.':'';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!verifyCsrf())$error='Қауіпсіздік токені жарамсыз.';
 else{$email=strtolower(trim($_POST['email']??''));$pass=$_POST['password']??'';
  if(!filter_var($email,FILTER_VALIDATE_EMAIL))$error='Email дұрыс емес.';
  else{$q=$pdo->prepare('SELECT id,full_name,email,password FROM users WHERE email=:email LIMIT 1');$q->execute([':email'=>$email]);$user=$q->fetch();
   if($user&&password_verify($pass,$user['password'])){session_regenerate_id(true);$_SESSION['csrf_token']=bin2hex(random_bytes(32));$_SESSION['user_id']=(int)$user['id'];$_SESSION['user_name']=$user['full_name'];$_SESSION['user_email']=$user['email'];$_SESSION['qarjy_transactions']=$_SESSION['qarjy_transactions']??[];$_SESSION['qarjy_wallets']=$_SESSION['qarjy_wallets']??['Kaspi'=>125000,'Halyk'=>85000,'Қолма-қол'=>15000];header('Location: dashboard.php');exit;}
   else $error='Email немесе құпиясөз қате.';
  }
 }
}
$pageTitle='Кіру — Qarjy';include 'includes/header.php';?>
<div class="auth"><h2>Қош келдіңіз! 👋</h2><p>Qarjy аккаунтыңызға кіріңіз.</p><?php if($success):?><div class="success"><?=e($success)?></div><?php endif;?><?php if($error):?><div class="error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrfToken())?>"><label>Email<input type="email" name="email" value="<?=e($email)?>" required></label><label>Құпиясөз<input type="password" name="password" required></label><button class="btn full">Жүйеге кіру →</button></form><p>Аккаунтыңыз жоқ па? <a href="register.php">Тіркелу</a></p></div>
<?php include 'includes/footer.php'; ?>