#!/usr/bin/env bash
# ============================================================
#  CTF Web 入门示例站点 —— 原生部署脚本（Debian / Ubuntu）
#  对应讲义：第 2 章「Apache2 部署」
#
#  用法：
#      sudo ./setup.sh
#      sudo LAB_ROOT=/srv/ctf ./setup.sh     # 自定义站点根目录
#
#  做的事情：
#      1. 安装 apache2 / php / libapache2-mod-php
#      2. 把 html/ 同步到站点根目录
#      3. 安装虚拟主机 ctf.conf，启用 rewrite / headers
#      4. 关闭默认站点 000-default（避免抢走 80 端口）
#      5. 写 /etc/hosts，让 ctf.local 指向本机
#      6. 设置 uploads 目录属主（说明「可写」不等于 777）
# ============================================================
set -euo pipefail

SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEST="${LAB_ROOT:-/var/www/ctf}"
HOSTNAME_LAB="ctf.local"

log()  { printf '\033[1;34m[+]\033[0m %s\n' "$*"; }
warn() { printf '\033[1;33m[!]\033[0m %s\n' "$*"; }
die()  { printf '\033[1;31m[x]\033[0m %s\n' "$*" >&2; exit 1; }

# ---------- 0. 环境检查 ----------
[ "$(uname -s)" = "Linux" ] || die "本脚本只支持 Linux。macOS 请见 README 的「macOS 说明」。"
command -v apt-get >/dev/null 2>&1 || die "未找到 apt-get，请使用 Debian / Ubuntu 系发行版。"

if [ "$(id -u)" -eq 0 ]; then
  SUDO=""
else
  command -v sudo >/dev/null 2>&1 || die "需要 root 权限，或安装 sudo。"
  SUDO="sudo"
fi

log "源目录：$SRC"
log "站点根目录：$DEST"

# ---------- 1. 安装软件包 ----------
log "安装 apache2 / php / 工具链 ……"
$SUDO apt-get update -qq
$SUDO DEBIAN_FRONTEND=noninteractive apt-get install -y -qq \
  apache2 libapache2-mod-php php php-cli rsync curl net-tools

# ---------- 2. 同步站点文件 ----------
log "同步网站文件到 $DEST"
$SUDO mkdir -p "$DEST"
$SUDO rsync -a --delete "$SRC/html/" "$DEST/"

# 站点文件归 root，只有 uploads 允许 www-data 写入
# （这正是第 5 章要讲的「属主 / 权限」）
$SUDO chown -R root:root "$DEST"
$SUDO mkdir -p "$DEST/uploads"
$SUDO chown -R www-data:www-data "$DEST/uploads"
$SUDO chmod 755 "$DEST" "$DEST/uploads"

# .htaccess 必须由 root 持有，避免被 Web 进程改写
$SUDO chown root:root "$DEST/uploads/.htaccess"
$SUDO chmod 644 "$DEST/uploads/.htaccess"

# ---------- 3. 安装虚拟主机 ----------
log "安装虚拟主机配置 ctf.conf"
sed "s|__DOCROOT__|$DEST|g" "$SRC/conf/ctf.conf" \
  | $SUDO tee /etc/apache2/sites-available/ctf.conf >/dev/null

$SUDO a2enmod rewrite headers >/dev/null

# libphp 只能跑在 prefork MPM 下（Debian 默认是 event）
$SUDO a2dismod mpm_event >/dev/null 2>&1 || true
$SUDO a2enmod mpm_prefork >/dev/null 2>&1 || true

# 显式启用 PHP 模块，避免 .php 被当成文本下载
PHPVER="$(php -r 'echo PHP_MAJOR_VERSION, ".", PHP_MINOR_VERSION;' 2>/dev/null || true)"
if [ -n "$PHPVER" ]; then
  $SUDO a2enmod "php${PHPVER}" >/dev/null 2>&1 || $SUDO a2enmod php >/dev/null 2>&1 || true
fi

$SUDO a2ensite ctf >/dev/null
# 默认站点会作为「兜底虚拟主机」抢走所有未匹配的 Host，先关掉
$SUDO a2dissite 000-default >/dev/null 2>&1 || true

$SUDO apache2ctl configtest
$SUDO systemctl enable --now apache2
$SUDO systemctl reload apache2

# ---------- 4. hosts ----------
if ! grep -qE "^[^#]*\b${HOSTNAME_LAB}\b" /etc/hosts; then
  log "写入 /etc/hosts：127.0.0.1 $HOSTNAME_LAB"
  echo "127.0.0.1 $HOSTNAME_LAB" | $SUDO tee -a /etc/hosts >/dev/null
else
  log "/etc/hosts 已包含 $HOSTNAME_LAB，跳过"
fi

# ---------- 5. 自检 ----------
log "自检 ……"
code=$(curl -s -o /dev/null -w '%{http_code}' -H "Host: $HOSTNAME_LAB" http://127.0.0.1/ || true)
code_php=$(curl -s -o /dev/null -w '%{http_code}' -H "Host: $HOSTNAME_LAB" http://127.0.0.1/info.php || true)
log "首页状态码：$code ｜ info.php 状态码：$code_php"

cat <<EOF

============================================================
 部署完成
------------------------------------------------------------
 首页        http://$HOSTNAME_LAB/
 请求信息    http://$HOSTNAME_LAB/info.php
 站点根目录  $DEST
 配  置      /etc/apache2/sites-available/ctf.conf
 日  志      /var/log/apache2/ctf_access.log
             /var/log/apache2/ctf_error.log
------------------------------------------------------------
 下一步：
     curl -i http://$HOSTNAME_LAB/
     打开 README.md，从第 2 章开始做实验
============================================================
EOF

if [ "$code" != "200" ] || [ "$code_php" != "200" ]; then
  warn "自检未全部返回 200，请查看：$SUDO tail -n 50 /var/log/apache2/error.log"
fi
