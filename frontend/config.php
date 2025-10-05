<?php
define("BASE_URL", "/KTHDV_GK_IBANKING/frontend/");

if (!function_exists("callAPI")) {
    function callAPI($method, $url, $data = false) {
        $ch = curl_init();

        switch (strtoupper($method)) {
            case "POST":
                curl_setopt($ch, CURLOPT_POST, 1);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;

            default: // GET
                if ($data && is_array($data)) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
                break;
        }

        // Thiết lập URL và các tùy chọn chung
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        // Nếu có lỗi cURL
        if ($error) {
            return ["error" => "Lỗi khi gọi API: $error"];
        }

        // Giải mã JSON
        $decoded = json_decode($response, true);

        // Nếu không phải JSON hợp lệ
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ["error" => "Phản hồi không hợp lệ từ API", "raw" => $response];
        }

        return $decoded;
    }
}
