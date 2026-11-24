<?php
require_once 'includes/functions.php'; requireLogin(); require_once 'includes/db.php';
$error=''; $types=['expense','income']; $categories=['Тамақ','Көлік','Сауда','Денсаулық','Ойын-сауық','Жалақы','Басқа']; $walletNames=['Kaspi','Halyk','Қолма-қол'];
if ($_SERVER['REQUEST_METHOD']==='POST') {
 if (!verifyCsrf()) $error='Қауіпсіздік токені жарамсыз.';
 else {
  $type=$_POST['type']??''; $raw=$_POST['amount']??''; $cat=trim($_POST['category']??''); $wallet=$_POST['wallet']??''; $desc=trim($_POST['description']??''); $date=$_POST['transaction_date']??date('Y-m-d');
  $dt=DateTime::createFromFormat('Y-m-d',$date);
  if (!in_array($type,$types,true) || !is_numeric($raw) || (float)$raw<=0 || (float)$raw>9999999999) $error='Операция түрін және соманы дұрыс енгізіңіз.';
  elseif (!in_array($cat,$categories,true) || !in_array($wallet,$walletNames,true)) $error='Санат немесе әмиян дұрыс таңдалмаған.';
  elseif (!$dt || $dt->format('Y-m-d')!==$date) $error='Күнді дұрыс енгізіңіз.';
  else {
   $sql='INSERT INTO transactions (user_id,type,amount,category,wallet,description,transaction_date) VALUES (:uid,:type,:amount,:category,:wallet,:description,:date)';
   $stmt=$pdo->prepare($sql);
   $stmt->execute([':uid'=>(int)$_SESSION['user_id'],':type'=>$type,':amount'=>(float)$raw,':category'=>$cat,':wallet'=>$wallet,':description'=>$desc,':date'=>$date]);
   $_SESSION['qarjy_notice']='Жаңа операция дерекқорға сақталды!';
   header('Location: dashboard.php'); exit;
  }
 }
}
$pageTitle='Операция қосу — Qarjy'; include 'includes/header.php';
?>
<div class="auth"><a href="dashboard.php">← Қаржы панелі</a><h2>Операция қосу</h2>
<?php if($error): ?><div class="error"><?=e($error)?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?=e(csrfToken())?>">
<label>Түрі<select name="type"><option value="expense">Шығын</option><option value="income">Кіріс</option></select></label>
<label>Сома (₸)<input type="number" name="amount" min="0.01" step="0.01" required></label>
<label>Санат<select name="category"><?php foreach($categories as $c): ?><option value="<?=e($c)?>"><?=e($c)?></option><?php endforeach; ?></select></label>
<label>Әмиян<select name="wallet"><?php foreach($walletNames as $w): ?><option value="<?=e($w)?>"><?=e($w)?></option><?php endforeach; ?></select></label>
<label>Күні<input type="date" name="transaction_date" value="<?=e(date('Y-m-d'))?>" required></label>
<label>Сипаттама<input name="description" maxlength="150" placeholder="Мысалы: Азық-түлік"></label>
<button class="btn full">Сақтау ✓</button></form></div>
<?php include 'includes/footer.php'; ?>
