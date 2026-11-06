<?php
// 目录级访问控制示例（讲义第 5.7 节）
// 本目录的 .htaccess 只允许 127.0.0.1 / ::1 访问。
// 生效前提：主配置中 AllowOverride All。
?>
<!doctype html>
<html lang="zh-CN">
<head><meta charset="utf-8"><title>Private</title>
<style>body{font-family:sans-serif;max-width:640px;margin:40px auto;line-height:1.7}</style></head>
<body>
  <h1>目录级访问控制</h1>
  <p>能看到本页，说明请求来自被允许的地址（本机）。</p>
  <pre># private/.htaccess
Require ip 127.0.0.1 ::1</pre>
  <p>
    思考：<code>Require ip</code> 判断的是什么？
    伪造 <code>X-Forwarded-For</code> 有用吗？为什么？
  </p>
  <p><a href="/">返回首页</a></p>
</body></html>
