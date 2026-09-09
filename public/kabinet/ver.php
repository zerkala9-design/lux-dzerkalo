<?php
// Версія застосунку = час останньої зміни app.php. Потрібна, щоб збережений на телефон
// кабінет (PWA) помічав нову версію й сам перезавантажувався, а не показував стару.
// Свідомо без сесії: віддає лише число, жодних даних кабінету тут немає.
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
header('X-Robots-Tag: noindex, nofollow');
echo (string) @filemtime(__DIR__ . '/app.php');
