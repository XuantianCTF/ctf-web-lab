<?php
// 文件包含的白名单写法（讲义第 7.2 节）
//
// 危险写法（不要这样写）：
//   include($_GET['page']);
//   用户可以包含非预期文件，把「读」升级为「执行」。
//
// 安全原则：用户输入只用于「查表」，不参与路径拼接。
header('Content-Type: text/plain; charset=utf-8');

$pages = [
    'about' => __DIR__ . '/files/about.php',
    'help'  => __DIR__ . '/files/help.php',
];

$key = $_GET['page'] ?? 'about';
if (!isset($pages[$key])) {
    http_response_code(404);
    exit("unknown page\n");
}

include $pages[$key];
