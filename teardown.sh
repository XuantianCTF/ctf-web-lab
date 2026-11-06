#!/usr/bin/env bash
# ============================================================
#  卸载实验环境（Debian / Ubuntu）
#  用法：sudo ./teardown.sh            # 保留站点文件
#        sudo ./teardown.sh --purge    # 同时删除站点根目录
# ============================================================
set -euo pipefail

DEST="${LAB_ROOT:-/var/www/ctf}"

if [ "$(id -u)" -eq 0 ]; then SUDO=""; else SUDO="sudo"; fi

echo "[+] 停用 ctf 站点"
$SUDO a2dissite ctf >/dev/null 2>&1 || true
$SUDO rm -f /etc/apache2/sites-available/ctf.conf

echo "[+] 恢复默认站点 000-default"
$SUDO a2ensite 000-default >/dev/null 2>&1 || true

echo "[+] 删除 /etc/hosts 中的 ctf.local"
$SUDO sed -i.bak '/\bctf\.local\b/d' /etc/hosts

$SUDO apache2ctl configtest
$SUDO systemctl reload apache2

if [ "${1:-}" = "--purge" ]; then
  echo "[+] 删除站点根目录 $DEST"
  $SUDO rm -rf "$DEST"
fi

echo "[+] 完成。apache2 / php 软件包未卸载（如需卸载请手动 apt remove）。"
