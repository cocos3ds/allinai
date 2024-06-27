<?php
header('Content-Type: application/json');
require_once("db.php");//获取mysqli连接
// 假设的API URL和API密钥
$apiUrl = 'https://api.moonshot.cn/v1/tokenizers/estimate-token-count';
$apiKey = 'sk-8EiijDnuZokTisMQrIxcGm5DLR2R0afw7ykJVAaWRE6X4x5l';
$message = $_POST['message'];
// 请求参数，根据API文档调整
$system_prompt = file_get_contents('prompt.txt');

$project_file = listFilesAndContents(['cacert.pem','test.php','prompt.txt','sendtoai.php','kimireturn.txt','debug_prompt.txt']);

$system_prompt = str_replace("[project file]",$project_file,$system_prompt);
// file_put_contents("debug_prompt.txt","\n--------------------\n".$system_prompt."\n--------------------\n",FILE_APPEND);
$postData = [
    "model" => "moonshot-v1-128k",
    "messages" => [
        ["role" => "system", "content" => $system_prompt],
        ["role" => "user", "content" => file_get_contents("temp.txt")]
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
  
    echo $responseData['data']['total_tokens'];
  
   
}

// 关闭cURL会话
curl_close($ch);


function listFilesAndContents($filterList = []) {
    $result = "";

    // 获取项目根路径
    $rootPath = dirname(__FILE__);

    // 打开目录句柄
    if ($handle = opendir($rootPath)) {
        // 遍历目录中的文件
        while (false !== ($entry = readdir($handle))) {
            // 忽略 . 和 .. 以及文件夹，只处理文件
            if ($entry != "." && $entry != ".." && is_file($rootPath . DIRECTORY_SEPARATOR . $entry) && !in_array($entry, $filterList)) {
                // 读取文件内容
                $content = file_get_contents($rootPath . DIRECTORY_SEPARATOR . $entry);

                // 累加文件名和内容到结果字符串
                $result .= "文件名: $entry\n";
                $result .= "$content\n\n";
            }
        }
        // 关闭目录句柄
        closedir($handle);
    } else {
        $result .= "无法打开目录: $rootPath\n";
    }

    return $result;
}



?>