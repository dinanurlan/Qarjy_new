<?php
require_once 'includes/functions.php';
if($_SERVER['REQUEST_METHOD']!=='POST'||!verifyCsrf()){http_response_code(403);exit('Қауіпсіздік тексеруінен өтпеді.');}
$_SESSION=[];if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);}session_destroy();header('Location: index.php');exit;
