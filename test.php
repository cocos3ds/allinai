<?php

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


$system_prompt = file_get_contents('prompt.txt');

$project_file = listFilesAndContents(['cacert.pem','test.php','prompt.txt','sendtoai.php']);

$system_prompt = str_replace("[project file]",$project_file,$system_prompt);


file_put_contents('auto.txt',$system_prompt);