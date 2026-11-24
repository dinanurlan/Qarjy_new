<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function requireLogin(): void { if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; } }
function csrfToken(): string { if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32)); return $_SESSION['csrf_token']; }
function verifyCsrf(): bool { return isset($_SESSION['csrf_token'],$_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'],$_POST['csrf_token']); }
function money($v): string { return '₸ '.number_format((float)$v,0,'.',' '); }
