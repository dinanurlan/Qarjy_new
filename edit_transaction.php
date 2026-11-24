<?php
require_once 'includes/functions.php'; requireLogin(); require_once 'includes/db.php';
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT); $uid=(int)$_SESSION['user_id'];
if (!$id || $id<1) { http_response_code(400); exit('Операция ID дұрыс емес.'); }
$stmt=$pdo->prepare('SELECT * FROM transactions WHERE id=:id AND user_id=:uid LIMIT 1');
$stmt->execute([':id'=>$id,':uid'=>$uid]); $tx=$stmt->fetch();
if (!$tx) { http_response_code(404); exit('Операция табылмады.'); }
$error=''; $types=['expense','income']; $categories=['Тамақ','Көлік','Сауда','Денсаулық','Ойын-сауық','Жалақы','Басқа']; $walletNames=['Kaspi','Halyk','Қолма-қол'];
if ($_SERVER['REQUEST_METHOD']==='POST') {
 if (!verifyCsrf()) $error='Қауіпсіздік токені жарамсыз.';
 else {
  $type=$_POST['type']??''; $raw=$_POST['amount']??''; $cat=trim($_POST['category']??''); $wallet=$_POST['wallet']??''; $desc=trim($_POST['description']??''); $date=$_POST['transaction_date']??'';
  $dt=DateTime::createFromFormat('Y-m-d',$date);
  if (!in_array($type,$types,true)||!is_numeric($raw)||(float)$raw<=0||(float)$raw>9999999999) $error='Операция түрін және соманы дұрыс енгізіңіз.';
  elseif (!in_array($cat,$categories,true)||!in_array($wallet,$walletNames,true)) $error='Санат немесе әмиян дұрыс таңдалмаған.';
  elseif (!$dt||$dt->format('Y-m-d')!==$date) $error='Күнді дұрыс енгізіңіз.';
  else {
   $update=$pdo->prepare('UPDATE transactions SET type=:type, amount=:amount, category=:category, wallet=:wallet, description=:description, transaction_date=:date WHERE id=:id AND user_id=:uid');
   $update->execute([':type'=>$type,':amount'=>(float)$raw,':category'=>$cat,':wallet'=>$wallet,':description'=>$desc,':date'=>$date,':id'=>$id,':uid'=>$uid]);
   $_SESSION['qarjy_notice']='Операция сәтті жаңартылды!';
   header('Location: dashboard.php'); exit;
  }
 }
}
$pageTitle='Операцияны өңдеу — Qarjy'; include 'includes/header.php';
?>
<div class="auth"><a href="dashboard.php">← Қаржы панелі</a><h2>Операцияны өңдеу</h2>
<?php if($error): ?><div class="error"><?=e($error)?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?=e(csrfToken())?>">
<label>Түрі<select name="type"><option value="expense" <?=$tx['type']==='expense'?'selected':''?>>Шығын</option><option value="income" <?=$tx['type']==='income'?'selected':''?>>Кіріс</option></select></label>
<label>Сома (₸)<input type="number" name="amount" min="0.01" step="0.01" value="<?=e($tx['amount'])?>" required></label>
<label>Санат<select name="category"><?php foreach($categories as $c): ?><option value="<?=e($c)?>" <?=$tx['category']===$c?'selected':''?>><?=e($c)?></option><?php endforeach; ?></select></label>
<label>Әмиян<select name="wallet"><?php foreach($walletNames as $w): ?><option value="<?=e($w)?>" <?=$tx['wallet']===$w?'selected':''?>><?=e($w)?></option><?php endforeach; ?></select></label>
<label>Күні<input type="date" name="transaction_date" value="<?=e($tx['transaction_date'])?>" required></label>
<label>Сипаттама<input name="description" maxlength="150" value="<?=e($tx['description'])?>"></label>
<button class="btn full">Өзгерістерді сақтау ✓</button></form></div>
<?php include 'includes/footer.php'; ?>
