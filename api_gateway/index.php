<?php 
header('Content-Type: application/json');

$service = $_GET['service'] ?? '';
$action  = $_GET['action'] ?? '';

switch ($service) {
    /* ---------------- USER SERVICE ---------------- */
case 'user':
    if ($action === 'get_user') {
        $user_id = $_GET['user_id'] ?? '';
        if (!$user_id) {
            echo json_encode(["error" => "Thiếu tham số user_id"]);
            exit;
        }
        $url = "http://localhost/KTHDV_GK_IBANKING/backend/user_service/get_user.php?user_id=" . urlencode($user_id);
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

            echo $response;
        } elseif ($action === 'reset_password') {
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/auth_service/reset_password.php";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
            $response = curl_exec($ch);

            if ($response === false) {
                echo json_encode(["error" => "Không thể kết nối auth_service"]);
            } else {
                echo $response;
            }
            curl_close($ch);
        } else {
            echo json_encode(["error" => "Action auth không hợp lệ"]);
        }
        break;

    /* ---------------- OTP SERVICE ---------------- */
    case 'otp':
        if ($action === 'send') {
            $input = file_get_contents("php://input");
            $url   = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/send_otp.php";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
            $response = curl_exec($ch);

            if ($response === false) {
                echo json_encode(["error" => "Không thể kết nối tới otp_service"]);
            } else {
                echo $response;
            }
            curl_close($ch);

        } elseif ($action === 'verify') {
            $input = file_get_contents("php://input");
            $url   = "http://localhost/KTHDV_GK_IBANKING/backend/otp_service/verify_otp.php";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
            $response = curl_exec($ch);

            if ($response === false) {
                echo json_encode(["error" => "Không thể kết nối tới otp_service"]);
            } else {
                echo $response;
            }
            curl_close($ch);

        } else {
            echo json_encode(["error" => "Action otp không hợp lệ"]);
        }
        break;

    /* ---------------- TRANSACTION SERVICE ---------------- */
case 'transaction':
    if ($action === 'get_transaction') {
        $user_id = $_GET['user_id'] ?? '';
        $limit   = $_GET['limit'] ?? '';
        $url = "http://localhost/KTHDV_GK_IBANKING/backend/transaction_service/list_transaction.php?user_id=" . urlencode($user_id);
        if ($limit) {
            $url .= "&limit=" . urlencode($limit);
        }

        if ($service === 'transaction' && $action === 'recent') {
            $user_id = $_GET['user_id'] ?? '';
            if (!$user_id) {
                echo json_encode(['success'=>false,'message'=>'Missing user_id']);
                exit;
            }

            // Gọi service backend
            $url = __DIR__ . '/../backend/transaction_service/get_transaction.php?user_id=' . urlencode($user_id);
            $response = @file_get_contents($url);
            $transactionsRaw = json_decode($response, true) ?? [];

            // Map trạng thái và type
            $status_map = [
                'PENDING' => 'Đang chờ xử lý',
                'DONE'    => 'Hoàn tất',
                'FAILED'  => 'Thất bại'
            ];

            $recent_transactions = [];
            foreach ($transactionsRaw as $t) {
                $recent_transactions[] = [
                    'transaction_id' => $t['TRANSACTION_ID'],
                    'description'    => $t['DESCRIPTION'],
                    'date'           => date('d/m/Y H:i', strtotime($t['CREATED_AT'])),
                    'amount'         => $t['CHANGE_AMOUNT'],
                    'type'           => strtolower($t['TYPE']), // online_shopping, transfer...
                    'status'         => $status_map[$t['STATUS'] ?? 'PENDING'] ?? ($t['STATUS'] ?? 'Chưa xác định')
                ];
            }

            echo json_encode(['success'=>true,'data'=>$recent_transactions]);
            exit;
        }
    }

        break;

    /* ---------------- PAYMENT SERVICE ---------------- */
   case 'payment':
        if ($action === 'list') {
            $user_id = $_GET['user_id'] ?? '';
            if (!$user_id) {
                echo json_encode(['success'=>false,'message'=>'Missing user id']);
                exit;
            }
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/transaction_service/list_payments.php?user_id=" . urlencode($user_id);
            $response = @file_get_contents($url);
            echo $response ?: json_encode(['success'=>false,'message'=>'Cannot connect payment_service']);
        } elseif ($action === 'create') {
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/transaction_service/create_payment.php";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
            $response = curl_exec($ch);
            curl_close($ch);
            echo $response ?: json_encode(["success"=>false,"message"=>"Cannot connect transaction_service"]);
        } else {
            echo json_encode(['success'=>false,'message'=>'Invalid action for payment']);
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

        } elseif ($action === 'get_invoice') {
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/student_service/get_invoice.php";
            $opts = [
                "http" => [
                    "method"  => "POST",
                    "header"  => "Content-Type: application/json",
                    "content" => file_get_contents("php://input")
                ]
            ];
            $context = stream_context_create($opts);
            echo @file_get_contents($url, false, $context) ?: json_encode(["error" => "Không thể kết nối student_service"]);

        } elseif ($action === 'update_invoice') {
            $url = "http://localhost/KTHDV_GK_IBANKING/backend/student_service/update_invoice.php";
            $opts = [
                "http" => [
                    "method"  => "POST",
                    "header"  => "Content-Type: application/json",
                    "content" => file_get_contents("php://input")
                ]
            ];
            $context = stream_context_create($opts);
            echo @file_get_contents($url, false, $context) ?: json_encode(["error" => "Không thể kết nối student_service"]);

        } else {
            echo json_encode(["error" => "Action student không hợp lệ"]);
        }
        break;

    /* ---------------- TUITION SERVICE ---------------- */

    /* ---------------- DEFAULT ---------------- */
    default:
        echo json_encode(["error" => "Service không hợp lệ"]);
}
