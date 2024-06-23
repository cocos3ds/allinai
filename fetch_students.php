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

$sql = "SELECT id, name FROM students";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $students = array();
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $response['status'] = 'success';
    $response['data'] = $students;
} else {
    $response['status'] = 'success';
    $response['data'] = array();
}

$conn->close();

echo json_encode($response);
?>
