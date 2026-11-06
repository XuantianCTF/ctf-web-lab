<?php
// 响应头与状态码示例（讲义第 4 章）
// 访问：headers.php            -> 200
//      headers.php?code=404    -> 404
//      headers.php?code=302&to=/info.php -> 重定向
$code = (int)($_GET['code'] ?? 200);
if ($code < 100 || $code > 599) {
    $code = 200;
}

http_response_code($code);
header('Content-Type: text/plain; charset=utf-8');
header('X-Demo: headers');
header('Cache-Control: no-store');

if ($code >= 300 && $code < 400) {
    header('Location: ' . ($_GET['to'] ?? '/'));
}

echo "requested status: $code\n";
echo "method          : " . $_SERVER['REQUEST_METHOD'] . "\n\n";
echo "用 curl -i 观察状态行与响应头：\n";
echo "  curl -i 'http://" . ($_SERVER['HTTP_HOST'] ?? 'ctf.local') . "/headers.php?code=$code'\n";
