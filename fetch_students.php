<?php
//这是获取考生列表的文件，目前会返回考生id和name
header('Content-Type: application/json');

require_once("db.php");//获取mysqli连接

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$response = array();
//查询考生的信息，获取id和考生的name
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
