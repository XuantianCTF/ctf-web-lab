<?php
// 文件上传的安全写法（讲义第 7.3 节）
//
// 四层防护：
//   1. 内容类型校验（mime_content_type 看文件内容，不只看扩展名）
//   2. 大小限制
//   3. 随机重命名，不信任原文件名
//   4. 存储目录不可执行（见 uploads/.htaccess）
//
// 注意：MIME 仍可被精心构造的文件欺骗，
//       最终防线是「上传目录不在 DocumentRoot，或禁止脚本解析」。
$msg   = '';
$saved = '';
$ok    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['f'])) {
    $f = $_FILES['f'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        $msg = '上传失败，错误码：' . $f['error'];
    } elseif ($f['size'] > 2 * 1024 * 1024) {
        $msg = '文件超过 2MB';
    } else {
        $allow = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
        ];
        $mime = mime_content_type($f['tmp_name']);

        if (!isset($allow[$mime])) {
            $msg = '不允许的类型：' . htmlspecialchars((string)$mime);
        } else {
            $name = bin2hex(random_bytes(16)) . '.' . $allow[$mime];
            $dst  = __DIR__ . '/uploads/' . $name;

            if (move_uploaded_file($f['tmp_name'], $dst)) {
                $msg   = '已保存为 /uploads/' . $name;
                $saved = $name;
                $ok    = true;
            } else {
                $msg = '保存失败，检查 uploads/ 是否可写';
            }
        }
    }
}
?>
<!doctype html>
<html lang="zh-CN">
<head><meta charset="utf-8"><title>上传示例</title>
<style>body{font-family:sans-serif;max-width:680px;margin:40px auto;line-height:1.7}
code{background:#f6f8fa;padding:2px 5px}</style></head>
<body>
  <h1>文件上传（安全写法）</h1>
  <p>只接受 JPEG / PNG / GIF，最大 2MB，保存时重命名为随机名。</p>

  <?php if ($msg): ?>
    <p><strong><?= htmlspecialchars($msg) ?></strong></p>
    <?php if ($ok): ?><p>访问：<a href="/uploads/<?= htmlspecialchars($saved) ?>">查看文件</a></p><?php endif; ?>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <input type="file" name="f" required>
    <button type="submit">上传</button>
  </form>

  <hr>
  <p>思考：如果只检查扩展名会怎样？为什么 <code>uploads/.htaccess</code> 还要额外禁用 PHP？</p>
  <p><a href="/">返回首页</a></p>
</body></html>
