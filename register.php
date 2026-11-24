<?php
require_once 'includes/functions.php'; require_once 'includes/db.php';
if(!empty($_SESSION['user_id'])){header('Location: dashboard.php');exit;}
$error=''; $name=''; $email='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!verifyCsrf()) $error='Қауіпсіздік токені жарамсыз.';
 else {
  $name=trim($_POST['name']??''); $email=strtolower(trim($_POST['email']??'')); $pass=$_POST['password']??''; $confirm=$_POST['confirm_password']??'';
  if($name===''||mb_strlen($name)>100) $error='Аты-жөніңізді дұрыс енгізіңіз.';
  elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>150) $error='Email дұрыс емес.';
  elseif(strlen($pass)<8) $error='Құпиясөз кемінде 8 таңба болуы керек.';
  elseif($pass!==$confirm) $error='Құпиясөздер сәйкес келмейді.';
  else {
   $q=$pdo->prepare('SELECT id FROM users WHERE email=:email');$q->execute([':email'=>$email]);
   if($q->fetch()) $error='Бұл email бұрын тіркелген.';
   else {$hash=password_hash($pass,PASSWORD_DEFAULT);$q=$pdo->prepare('INSERT INTO users(full_name,email,password) VALUES(:name,:email,:password)');$q->execute([':name'=>$name,':email'=>$email,':password'=>$hash]);header('Location: login.php?registered=1');exit;}
  }
 }
}
$pageTitle='Тіркелу — Qarjy';include 'includes/header.php'; ?>
<div class="auth"><h2>Qarjy-ға тіркелу 💜</h2><p>Жеке қаржыңызды бақылауды бастаңыз.</p><?php if($error):?><div class="error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrfToken())?>"><label>Аты-жөніңіз<input name="name" maxlength="100" value="<?=e($name)?>" required></label><label>Email<input name="email" type="email" maxlength="150" value="<?=e($email)?>" required></label><label>Құпиясөз<input name="password" type="password" minlength="8" required></label><label>Құпиясөзді қайталаңыз<input name="confirm_password" type="password" minlength="8" required></label><button class="btn full">Тіркелу →</button></form><p>Аккаунтыңыз бар ма? <a href="login.php">Кіру</a></p></div>
<?php include 'includes/footer.php'; ?>