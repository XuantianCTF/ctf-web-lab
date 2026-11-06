# ctf-web-lab

一个用于 **CTF Web 入门教学** 的最小示例仓库：Linux + Apache2 + PHP，从部署、HTTP 报文到 URL 映射与安全写法，每个知识点对应一个可直接访问的示例页面。

本仓库只包含**可运行的示例代码与部署脚本**；讲义正文由独立的博客目录维护，不在本仓库内。

- 无需 Docker，直接使用系统包管理器部署
- 每个示例只演示一个概念，代码内附注释
- 只包含安全写法，不含靶标、flag 或利用脚本

---

## 目录结构

```
.
├── setup.sh              # 一键部署（Debian / Ubuntu）
├── teardown.sh           # 卸载 / 回滚
├── conf/
│   └── ctf.conf          # Apache 虚拟主机配置（__DOCROOT__ 由脚本替换）
├── html/                 # 站点根目录
│   ├── index.php         # 示例导航 + 当前请求回显
│   ├── static.html       # 静态资源
│   ├── info.php          # $_SERVER 关键字段
│   ├── echo.php          # 原样回显请求行 / 头 / 体
│   ├── headers.php       # 状态码与响应头
│   ├── form.php          # GET / POST 对比
│   ├── path-safe.php     # 路径处理：basename + realpath + 前缀校验
│   ├── include-demo.php  # 文件包含的白名单写法
│   ├── upload-safe.php   # 上传：类型 / 大小 / 随机名
│   ├── files/            # 静态文件与包含示例页
│   ├── private/          # .htaccess + Require ip 示例
│   └── uploads/          # 上传目录（.htaccess 禁用脚本解析）
├── tools/
│   └── echo.py           # 独立的最小 HTTP 回显服务（协议演示用）
└── README.md
```

---

## 快速开始

### 环境要求

- Debian / Ubuntu（22.04+），`sudo` 权限
- 80 端口未被占用
- 仅用于本机或授权环境

检查端口：

```bash
sudo ss -lntp | grep ':80' || echo "80 端口空闲"
```

### 部署

```bash
chmod +x setup.sh teardown.sh
sudo ./setup.sh
```

脚本会：

1. 安装 `apache2`、`libapache2-mod-php`、`php`、`rsync`、`curl`；
2. 把 `html/` 同步到 `/var/www/ctf`；
3. 安装并启用虚拟主机 `ctf.conf`，启用 `rewrite`、`headers`、`php` 模块；
4. 关闭默认站点 `000-default`；
5. 写入 `/etc/hosts`，把 `ctf.local` 指向本机；
6. 设置 `uploads/` 属主，说明“可写”不等于 `chmod 777`。

自定义站点目录：

```bash
sudo LAB_ROOT=/srv/ctf ./setup.sh
```

### 访问

| URL | 内容 |
|---|---|
| <http://ctf.local/> | 示例导航 |
| <http://ctf.local/info.php> | 服务端变量 |
| <http://ctf.local/echo.php> | 原样回显请求 |
| <http://ctf.local/headers.php?code=404> | 状态码 / 响应头 |
| <http://ctf.local/path-safe.php?file=readme.txt> | 安全路径处理 |
| <http://ctf.local/include-demo.php?page=about> | 白名单包含 |
| <http://ctf.local/upload-safe.php> | 安全上传 |

用 IP 访问时若想命中该站点：

```bash
curl -H 'Host: ctf.local' http://127.0.0.1/
```

### 清理

```bash
sudo ./teardown.sh            # 保留站点文件
sudo ./teardown.sh --purge    # 同时删除站点目录
```

---

## 协议演示（不依赖 Apache）

```bash
python3 tools/echo.py 8080
curl -v http://127.0.0.1:8080/hello?x=1
printf 'GET / HTTP/1.1\r\nHost: x\r\n\r\n' | nc 127.0.0.1 8080
```

适合在讲解 HTTP 报文结构时单独使用。

---

## 配置说明

`conf/ctf.conf` 中的 `__DOCROOT__` 会在部署时替换为实际站点根目录，主要包含：

| 指令 | 用途 |
|---|---|
| `DocumentRoot` | URL 到文件的映射起点 |
| `Options Indexes` | 目录列表（信息泄露示例，可改为 `-Indexes`） |
| `AllowOverride All` | 启用 `.htaccess` |
| `Alias /shared` | 别名映射示例 |
| `<Directory private>` | `Require ip` 访问控制示例 |
| `CustomLog ... combined` | 含 Referer / User-Agent 的日志格式 |

修改配置后：

```bash
sudo apache2ctl configtest && sudo systemctl reload apache2
```

---

## macOS 说明

macOS 自带 Apache，但 12 起不再自带 PHP。推荐在 Linux 虚拟机中运行；或使用：

```bash
brew install httpd php
```

只演示协议时，`tools/echo.py` 可直接在 macOS 上使用。

---

## 许可

[MIT](LICENSE)
