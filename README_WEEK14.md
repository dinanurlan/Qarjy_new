Qarjy — 14-апта

Қосымша функция: жеке профиль беті (аты-жөні мен email өзгерту).

Орнату:
1. Qarjy_14_week_PHP папкасын C:\xampp\htdocs ішіне көшіріңіз.
2. XAMPP-та Apache және MySQL қосыңыз. MySQL порты — 3307.
3. http://localhost/Qarjy_14_week_PHP/login.php адресін ашыңыз.
4. Бұрынғы аккаунтыңызбен кіріңіз. Қайтадан база құрудың қажеті жоқ.

Қауіпсіздік:
- includes/functions.php ішіндегі e() функциясы htmlspecialchars(..., ENT_QUOTES, 'UTF-8') қолданады.
- Профильді жаңарту CSRF токенімен қорғалған.
- Профиль SELECT/UPDATE және email тексеруі PDO prepared statements арқылы орындалады.
- Пайдаланушы тек өз профилін user_id арқылы жаңарта алады.
- Кіру кезінде session_regenerate_id(true) орындалады.
- Email бірегейлігі сервер жағында тексеріледі.

Ескерту: setup_database.php немесе setup_week12.php файлдарын қайта іске қосудың қажеті жоқ. Алдыңғы qarjy_db дерекқоры қолданылады.
