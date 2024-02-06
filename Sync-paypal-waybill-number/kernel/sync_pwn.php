<?php
/*
* 同步paypal运单号
* Author: Mango Fish
* 沙盒 客户端ID: ARtj3QsFheJVw6i1X09HcncobYORxbDRc0OU7pzEi0G4_i9K1ILF_gnh_L5o6m8l5qsaDnL4K03eM_kL
* 沙盒 密钥: EEc0GGoYs7PoWOcewgNSzWeTRJqag5Dky4H2_oKRBC_GmrDuftN4Ogd2HnzqefT2HTSt0by7yXet9xVT
* 沙盒交易id  3DY67100A0250151W
*/
// 如果不是管理员页面，什么也不做
if (!class_exists('SyncPWN')) {
    class SyncPWN
    {
        private $basicUrl;
        private $ScopeUrl;
        private $environment;
        public function __construct()
        {
            // 如果不是管理员页面，什么也不做
            if (!is_admin()) return false;
            // 切换 沙盒/生产 环境
            $this->environment = false;
            $this->basicUrl = $this->environment ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
            $this->ScopeUrl = $this->environment ? '$this->ScopeUrl' : 'https://uri.sandbox.paypal.com';

            add_action('wp_ajax_sync_pwn_send_client', array($this, 'sync_pwn_send_client'));
            add_action('wp_ajax_sync_paypal_waybill_number', array($this, 'sync_paypal_waybill_number'));
        }
        /**
         * [输出管理菜单]
         */
        public static function outPutMenus()
        {
            add_menu_page(
                'Sync_paypal_number_main_menu',
                'Sync_PWN',
                'manage_options',
                'Sync_paypal_number_main_menu',
                'SyncPWN::page',
                SYNC_PAYPAL_WAYBILL_NUMBER_URL . '/static/logo.svg'
            );
            wp_enqueue_style('sync_pwnStyle', SYNC_PAYPAL_WAYBILL_NUMBER_URL . 'static/css/index.css', [], '1.0');
        }
        /**
         * [page输出页面，判断当前用户是否有权限访问]
         */
        public static function page()
        {
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            self::outputHtml();
        }
        public static function outputHtml()
        {
            self::enqueue_ui_script();
            include_once SYNC_PAYPAL_WAYBILL_NUMBER_DIR . 'templates/admin_template_file.php';
        }
        /**
         * [引入指定的CSS/js资源 / 已存在则跳过]
         */
        public static function enqueue_ui_script()
        {
            if (!wp_script_is('jquery', 'enqueued')) {
                echo '<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.1/jquery.js"></script>';
                wp_enqueue_script('jquery');
            }
            if (!wp_script_is('vue-script', 'enqueued')) {
                echo '<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>';
                wp_enqueue_script('vue-script');
            }

            if (!wp_style_is('bootstrap-style', 'enqueued')) {
                wp_enqueue_style('bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css', array(), null, false);
            }
            if (!wp_script_is('bootstrap-script', 'enqueued')) {
                wp_enqueue_script('bootstrap-script', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js', array('jquery'), null, false);
            }
        }
        /**
         * [保存获取客户端 ID 和客户端密钥 / 获取访问令牌]
         */
        public function sync_pwn_send_client()
        {
            $answer = array(
                'code'      => 101,
                'message'   => '请检查参数',
            );
            if (!empty($_POST)) {
                $res = $_POST['res'];
                if (empty($res)) {
                    echo json_encode($answer);
                    wp_die();
                } else if (isset($res['ClientIdInput'])) {
                    $ClientIdInput = trim($res['ClientIdInput']);
                    $ClientSecretInput = trim($res['ClientSecretInput']);
                    $CLIENT_CODE = $ClientIdInput . ":" . $ClientSecretInput;
                    $credentials = base64_encode($CLIENT_CODE);
                    $headers['Authorization'] = 'Basic ' . $credentials;
                    $headers['Content-Type'] = 'application/x-www-form-urlencoded';
                    $apiUrl = $this->basicUrl . '/v1/oauth2/token';
                    // $this->ScopeUrl/services/shipping/trackers/readwrite
                    $tokenScope = "$this->ScopeUrl/services/shipping/trackers/readwrite https://uri.sandbox.paypal.com/services/shipping/trackers/readwrite";
                    $body = 'grant_type=client_credentials&scope=' . urlencode($tokenScope);
                    $response = sendPost($apiUrl, $body, $headers);
                    $answer['code'] = $response['response']['code'];
                    $body = json_decode($response['body'], true);
                    if (isset($body['error_description'])) {
                        $answer['message'] =  $body['error_description'];
                    } else {
                        $answer['message'] = $response['response']['message'];
                    }
                    $answer['timeout'] = $body['expires_in'] - 300;
                    $this->log('sync_pwn_send_client - response', $response);
                    if ($response['response']['code'] == 200 && $res['RememberMe']) {
                        $information = array(
                            'Client'    => array('ClientIdInput' => $ClientIdInput, 'ClientSecretInput' => $ClientSecretInput),
                            'Delete'     => '',
                        );
                        // 记录配置
                        SyncPWN_submit_default_options('SyncPWN_Client_Information', $information);
                        SyncPWN_Up_options('SyncPWN_all_configuration_parameter', 'configuration_parameter', 'SyncPWN_Client_Information', '-1');
                        // 缓存access_token / 在官方过期前5分支换新
                        $cache_token = array(
                            'access_token'  => $body['access_token'],
                            'expires_in'    => $answer['timeout'],
                            'begin_cache'   => time(),
                        );
                        set_transient('SyncPWN_access_token', $cache_token, $answer['timeout']);
                        set_transient('SyncPWN_RememberAuthor', $CLIENT_CODE, 7 * DAY_IN_SECONDS);
                    } else if ($response['response']['code'] == 200) {
                        delete_transient('SyncPWN_RememberAuthor');
                        delete_transient('SyncPWN_access_token');
                        if (get_option('SyncPWN_Client_Information')) {
                            delete_option("SyncPWN_Client_Information");
                        }
                    }
                    $answer['type'] = '0';
                } else if (isset($res['RefreshToken'])) {
                    if ($res['RefreshToken'] == 'Refresh') {
                        $SyncPWN_cache_token = get_transient('SyncPWN_access_token');
                        if (false === $SyncPWN_cache_token) {
                            if (false === ($SyncPWN_RememberAuthor = get_transient('SyncPWN_RememberAuthor'))) {
                                $answer['code'] = 102;
                                $answer['message'] = '登录信息过期,请重新登录授权';
                                echo json_encode($answer);
                                wp_die();
                            }
                            $credentials = base64_encode($SyncPWN_RememberAuthor);
                            $headers['Authorization'] = 'Basic ' . $credentials;
                            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
                            $apiUrl = $this->basicUrl . '/v1/oauth2/token';
                            $tokenScope = "https://uri.sandbox.paypal.com/services/shipping/trackers/readwrite https://uri.sandbox.paypal.com/services/shipping/trackers/read";
                            $body = 'grant_type=client_credentials&scope=' . $tokenScope;
                            $response = sendPost($apiUrl, $body, $headers);
                            $answer['code'] = $response['response']['code'];
                            $body = json_decode($response['body'], true);
                            if (isset($body['error_description'])) {
                                $answer['message'] =  $body['error_description'];
                            } else {
                                $answer['message'] = $response['response']['message'];
                            }
                            if ($response['response']['code'] == 200) {
                                $cache_token = array(
                                    'access_token'  => $body['access_token'],
                                    'expires_in'    => $body['expires_in'] - 300,
                                    'begin_cache'   => time(),
                                );
                                // set_transient( 'SyncPWN_access_token', $body['access_token'], $body['expires_in']);
                                set_transient('SyncPWN_access_token', $cache_token, $body['expires_in'] - 300);
                            }
                        } else {
                            $answer['code'] = 200;
                            $answer['message'] = 'OK';
                            $answer['timeout'] = $SyncPWN_cache_token['begin_cache'] + $SyncPWN_cache_token['expires_in'] - time();
                            $answer['timeout'] = $answer['timeout'] < 0 ? 0 : $answer['timeout'];
                        }
                    } else {
                        $answer['code'] = 101;
                        $answer['message'] = '更新失败,请登出或稍后重试';
                        $answer['timeout'] = 0;
                    }
                    $answer['type'] =  '1';
                } else if (isset($res['RemoveToken'])) {
                    delete_transient('SyncPWN_RememberAuthor');
                    delete_transient('SyncPWN_access_token');
                    if (get_option('SyncPWN_Client_Information')) {
                        delete_option("SyncPWN_Client_Information");
                    }
                    $answer['code'] = 200;
                    $answer['message'] =  '已登出';
                    $answer['type'] =  '2';
                }
            }
            $this->log('sync_pwn_send_client', $answer);
            echo json_encode($answer);
            wp_die();
        }
        /**
         * [添加跟踪]
         */
        public function sync_paypal_waybill_number()
        {
            // 请求不是来自管理员页面 什么也不做
            if (!is_admin()) wp_die();
            $answer = array(
                'code'      => 101,
                'message'   => '请检查参数',
                'type'      => '3'
            );
            // 请求参数空 什么也不做
            if (empty($_POST) || !isset($_POST['res']) || !empty($_GET)) {
                echo json_encode($answer);
                wp_die();
            }

            $resource = $_POST['res'];
            $answer['transaction_id'] = isset($resource['transaction_id']) ? $resource['transaction_id'] : 'undefined';
            $answer['message'] = '添加成功';
            // $SyncPWN_RememberAuthor = get_transient('SyncPWN_RememberAuthor'); //用户信息
            $SyncPWN_access_token = get_transient('SyncPWN_access_token');
            if (false === $SyncPWN_access_token) {
                $answer['message'] = 'token过期或不可用请获取最新Token后重试';
            } else {
                $allowable = array('transaction_id', 'tracking_number', 'tracking_number_type', 'status', 'shipment_date', 'carrier', 'carrier_name_other', 'notify_buyer');
                $tracker = array();
                if ($resource['carrier'] != 'OTHER' && isset($resource['carrier_name_other'])){
                    unset($resource['carrier_name_other']);
                }
                foreach ($resource as $key => $value) {
                    if (in_array($key, $allowable)) $tracker[$key] = $value;
                }
                if (empty($tracker)) {
                    $answer['message'] = '数据无效或不合法,参照PayPal官方要求检查后重试';
                    echo json_encode($answer);
                    wp_die();
                }

                $apiUrl = $this->basicUrl . '/v1/shipping/trackers-batch';
                $headers = array(
                    'Authorization' => 'Bearer ' . $SyncPWN_access_token['access_token'],
                    'Content-Type' => 'application/json',
                );
                $this->log('sync_paypal_waybill_number 1||', $headers);
                $body = array(
                    'trackers' => array(
                        $tracker
                    )
                );
                $this->log('sync_paypal_waybill_number 2', $body);
                $response = sendPost($apiUrl, json_encode($body), $headers);
                $this->log('sync_paypal_waybill_number 3', $response);
                $body = json_decode($response['body'], true);
                $answer['code'] = 205;
                if (isset($body['error_description'])) {
                    // $answer['code'] = 105;
                    $answer['message'] =  $body['error_description'];
                } else if (isset($body['errors'][0]['message'])) {
                    // $answer['code'] = 105;
                    $answer['message'] = $body['errors'][0]['message'];
                } else {
                    $answer['message'] = $body['message'];
                }
                $this->log('sync_paypal_waybill_number 4', $body);
            }
            echo json_encode($answer);
            wp_die();
        }

        /**
         * [记录日志]
         */
        public function log(...$args)
        {
            $timestamp = date('Y-m-d H:i:s');
            $log_message = $timestamp . ' - ' . implode(' ', array_map('json_encode', $args)) . PHP_EOL;
            $log_file = SYNC_PAYPAL_WAYBILL_NUMBER_DIR . '/logs/look.log';
            file_put_contents($log_file, $log_message, FILE_APPEND);
        }
    }
}



// 您好，我现在正在开发同步订单号到PayPal后台的功能。但是现在当我在沙盒环境下使用https://api-m.sandbox.paypal.com/v1/shipping/trackers-batch接口时。我总是得到如下的回复
// {"errors":[{"name":"NOT_AUTHORIZED","message":"Authorization failed due to insufficient permissions","debug_id":"bbb7a723fc69b","details":[{"location":"body"}]}]}
// 我的请求头信息如下
// $headers = array(
// 'Authorization' => 'Bearer ' . $operation_token,
// 'Content-Type' => 'application/json',
// );
// 其中$operation_token是我请求
// https://api-m.sandbox.paypal.com/v1/oauth2/token
// 认证之后返回的access_token值

// 我想咨询一下，沙盒环境是否具有所以的测试权限(比如 添加跟踪)。如果是的话，那么我的问题出在哪里 导致了 NOT_AUTHORIZED 的问题。debug_id:bbb7a723fc69b
// 任何指导和帮助我都非常感谢
// --------------------------------------------------------
// Hello, I am now developing the function of synchronizing order number to PayPal background. But now when I am in a sandbox environment using the https://api-m.sandbox.paypal.com/v1/shipping/trackers-batch interface. I always get the following response
// {"errors":[{"name":"NOT_AUTHORIZED","message":"Authorization failed due to insufficient  permissions","debug_id":"bbb7a723fc69b","details":[{"location":"body"}]}]}
// My request header information is as follows
// $headers = array(
// 'Authorization' =&gt;  'Bearer ' . $operation_token,
// 'Content-Type' =&gt;  'application/json',
// );
// Where $operation_token is my request
// https://api-m.sandbox.paypal.com/v1/oauth2/token
// access_token value returned after authentication

// I would like to ask if the sandbox environment has all the testing permissions (such as adding traces). If so, then my question is what caused the NOT_AUTHORIZED problem. debug_id:bbb7a723fc69b
// Any guidance and help would be greatly appreciated