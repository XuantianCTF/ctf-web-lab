<?php
// GET / POST 表单示例（讲义第 4 章）
// 观察：参数在 URL（GET）与在请求体（POST）时，服务端读取方式相同，
//       但日志、缓存、浏览器历史中的暴露面完全不同。
$name = $_POST['name'] ?? $_GET['name'] ?? '';
$msg  = $_POST['msg']  ?? $_GET['msg']  ?? '';
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>表单示例</title>
  <style>
    body { font-family: sans-serif; max-width: 680px; margin: 40px auto; line-height: 1.7; }
    input, textarea { width: 100%; box-sizing: border-box; padding: 6px; margin: 4px 0 12px; }
    fieldset { border: 1px solid #ccc; border-radius: 6px; margin-bottom: 20px; }
  </style>
</head>
<body>
  <h1>GET / POST 表单</h1>

  <fieldset>
    <legend>GET（参数出现在 URL）</legend>
    <form method="get" action="/form.php">
      <label>name <input name="name" value="<?= htmlspecialchars($name) ?>"></label>
      <label>msg  <textarea name="msg"><?= htmlspecialchars($msg) ?></textarea></label>
      <button type="submit">GET 提交</button>
    </form>
  </fieldset>

  <fieldset>
    <legend>POST（参数在请求体）</legend>
    <form method="post" action="/form.php">
      <label>name <input name="name" value="<?= htmlspecialchars($name) ?>"></label>
      <label>msg  <textarea name="msg"><?= htmlspecialchars($msg) ?></textarea></label>
      <button type="submit">POST 提交</button>
    </form>
  </fieldset>

  <?php if ($name !== '' || $msg !== ''): ?>
    <h2>服务端收到</h2>
    <pre>method = <?= htmlspecialchars($_SERVER['REQUEST_METHOD']) ?>
name   = <?= htmlspecialchars($name) ?>
msg    = <?= htmlspecialchars($msg) ?></pre>
  <?php endif; ?>

  <p><a href="/">返回首页</a></p>
</body>
</html>
