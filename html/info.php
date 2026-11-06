<?php
// 服务端变量观察页（讲义第 3 章）
// 用途：对比「客户端发了什么」与「服务端收到了什么」
header('Content-Type: text/plain; charset=utf-8');

printf("method   : %s\n", $_SERVER['REQUEST_METHOD'] ?? '');
printf("uri      : %s\n", $_SERVER['REQUEST_URI'] ?? '');
printf("protocol : %s\n", $_SERVER['SERVER_PROTOCOL'] ?? '');
printf("host     : %s\n", $_SERVER['HTTP_HOST'] ?? '');
printf("ua       : %s\n", $_SERVER['HTTP_USER_AGENT'] ?? '');
printf("referer  : %s\n", $_SERVER['HTTP_REFERER'] ?? '');
printf("cookie   : %s\n", $_SERVER['HTTP_COOKIE'] ?? '');
printf("xff      : %s\n", $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '(none)');
printf("remote   : %s\n", $_SERVER['REMOTE_ADDR'] ?? '');
printf("script   : %s\n", $_SERVER['SCRIPT_FILENAME'] ?? '');
printf("docroot  : %s\n", $_SERVER['DOCUMENT_ROOT'] ?? '');
printf("sapi     : %s\n", PHP_SAPI);

if (!empty($_GET)) {
    echo "--- GET ---\n";
    foreach ($_GET as $k => $v) {
        printf("%s = %s\n", $k, is_array($v) ? json_encode($v) : $v);
    }
}
