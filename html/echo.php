<?php
// 原样回显请求（讲义第 3 章）
// 用途：用 nc / curl 手写报文后，确认服务端真正收到的内容
header('Content-Type: text/plain; charset=utf-8');

$headers = function_exists('getallheaders') ? getallheaders() : [];
$body    = file_get_contents('php://input');

printf("%s %s %s\n", $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['SERVER_PROTOCOL']);
foreach ($headers as $k => $v) {
    printf("%s: %s\n", $k, $v);
}
echo "\n" . $body;
