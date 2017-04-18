<?php
//swoole服务端脚本，根据名称加载文件，并能传递参数，可扩展为完整框架
$http = new swoole_http_server("0.0.0.0", 9501);

$http->set([
    'worker_num' => 4,
    //'daemonize' => 1,
]);

$http->on('request', function ($request, $response) {
    $pathinfo = $request->server['path_info'];
    // 拼接文件名
    $filename = __DIR__ . '/' . $pathinfo;
    if (is_file($filename)) {
        $ext = pathinfo($request->server['path_info'], PATHINFO_EXTENSION);
        if ($ext == 'php') { //  处理动态请求
            $_GET = $_POST = $_COOKIE = $_REQUEST = [];

            if (!empty($request->get)) {
                $_GET = $request->get;
                $_REQUEST += $_GET;
            }

            if (!empty($request->post)) {
                $_POST = $request->post;
                $_REQUEST += $_POST;
            }

            if (!empty($request->cookie)) {
                $_COOKIE = $request->cookie;
            }

            // 开启输出缓冲区
            ob_start();

            // 载入php文件
            include $filename;

            // 获取缓冲区内容
            $content = ob_get_contents();
            ob_end_clean();

            // 返回
            $response->end($content);
        } else {
            $mimes = include("mimes.php");
            $response->header("Content-Type", $mimes[$ext]);
            // 读取文件内容并输出
            $content = file_get_contents($filename) ;
            $response->end($content);
        }
    } else {
        $response->status('404');
        $response->end('404 not found');
    }
});

$http->start();