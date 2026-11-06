#!/usr/bin/env python3
# ============================================================
#  原样回显 HTTP 请求（讲义第 3 章的「神器」）
#
#  用法：
#      python3 tools/echo.py            # 监听 0.0.0.0:8080
#      python3 tools/echo.py 9000       # 指定端口
#
#  然后：
#      curl -v http://127.0.0.1:8080/hello?x=1
#      printf 'GET / HTTP/1.1\r\nHost: x\r\n\r\n' | nc 127.0.0.1 8080
#
#  作用：把「服务端真正收到的字节」原样打印回来，
#        让学员亲眼看到请求行 / 请求头 / 请求体。
# ============================================================
import sys
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer


class Handler(BaseHTTPRequestHandler):
    server_version = "CTF-Echo/1.0"
    sys_version = ""

    def _dump(self):
        length = int(self.headers.get("Content-Length") or 0)
        body = self.rfile.read(length) if length else b""

        raw = f"{self.command} {self.path} {self.request_version}\n"
        for k, v in self.headers.items():
            raw += f"{k}: {v}\n"
        raw += "\n"
        raw += body.decode("utf-8", "replace")

        data = raw.encode("utf-8")
        self.send_response(200)
        self.send_header("Content-Type", "text/plain; charset=utf-8")
        self.send_header("Content-Length", str(len(data)))
        self.send_header("X-Echo", "1")
        self.end_headers()
        self.wfile.write(data)

    do_GET = do_POST = do_PUT = do_DELETE = do_PATCH = do_HEAD = _dump

    def log_message(self, fmt, *args):
        # 同时把访问记录打到终端，方便对照
        sys.stderr.write("[echo] " + (fmt % args) + "\n")


def main():
    port = int(sys.argv[1]) if len(sys.argv) > 1 else 8080
    srv = ThreadingHTTPServer(("0.0.0.0", port), Handler)
    print(f"[echo] listening on http://0.0.0.0:{port}  (Ctrl-C 退出)")
    try:
        srv.serve_forever()
    except KeyboardInterrupt:
        print("\n[echo] bye")


if __name__ == "__main__":
    main()
