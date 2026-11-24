<?php
require_once 'includes/functions.php';
requireLogin();
require_once 'includes/db.php';
$userId = (int)$_SESSION['user_id'];
$error = '';
$success = '';
$stmt = $pdo->prepare('SELECT full_name, email, created_at FROM users WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();
if (!$user) { session_destroy(); header('Location: login.php'); exit; }
$name = $user['full_name'];
$email = $user['email'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $error = 'Қауіпсіздік токені жарамсыз. Бетті жаңартып көріңіз.';
    } else {
        $name = trim($_POST['full_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        if ($name === '' || mb_strlen($name) > 100) $error = 'Аты-жөніңіз 1–100 таңба болуы керек.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) $error = 'Email мекенжайын дұрыс енгізіңіз.';
        else {
            $check = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
            $check->execute([':email' => $email, ':id' => $userId]);
            if ($check->fetch()) $error = 'Бұл email басқа аккаунтта қолданылып тұр.';
            else {
                $update = $pdo->prepare('UPDATE users SET full_name = :name, email = :email WHERE id = :id');
                $update->execute([':name' => $name, ':email' => $email, ':id' => $userId]);
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $success = 'Профиль сәтті жаңартылды!';
                $user['created_at'] = $user['created_at'];
            }
        }
    }
}
$pageTitle = 'Менің профилім — Qarjy';
include 'includes/header.php';
?>
<main class="auth">
 <h2>Менің профилім 👤</h2>
 <p>Жеке аккаунтыңыздың мәліметтерін осы жерден өзгерте аласыз.</p>
 <?php if ($error): ?><div class="error"><?=e($error)?></div><?php endif; ?>
 <?php if ($success): ?><div class="success"><?=e($success)?></div><?php endif; ?>
 <form method="post" action="profile.php">
  <input type="hidden" name="csrf_token" value="<?=e(csrfToken())?>">
  <label>Аты-жөніңіз<input type="text" name="full_name" maxlength="100" value="<?=e($name)?>" required></label>
  <label>Email<input type="email" name="email" maxlength="150" value="<?=e($email)?>" required></label>
  <label>Тіркелген күні<input type="text" value="<?=e(date('d.m.Y', strtotime($user['created_at'])))?>" disabled></label>
  <button class="btn full" type="submit">Өзгерістерді сақтау</button>
 </form>
 <p><a href="dashboard.php">← Қаржы панеліне қайту</a></p>
</main>
<?php include 'includes/footer.php'; ?>
