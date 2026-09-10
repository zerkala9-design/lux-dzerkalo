<?php
/**
 * Спільне сховище кабінету (замовлення + параметри/прайс) для всіх акаунтів.
 *
 * Дані зберігаються у файлах на сервері (data/orders.json, data/params.json),
 * тож усі, хто заходить у /kabinet, бачать той самий список замовлень і той
 * самий прайс — незалежно від пристрою чи «акаунта».
 *
 * Доступ лише для авторизованих у кабінеті (та сама сесія, що й index.php).
 *
 * GET  sync.php?key=orders|params            -> віддає JSON (або порожній)
 * POST sync.php?key=orders|params  (тіло=JSON) -> зберігає JSON
 */

session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// Захист: лише авторизовані користувачі кабінету
if (empty($_SESSION['kabinet_ok'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'unauthorized']);
    exit;
}

$key = isset($_GET['key']) ? preg_replace('/[^a-z]/', '', (string) $_GET['key']) : '';
$allowed = ['orders', 'params', 'archive'];
if (!in_array($key, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'bad key']);
    exit;
}

$dir = __DIR__ . '/data';
if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

// Дані лежать у файлах із розширенням .php і заглушкою «<?php exit;» на початку.
// Причина: хостинг віддає статику через nginx, який .htaccess не читає, тож звичайний
// data/orders.json був доступний будь-кому без входу. Файл .php сервер завжди віддає
// інтерпретатору — той одразу завершує роботу і не показує нічого.
$GUARD = "<?php exit; ?>\n";
$file  = $dir . '/' . $key . '.json.php';
$legacy = $dir . '/' . $key . '.json';   // старий, відкритий формат

// Разова міграція старих даних + видалення відкритого файлу з сервера
if (!is_file($file) && is_file($legacy)) {
    $old = @file_get_contents($legacy);
    if ($old !== false && @file_put_contents($file, $GUARD . $old, LOCK_EX) !== false) {
        @unlink($legacy);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    // Мʼяка валідація: має бути валідний JSON
    $decoded = json_decode($raw, true);
    if ($decoded === null && trim($raw) !== 'null') {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'invalid json']);
        exit;
    }
    // атомарний запис
    $tmp = $file . '.tmp';
    if (@file_put_contents($tmp, $GUARD . $raw, LOCK_EX) === false || !@rename($tmp, $file)) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'write failed']);
        exit;
    }
    @unlink($legacy); // старий відкритий файл більше не потрібен
    echo json_encode(['ok' => true, 'saved' => $key, 'ts' => time()]);
    exit;
}

// GET
if (is_file($file)) {
    $data = @file_get_contents($file);
    if ($data !== false && strpos($data, $GUARD) === 0) {
        $data = substr($data, strlen($GUARD));   // прибрати заглушку
    }
    echo ($data !== false && $data !== '') ? $data : 'null';
} else {
    echo 'null';
}
