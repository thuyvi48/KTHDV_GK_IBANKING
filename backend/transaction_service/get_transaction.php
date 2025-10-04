<?php
header("Content-Type: application/json");
require_once "db.php";

if (!isset($_GET['user_id'])) {
    echo json_encode(["success"=>false,"message"=>"Missing user_id"]);
    exit;
}

$userId = $_GET['user_id'];

$sql = "SELECT TRANSACTION_ID, USER_ID, PAYMENT_ID, BALANCE_AFTER, TYPE, CHANGE_AMOUNT, DESCRIPTION, CREATED_AT, STATUS
        FROM TRANSACTIONS
        WHERE USER_ID =?
        ORDER BY CREATED_AT DESC
        LIMIT 5";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
$status_map = [
    'PENDING' => 'Đang chờ xử lý',
    'DONE'    => 'Hoàn tất',
    'FAILED'  => 'Thất bại'
];

while ($row = $result->fetch_assoc()) {
    $transactions[] = [
        "transaction_id" => $row['TRANSACTION_ID'],
        "payment_id"     => $row['PAYMENT_ID'],
        "description"    => $row['DESCRIPTION'],
        "date"           => date('d/m/Y H:i', strtotime($row['CREATED_AT'])),
        "amount"         => (float)$row['CHANGE_AMOUNT'],
        "type"           => strtolower($row['TYPE']),
        "status"         => $status_map[$row['STATUS']] ?? $row['STATUS']
    ];
}

echo json_encode([
    "success" => true,
    "data"    => $transactions
]);

?>
