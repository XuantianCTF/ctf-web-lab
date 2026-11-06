<?php
// 示例站点首页：把各章示例串起来（对应讲义第 2、3 章）
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'ctf.local';
$base   = $scheme . '://' . $host;

$examples = [
    ['static.html',    '静态页面',            '静态文件的响应体就是磁盘字节'],
    ['info.php',       '服务端变量',          '看 PHP 收到了什么'],
    ['echo.php',       '原样回显请求',        '请求行 / 请求头 / 请求体'],
    ['headers.php',    '响应头与状态码',      '?code=404'],
    ['form.php',       'GET / POST 表单',     '观察方法对参数位置的影响'],
    ['path-safe.php',  '路径处理（安全写法）', 'basename + realpath + 前缀校验'],
    ['include-demo.php', '包含（白名单写法）', '?page=about'],
    ['upload-safe.php',  '上传（三重校验）',   '类型 / 大小 / 随机名'],
    ['files/readme.txt', '静态文件',           '?file=readme.txt'],
    ['private/',       '目录级访问控制',      '.htaccess + Require ip'],
];
?>
<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CTF Web 示例站点</title>
<style>
  body { font-family: -apple-system, "Segoe UI", "Noto Sans SC", sans-serif;
         max-width: 880px; margin: 40px auto; padding: 0 20px; color: #1f2328; line-height: 1.6; }
  h1 { margin-bottom: 4px; }
  .sub { color: #656d76; margin-top: 0; }
  table { border-collapse: collapse; width: 100%; margin: 24px 0; }
  th, td { border-bottom: 1px solid #d8dee4; padding: 8px 10px; text-align: left; vertical-align: top; }
  th { background: #f6f8fa; font-weight: 600; }
  code { background: #f6f8fa; padding: 1px 5px; border-radius: 4px; font-size: 90%; }
  a { color: #0969da; text-decoration: none; }
  a:hover { text-decoration: underline; }
  .box { background: #f6f8fa; border: 1px solid #d8dee4; border-radius: 8px;
         padding: 12px 16px; font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
         font-size: 13px; white-space: pre-wrap; }
</style>
</head>
<body>
  <h1>CTF Web 示例站点</h1>
  <p class="sub">Linux + Apache2 + PHP ｜ 协议 → 服务 → 系统 → 代码</p>

  <table>
    <tr><th>示例</th><th>内容</th><th>观察点</th></tr>
    <?php foreach ($examples as [$path, $title, $hint]): ?>
    <tr>
      <td><a href="/<?= htmlspecialchars($path) ?>"><?= htmlspecialchars($path) ?></a></td>
      <td><?= htmlspecialchars($title) ?></td>
      <td><code><?= htmlspecialchars($hint) ?></code></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <h2>当前请求（服务端视角）</h2>
  <div class="box"><?php
    printf("method : %s\n", $_SERVER['REQUEST_METHOD']);
    printf("uri    : %s\n", $_SERVER['REQUEST_URI']);
    printf("host   : %s\n", $_SERVER['HTTP_HOST'] ?? '');
    printf("ua     : %s\n", $_SERVER['HTTP_USER_AGENT'] ?? '');
    printf("remote : %s\n", $_SERVER['REMOTE_ADDR']);
    printf("script : %s\n", $_SERVER['SCRIPT_FILENAME']);
    printf("php    : %s\n", PHP_VERSION);
  ?></div>

  <p><small>完整讲解见仓库根目录 <code>README.md</code>。</small></p>
</body>
</html>
