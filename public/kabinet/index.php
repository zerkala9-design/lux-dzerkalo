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

// Токен «запамʼятати мене» — привʼязаний до пароля (зміна пароля скидає всі токени)
$REMEMBER = hash('sha256', $PASS_HASH . '|lux-kabinet-remember-v1');
$COOKIE   = 'kabinet_remember';
$YEAR     = 31536000;

// Тримати сесію довго (щоб не питати пароль щоразу)
@ini_set('session.gc_maxlifetime', (string) $YEAR);
session_set_cookie_params([
    'lifetime' => $YEAR, 'path' => '/kabinet/', 'secure' => true,
    'httponly' => true, 'samesite' => 'Lax',
]);
session_start();

// Вихід
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    setcookie($COOKIE, '', time() - 3600, '/kabinet/', '', true, true);
    header('Location: /kabinet/');
    exit;
}

$error = false;

/* ───────────────── Захист від перебору пароля ─────────────────
   Затримки замало: запити можна слати паралельно, і короткий пароль
   перебирається за хвилини. Тому рахуємо невдалі спроби з кожної IP
   і після MAX_FAILS блокуємо її на BLOCK_MIN хвилин.
   Лічильник у файлі під захистом «<?php exit;» — напряму з браузера не читається. */
const MAX_FAILS = 5;      // скільки помилок дозволено
const BLOCK_MIN = 15;     // на скільки хвилин блокувати
const WINDOW_MIN = 15;    // за який період рахуємо помилки

$guardDir  = __DIR__ . '/data';
if (!is_dir($guardDir)) { @mkdir($guardDir, 0775, true); }
$guardFile = $guardDir . '/login_guard.json.php';
$GUARD_PREFIX = "<?php exit; ?>\n";

// REMOTE_ADDR, а не X-Forwarded-For: заголовок підробляється, і тоді захист обходиться
$ipKey = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . $PASS_HASH);

/** Читає стан, застосовує $fn, записує назад. Під flock — бо атака саме паралельна. */
function guard_update(string $file, string $prefix, callable $fn) {
    $fh = @fopen($file, 'c+');
    if (!$fh) { return $fn([]); }              // не змогли відкрити — не блокуємо вхід
    @flock($fh, LOCK_EX);
    $raw = stream_get_contents($fh);
    if (strpos((string) $raw, $prefix) === 0) { $raw = substr($raw, strlen($prefix)); }
    $state = json_decode((string) $raw, true);
    if (!is_array($state)) { $state = []; }

    $now = time();
    foreach ($state as $k => $v) {             // прибираємо застаріле, щоб файл не ріс
        $seen = (int) ($v['seen'] ?? 0);
        if ($now - $seen > max(BLOCK_MIN, WINDOW_MIN) * 60) { unset($state[$k]); }
    }

    $result = $fn($state);
    @ftruncate($fh, 0); @rewind($fh);
    @fwrite($fh, $prefix . json_encode($state));
    @fflush($fh); @flock($fh, LOCK_UN); @fclose($fh);
    return $result;
}

// Скільки секунд лишилось до розблокування (0 — не заблоковано)
$blockedFor = guard_update($guardFile, $GUARD_PREFIX, function (array &$s) use ($ipKey) {
    $until = (int) ($s[$ipKey]['until'] ?? 0);
    return $until > time() ? $until - time() : 0;
});

// Обробка входу
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pass'])) {
    if ($blockedFor > 0) {
        $error = true;                          // заблоковано — пароль навіть не перевіряємо
    } elseif (password_verify((string) $_POST['pass'], $PASS_HASH)) {
        guard_update($guardFile, $GUARD_PREFIX, function (array &$s) use ($ipKey) {
            unset($s[$ipKey]);                  // успішний вхід скидає лічильник
        });
        session_regenerate_id(true);
        $_SESSION['kabinet_ok'] = true;
        // запамʼятати на рік
        setcookie($COOKIE, $REMEMBER, time() + $YEAR, '/kabinet/', '', true, true);
        header('Location: /kabinet/app.php');
        exit;
    } else {
        $error = true;
        $blockedFor = guard_update($guardFile, $GUARD_PREFIX, function (array &$s) use ($ipKey) {
            $now = time();
            $rec = $s[$ipKey] ?? ['n' => 0, 'first' => $now];
            if ($now - (int) $rec['first'] > WINDOW_MIN * 60) { $rec = ['n' => 0, 'first' => $now]; }
            $rec['n'] = (int) $rec['n'] + 1;
            $rec['seen'] = $now;
            $rec['until'] = ($rec['n'] >= MAX_FAILS) ? $now + BLOCK_MIN * 60 : 0;
            $s[$ipKey] = $rec;
            return $rec['until'] > $now ? $rec['until'] - $now : 0;
        });
        usleep(600000);                         // затримка лишається як перший бар'єр
    }
}

// Автовхід за токеном «запамʼятати мене» (пароль вже вводили раніше)
if (empty($_SESSION['kabinet_ok'])
    && isset($_COOKIE[$COOKIE])
    && hash_equals($REMEMBER, (string) $_COOKIE[$COOKIE])) {
    $_SESSION['kabinet_ok'] = true;
    setcookie($COOKIE, $REMEMBER, time() + $YEAR, '/kabinet/', '', true, true); // продовжити
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
  <link rel="manifest" href="/kabinet/manifest.json" />
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
    <input id="pass" name="pass" type="password" placeholder="••••••••" autofocus required <?= $blockedFor > 0 ? 'disabled' : '' ?> />
    <button type="submit" <?= $blockedFor > 0 ? 'disabled' : '' ?>>Увійти</button>
    <?php if ($blockedFor > 0): ?>
      <div class="err">Забагато невдалих спроб. Спробуйте через <?= (int) ceil($blockedFor / 60) ?> хв.</div>
    <?php elseif ($error): ?>
      <div class="err">Невірний пароль. Спробуй ще раз.</div>
    <?php endif; ?>
  </form>
</body>
</html>
