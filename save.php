<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root"; // 替换为你的数据库用户名
$password = "root"; // 替换为你的数据库密码
$dbname = "exam_db";
$port = 3307;

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname,$port);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    $stmt = $conn->prepare("INSERT INTO students (name) VALUES (?)");
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = '数据已保存';
    } else {
        $response['status'] = 'error';
        $response['message'] = '数据保存失败';
    }

    $stmt->close();
} else {
    $response['status'] = 'error';
    $response['message'] = '无效的请求方法';
}

$conn->close();

echo json_encode($response);
?>
