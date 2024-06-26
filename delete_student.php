<?php
require_once("db.php");//获取mysqli连接

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 设置响应头
header('Content-Type: application/json');

// 获取POST数据
$id = $_POST['id'];


// 检查是否有ID
if (empty($id)) {
    echo json_encode(['status' => 'error', 'message' => '缺少ID参数']);
    exit();
}

$id = $conn->real_escape_string($id);

// 删除考生
$sql = "DELETE FROM students WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => '考生删除成功']);
} else {
    echo json_encode(['status' => 'error', 'message' => '删除失败: ' . $conn->error]);
}

// 关闭连接
$conn->close();
?>
