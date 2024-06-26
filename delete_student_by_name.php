<?php
require_once("db.php"); //获取mysqli连接

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 设置响应头
header('Content-Type: application/json');

// 获取POST数据
$name = $_POST['name'];


// 检查是否有name
if (empty($name)) {
    echo json_encode(['status' => 'error', 'message' => '缺少name参数']);
    exit();
}

$name = $conn->real_escape_string($name);

// 删除考生
$sql = "DELETE FROM students WHERE name = '$name'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => '考生删除成功']);
} else {
    echo json_encode(['status' => 'error', 'message' => '删除失败: ' . $conn->error]);
}

// 关闭连接
$conn->close();
?>
