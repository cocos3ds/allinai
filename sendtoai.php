<?php
header('Content-Type: application/json');
require_once("db.php");//获取mysqli连接
// 假设的API URL和API密钥
// $apiUrl = 'https://api.moonshot.cn/v1/chat/completions'; //kimi
// $apiKey = 'sk-8EiijDnuZokTisMQrIxcGm5DLR2R0afw7ykJVAaWRE6X4x5l'; //kimi
$apiUrl = 'https://api.bianxieai.com/v1/chat/completions';
$apiKey = 'sk-JSEjknl289dBzb6HD1BdCeE256Ea44BbB13e1361E40b45D4';
set_time_limit(0);
$message = $_POST['message'];
writeLog($message);
// 请求参数，根据API文档调整
$system_prompt = file_get_contents('prompt.txt');
$exclude_file = ['cacert.pem','delete_student.php','db.php','database_sql.sql','save.php','delete_student_by_name.php','fetch_students.php','test.php','prompt.txt','sendtoai.php','kimireturn.txt','debug_prompt.txt','estimate_token.php','index_template.html','jquery-3.6.0.min.js','css2.css','log.txt'];
$project_file = listFilesAndContents($exclude_file);

$system_prompt = str_replace("[project file]",$project_file,$system_prompt);
// $system_prompt = $system_prompt."\n".$message;
file_put_contents("debug_prompt.txt","\n--------------------\n".$system_prompt."\n--------------------\n",FILE_APPEND);
$postData = [
    "model" => "gpt-3.5-turbo",
    // "max_tokens"=>20000,
    "messages" => [
        ["role" => "system", "content" => $system_prompt],
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
    writeLog("kimi response:".$response);
    // 解析响应数据
    $responseData = json_decode($response, true);

    if(isset($responseData['error'])){
        echo json_encode(['reply'=>[],'status'=>'error','msg'=>$responseData['error']['message'],'type'=>'kimierror']);
        exit();
    }

    $replyStr =  $responseData['choices'][0]['message']['content'];
    file_put_contents("kimireturn.txt","\n--------------------\n".$replyStr."\n--------------------\n",FILE_APPEND);
    $reply = json_decode($replyStr,true);
    // print($reply);exit();
    if($reply['type'] == 'site'){
        echo json_encode(['reply'=>$reply['site']['mission'],'status'=>'ok','msg'=>'执行成功','type'=>'site']);
    }else{
        // 执行SQL查询
        if (isset($reply['server']['sql']['query'])) {
            $sql = $reply['server']['sql']['query'];
            if ($conn->query($sql) === TRUE) {
                writeLog($sql." query executed successfully");
            } else {
                writeLog($sql."Error executing query: " . $conn->error );
            }
        }

        // 替换文件内容
        if (isset($reply['server']['files']['modify'])) {
            foreach ($reply['server']['files']['modify'] as $file) {
                $filename = $file['filename'];
                $newcontent = $file['newcontent'];
                if (file_put_contents($filename, $newcontent) !== false) {
                    writeLog("File $filename updated successfully");
                } else {
                    writeLog("Error updating file $filename");
                }
            }
        }
        //  // 替换文件内容
        //  if (isset($reply['server']['files']['index'])) {
        //     $index_file = $reply['server']['files']['index'];
        //     if(!empty($index_file['css'])){
        //         $css  = $index_file['css'];
        //     }else{
        //         $css = file_get_contents("index_css.txt");
        //         $css = explode("\n",$css);
        //         array_shift($css);
        //         $css = implode("\n",$css);
        //     }

        //     if(!empty($index_file['index_css'])){
        //         $css  = $index_file['index_css'];
        //     }else{
        //         $css = file_get_contents("index_css.txt");
        //         $css = explode("\n",$css);
        //         array_shift($css);
        //         $css = implode("\n",$css);
        //     }

        //     if(!empty($index_file['index_body'])){
        //         $body  = $index_file['index_body'];
        //     }else{
        //         $body = file_get_contents("index_body.txt");
        //         $body = explode("\n",$body);
        //         array_shift($body);
        //         $body = implode("\n",$body);
        //     }

        //     if(!empty($index_file['index_javascript'])){
        //         $js  = $index_file['index_javascript'];
        //     }else{
        //         $js = file_get_contents("index_javascript.txt");
        //         $js = explode("\n",$js);
        //         array_shift($js);
        //         $js = implode("\n",$js);
        //     }

        //     $index_template = file_get_contents("index_template.html");
        //     $index_template = str_replace("{{css}}",$css,$index_template);
        //     $index_template = str_replace("{{body}}",$body,$index_template);
        //     $index_template = str_replace("{{js}}",$js,$index_template);
        //     if (file_put_contents("index.html", $index_template) !== false) {
        //         writeLog("File index.html updated successfully");
        //     } else{
        //         writeLog("File index.html updated fail");
        //     }
    
        // }

        // 新增文件
        if (isset($reply['server']['files']['add'])) {
            foreach ($reply['server']['files']['add'] as $file) {
                $filepath = $file['filepath'];
                $content = $file['content'];
                if (file_put_contents($filepath, $content) !== false) {
                    writeLog("File $filepath created successfully");
                } else {
                    writeLog("Error creating file $filepath");
                }
            }
        }
        echo json_encode(['reply'=>[],'status'=>'ok','msg'=>'执行成功','type'=>'server']);
    }
   
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