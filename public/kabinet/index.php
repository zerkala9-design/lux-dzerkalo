<?php
/**
 * Приватний кабінет /kabinet — серверний пароль (Basic-рівень, без сторонніх сервісів).
 *
 * Пароль зберігається ТІЛЬКИ як bcrypt-хеш нижче — у відкритому вигляді його немає.
 * Щоб змінити пароль: згенеруй новий хеш командою
 *     php -r 'echo password_hash("НОВИЙ_ПАРОЛЬ", PASSWORD_BCRYPT), "\n";'
 * і встав його у $PASS_HASH. Один спільний пароль для персоналу.
 *
 * Сам застосунок лежить у app.html і віддається лише після успішного входу
 * (прямий доступ до app.html закритий у .htaccess).
 */

// bcrypt-хеш пароля (не сам пароль!).
$PASS_HASH = '$2y$12$Cu21nx9TBkE1/6uf7i.Eo.tviz4.Q7hA/m1/zXvlkBVvorAxWCXri';

session_start();

// Вихід
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: /kabinet/');
    exit;
}

$error = false;

// Обробка входу
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pass'])) {
    if (password_verify((string) $_POST['pass'], $PASS_HASH)) {
        session_regenerate_id(true);
        $_SESSION['kabinet_ok'] = true;
        header('Location: /kabinet/app.php');
        exit;
    }
    $error = true;
    // невелика затримка проти перебору
    usleep(600000);
}

// Уже авторизований → застосунок (самозахищений app.php)
if (!empty($_SESSION['kabinet_ok'])) {
    header('Location: /kabinet/app.php');
    exit;
}

// Інакше — форма входу
header('Content-Type: text/html; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');
?><!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8" />
  <title>Кабінет · Lux Дзеркало</title>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="apple-touch-icon" href="/kabinet/apple-touch-icon.png" />
  <link rel="icon" type="image/png" href="/kabinet/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-title" content="Lux Кабінет" />
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
  <meta name="theme-color" content="#0b1020" />
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{min-height:100vh;display:flex;align-items:center;justify-content:center;
      font-family:system-ui,-apple-system,"Segoe UI",sans-serif;
      background:radial-gradient(circle at top,#10142a 0,#05060a 40%,#020308 100%);
      color:#e5e7eb;padding:20px}
    .card{width:100%;max-width:380px;background:rgba(20,24,42,.72);
      border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:28px 24px;
      box-shadow:0 20px 60px rgba(0,0,0,.45)}
    h1{font-size:22px;font-weight:800;margin-bottom:6px}
    p{font-size:13px;color:rgba(229,231,235,.7);margin-bottom:20px;line-height:1.5}
    label{display:block;font-size:12px;color:rgba(229,231,235,.8);margin-bottom:6px}
    input[type=password]{width:100%;padding:13px 14px;border-radius:12px;
      border:1px solid rgba(255,255,255,.14);background:rgba(0,0,0,.25);
      color:#fff;font-size:16px;outline:none}
    input[type=password]:focus{border-color:rgba(255,122,0,.6)}
    button{width:100%;margin-top:16px;padding:14px;border:0;border-radius:12px;
      font-size:16px;font-weight:800;color:#1a1205;cursor:pointer;
      background:linear-gradient(90deg,#ffb35a,#ff7a00);
      box-shadow:0 10px 30px rgba(255,122,0,.35)}
    button:active{transform:translateY(1px)}
    .err{margin-top:14px;font-size:13px;color:#ff8a8a;text-align:center}
  </style>
</head>
<body>
  <form class="card" method="post" autocomplete="off">
    <h1>Lux Дзеркало · Кабінет</h1>
    <p>Робочий калькулятор і наряди. Доступ лише для персоналу — введи пароль.</p>
    <label for="pass">Пароль</label>
    <input id="pass" name="pass" type="password" placeholder="••••••••" autofocus required />
    <button type="submit">Увійти</button>
    <?php if ($error): ?><div class="err">Невірний пароль. Спробуй ще раз.</div><?php endif; ?>
  </form>
</body>
</html>
