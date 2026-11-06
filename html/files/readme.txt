CTF Web Lab / files
===================

这个目录用于演示两件事：

1. 静态文件映射
   请求 /files/readme.txt  ->  磁盘上的本文件

2. 配置中的 Alias
   请求 /shared/readme.txt ->  同样映射到本目录
   见 conf/ctf.conf

课堂练习（安全向）：
- 对比 /files/readme.txt 与 /shared/readme.txt 的响应头；
- 修改 Alias 指向另一个目录，观察结果；
- 阅读 path-safe.php，说明为什么 basename + realpath 能防住目录穿越。
