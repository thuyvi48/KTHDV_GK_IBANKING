<?php 
header('Content-Type: application/json');

$service = $_GET['service'] ?? '';
$action  = $_GET['action'] ?? '';

switch ($service) {
    /* ---------------- USER SERVICE ---------------- */
    case 'user':
        if ($action === 'get') {
            $id = $_GET['id'] ?? '';
            if (!$id) {
                echo json_encode(["error" => "Thiếu tham số id"]);
                exit;
            }
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?id=" . urlencode($id);
            echo @file_get_contents($url) ?: json_encode(["error" => "Không thể kết nối user_service"]);
        } elseif ($action === 'list') {
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/list_users.php";
            echo @file_get_contents($url) ?: json_encode(["error" => "Không thể kết nối user_service"]);
        } else {
            echo json_encode(["error" => "Action user không hợp lệ"]);
        }
        break;

    /* ---------------- AUTH SERVICE ---------------- */
    case 'auth':
        if ($action === 'login') {
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/auth_service/login.php";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
            $response = curl_exec($ch);
            curl_close($ch);

            echo $response ?: json_encode(["error" => "Không thể kết nối auth_service"]);
        } else {
            echo json_encode(["error" => "Action auth không hợp lệ"]);
        }
        break;

    /* ---------------- OTP SERVICE ---------------- */
    case 'otp':
        if ($action === 'create') {
            $transaction_id = $_GET['transaction_id'] ?? '';
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/create_otp.php?transaction_id=" . urlencode($transaction_id);
            echo @file_get_contents($url) ?: json_encode(["error" => "Không thể kết nối otp_service"]);
        } elseif ($action === 'verify') {
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/verify_otp.php";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
            $response = curl_exec($ch);
            curl_close($ch);

            echo $response ?: json_encode(["error" => "Không thể kết nối otp_service"]);
        } else {
            echo json_encode(["error" => "Action otp không hợp lệ"]);
        }
        break;

    /* ---------------- TRANSACTION SERVICE ---------------- */
     case 'transaction':
        if ($action === 'list') {
            $user_id = $_GET['user_id'] ?? '';
            $url = "http://localhost/ibanking/backend/transaction_service/index.php?action=list&user_id=" . urlencode($user_id);
            $response = @file_get_contents($url);

            if ($response === false) {
                echo json_encode(["error" => "Không thể kết nối transaction_service"]);
            } else {
                echo $response;
            }
        } else {
            echo json_encode(["error" => "Action transaction không hợp lệ"]);
        }
        break;

    /* ---------------- PAYMENT SERVICE ---------------- */
    case 'payment':
        if ($action === 'create') {
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/payment_service/create_payment.php";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
            $response = curl_exec($ch);
            curl_close($ch);

            echo $response ?: json_encode(["error" => "Không thể kết nối payment_service"]);
        } elseif ($action === 'status') {
            $id = $_GET['id'] ?? '';
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/payment_service/get_payment_status.php?id=" . urlencode($id);
            echo @file_get_contents($url) ?: json_encode(["error" => "Không thể kết nối payment_service"]);
        } else {
            echo json_encode(["error" => "Action payment không hợp lệ"]);
        }
        break;

    /* ---------------- STUDENT SERVICE ---------------- */
    case 'student':
        if ($action === 'get') {
            $id = $_GET['id'] ?? '';
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/student_service/get_student.php?id=" . urlencode($id);
            echo @file_get_contents($url) ?: json_encode(["error" => "Không thể kết nối student_service"]);
        } elseif ($action === 'list') {
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/student_service/list_students.php";
            echo @file_get_contents($url) ?: json_encode(["error" => "Không thể kết nối student_service"]);
        } else {
            echo json_encode(["error" => "Action student không hợp lệ"]);
        }
        break;

    /* ---------------- TUITION SERVICE ---------------- */
    case 'tuition':
        if ($action === 'get') {
            $id = $_GET['id'] ?? '';
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/tuition_service/get_invoice.php?id=" . urlencode($id);
            echo @file_get_contents($url) ?: json_encode(["error" => "Không thể kết nối tuition_service"]);
        } elseif ($action === 'list') {
            $student_id = $_GET['student_id'] ?? '';
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/tuition_service/list_invoices.php";
            if ($student_id) {
                $url .= "?student_id=" . urlencode($student_id);
            }
            echo @file_get_contents($url) ?: json_encode(["error" => "Không thể kết nối tuition_service"]);
        } else {
            echo json_encode(["error" => "Action tuition không hợp lệ"]);
        }
        break;

    /* ---------------- DEFAULT ---------------- */
    default:
        echo json_encode(["error" => "Service không hợp lệ"]);
}
