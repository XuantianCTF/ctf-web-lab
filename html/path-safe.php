<?php
// 路径处理的安全写法（讲义第 7.1 节）
//
// 危险写法（不要这样写）：
//   readfile(__DIR__ . '/files/' . $_GET['file']);
//   拼接后没有规范化，file=../../etc/passwd 可以跳出目录。
//
// 本页演示三层校验：
//   1. basename()   —— 去掉任何目录部分
//   2. realpath()   —— 规范化，解析 . 与 ..
//   3. 前缀比对     —— 确认最终路径仍在基目录内
header('Content-Type: text/plain; charset=utf-8');

$base = realpath(__DIR__ . '/files');
$name = basename($_GET['file'] ?? 'readme.txt');
$path = realpath($base . DIRECTORY_SEPARATOR . $name);

printf("base : %s\n", $base);
printf("name : %s\n", $name);
printf("path : %s\n\n", $path === false ? '(not found)' : $path);

if ($path === false || !str_starts_with($path, $base . DIRECTORY_SEPARATOR)) {
    http_response_code(400);
    exit("invalid file\n");
}

readfile($path);
