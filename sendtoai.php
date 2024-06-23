<?php
header('Content-Type: application/json');
// 假设的API URL和API密钥
$apiUrl = 'https://api.moonshot.cn/v1/chat/completions';
$apiKey = 'sk-8EiijDnuZokTisMQrIxcGm5DLR2R0afw7ykJVAaWRE6X4x5l';
$message = $_POST['message'];
// 请求参数，根据API文档调整
$postData = [
    "model" => "moonshot-v1-8k",
    "messages" => [
        ["role" => "system", "content" => "你是一名资深php程序员，你擅长解决各种程序问题"],
        ["role" => "user", "content" => $message]
    ],
    "temperature" => 0.3
];

// 初始化cURL会话
$ch = curl_init($apiUrl);

// 设置cURL选项
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
// 使用受信任的 CA 证书
curl_setopt($ch, CURLOPT_CAINFO, './cacert.pem');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

// 执行cURL请求并获取响应
$response = curl_exec($ch);

// 检查是否有错误发生
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // 解析响应数据
    $responseData = json_decode($response, true);
    $reply =  $responseData['choices'][0]['message']['content'];

    echo json_encode(['reply'=>$reply]);
}

// 关闭cURL会话
curl_close($ch);

?>