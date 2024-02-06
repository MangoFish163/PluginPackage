<?php
function testExcel()
{
    // $order = wc_get_order( 889 );
    $order_data = array(
        'date_created' => date('Y-m-d H:m:s'),
        'order_status' => 'OK',
    );; // 获取订单数据
    if (!empty($order_data)) {
        $date_created = $order_data['date_created']; // 获取创建日期
        $order_status = $order_data['status']; // 获取订单状态
        $csv_data = array();
        $csv_data[] = array(
            'date_created',
            'status',
            'name',
            'nickName',
        );
        for ($i = 0; $i < 10; $i++) {
            $csv_data[] = array(
                $date_created,
                $order_status,
                '小A_' . $i,
                'XIao_' . $i,
            );
        }
        $csv_file = fopen(SYNC_PAYPAL_WAYBILL_NUMBER_DIR . '/static/data.csv', 'w');

        foreach ($csv_data as $line) {
            fputcsv($csv_file, $line);
        }

        fclose($csv_file);
    }
}

function sync_pwn_jwb()
{
    $payload = [
        'iss' => '',
        // 'payer_id' => 'PYK7ZVZLM47VN',
        'email' => 'sb-m478fo29164623@business.example.com'
    ];
    $encodedPayload = json_encode($payload);
    // 构建未签名的 JSON Web 令牌
    // return base64_encode($encodedPayload);
    return $encodedPayload;
}
function sendPost($apiUrl, $body, $headers = array(), $timeout = 30, $cookies = array(), $blocking = true)
{
    $args = array(
        'method'    => 'POST',
        'timeout'   => $timeout,
        'blocking'  => $blocking, // 同步阻塞
        'body'      => $body,
        'headers'   => $headers,
        'cookies'   => $cookies,
    );
    return wp_remote_post($apiUrl, $args);
}
function SyncPWN_submit_default_options($optionKey, $information = '')
{
    $default = get_option($optionKey);
    if ($default == '' && !empty($information)) {
        $default = $information;
        update_option($optionKey, $information);
    } else if ($default == '') {
        // 设置默认数据
        $default = array(
            'SendVal'    => '',
            'Delete'     => '',
        );
        update_option($optionKey, $default);
    }
    return $default;
}
function SyncPWN_Up_options($optionKey, $SendKey = '', $SendVal = '', $k = '')
{
    $default = get_option($optionKey);
    if ($default != '' && $SendKey !== '') {
        $type = gettype($default[$SendKey]);
        if ($type == 'array' || $type == object) {
            if (empty($k)) {
                $default[$SendKey][] = $SendVal;
            } else {
                $default[$SendKey][$k] = $SendVal;
            }
        } else {
            $default[$SendKey] = $SendVal;
        }
    } else if ($default == '') {
        // 设置默认数据
        if (empty($k)) {
            $default = array(
                $SendKey    => $SendVal,
                'Delete'    => '',
            );
        } else if ($k == '-1') {
            $default = array(
                $SendKey    => array($SendVal),
                'Delete'    => '',
            );
        } else {
            $default = array(
                $SendKey    => array($k => $SendVal),
                'Delete'    => '',
            );
        }
    }
    update_option($optionKey, $default);
    return $default;
}





// 调试
// 创建测试订单
function sync_paypal_waybill_number2()
{
    // 请求不是来自管理员页面 什么也不做
    if (!is_admin()) wp_die();
    $answer = array(
        'code'      => 101,
        'message'   => '请检查参数',
        'type'      => '4'
    );
    $apiUrl = 'https://api-m.sandbox.paypal.com/v2/checkout/orders';
    $SyncPWN_cache_token = get_transient('SyncPWN_access_token');
    $headers = array(
        'Authorization' => 'Bearer ' . $SyncPWN_cache_token['access_token'],
        'Content-Type' => 'application/json',
        'PayPal-Request-Id' => 'order' . time(),
    );
    $body = file_get_contents(SYNC_PAYPAL_WAYBILL_NUMBER_DIR . '/static/testOrder.json');
    $response = sendPost($apiUrl, $body, $headers);
    $body = json_decode($response['body']);
    looklog(gettype($response), '===>', $body);
    $answer['message'] = 'sync_paypal_waybill_number2';
    echo json_encode($answer);
    wp_die();
}
add_action('wp_ajax_sync_paypal_waybill_number2', 'sync_paypal_waybill_number2');
// 获取订单信息
function sync_paypal_distance_order_info($transaction_id, $accessToken)
{
    // 获取订单详情
    $apiUrl = "https://api-m.sandbox.paypal.com/v2/checkout/orders?transaction_id=$transaction_id";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
}
// 日志
function looklog(...$args)
{
    $timestamp = date('Y-m-d H:i:s');
    $log_message = $timestamp . ' - ' . implode(' ', array_map('json_encode', $args)) . PHP_EOL;
    $log_file = SYNC_PAYPAL_WAYBILL_NUMBER_DIR . '/logs/look.log';
    file_put_contents($log_file, $log_message, FILE_APPEND);
}
