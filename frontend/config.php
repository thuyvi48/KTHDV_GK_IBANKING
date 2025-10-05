<?php
define("BASE_URL", "/KTHDV_GK_IBANKING/frontend/");

// Hàm gọi API Gateway / Service
if (!function_exists("callAPI")) {
    function callAPI($method, $url, $data = false) {
        $ch = curl_init();
        switch (strtoupper($method)) {
            case "POST":
                curl_setopt($ch, CURLOPT_POST, 1);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                }
                break;
            default: // GET
                if ($data && is_array($data)) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
                break;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }
}
