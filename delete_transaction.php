<?php
require_once 'includes/functions.php'; requireLogin(); require_once 'includes/db.php';
if ($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); exit('Әдіс рұқсат етілмейді.'); }
if (!verifyCsrf()) { http_response_code(403); exit('Қауіпсіздік токені жарамсыз.'); }
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$id || $id<1) { http_response_code(400); exit('Операция ID дұрыс емес.'); }
$stmt=$pdo->prepare('DELETE FROM transactions WHERE id=:id AND user_id=:uid');
$stmt->execute([':id'=>$id,':uid'=>(int)$_SESSION['user_id']]);
$_SESSION['qarjy_notice']=$stmt->rowCount()?'Операция жойылды.':'Операция табылмады.';
header('Location: dashboard.php'); exit;
