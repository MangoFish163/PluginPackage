      
<?php
/**
 * 这里定义的方法主要针对与 OrderToFeishu 插件
 *
 * @since 			1.0.0
 */
// if (class_exists('WPTPLUS_Tofeishu')) return;
class WPTPLUS_Tofeishu
{
    /**
     * Instance of this class.
     */
    protected static $instance = null;
    /**
    * Return an instance of this class.
    */
    public static function get_instance()
    {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self();
            self::$instance->do_hooks();
        }
        return self::$instance;
    }
    /**
     * Handle WP actions and filters.
     */
    private function do_hooks()
    {
        add_action('woocommerce_new_order', array($this, 'schedule_feishu_event'));
        add_action('wporderiofo_to_feishu_event', array($this, 'wporderiofo_to_feishu')); 
    }


    public function schedule_feishu_event($oldOrderId)
    {
        wp_schedule_single_event(time() + 5, 'wporderiofo_to_feishu_event',array($oldOrderId));
    }

    public function wporderiofo_to_feishu($order_id)
    {
        $feishu_webhook_url = get_option('wptplus_order_to_feishu', '');
        $url = home_url() . '/wp-admin/post.php?post=' . $order_id . '&action=edit';
        $site_title = get_bloginfo('name');
        $order = wc_get_order($order_id);
        $order_data = array(
            'order_id'      => $order->get_id(),
            'order_total'   => $order->get_total(),
            'creation_date' => $order->get_date_created()->format('Y-m-d H:i:s'),
        );

        // Get customer details
        $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        $shipping_country = $order->get_shipping_country();

        // Get product details
        $products = $order->get_items();
        $product_info = array();
        $product_quantities = 0; // Initialize product quantity
        foreach ($order->get_items() as $product) {
            $product_name = $product->get_name();
            $product_quantity = $product->get_quantity();
            $product_info[] = "{$product_name} × {$product_quantity}  (件)";
            $product_quantities += $product_quantity; // Increment product quantity
        }
        $product_info_combined = implode("  \n  ", $product_info);
        // Get payment method
        $payment_method = $order->get_payment_method(); // 获取付款方式 ID
        $payment_title = $order->get_payment_method_title(); // 获取付款方式名称

        $user_ip = $_SERVER['REMOTE_ADDR'];

        $message_data = array(
            'msg_type' => 'text',
            'content'  => array(
                'text' => "网站名称 : {$site_title}\n订单创建时间 : {$order_data['creation_date']}\n订单 ID : {$order_data['order_id']}\n客户名称 : {$customer_name}\n收货国家 : {$shipping_country}\n订单产品信息 : {$product_info_combined}\n订单产品总件数 : {$product_quantities}\n订单金额 : {$order_data['order_total']}\n支付方式ID : {$payment_method}\n支付方式名称 : {$payment_title}\n订单地址 : {$url}\n用户IP : {$user_ip}\n",
            ),
        );

        $response = wp_remote_post($feishu_webhook_url, array(
            'headers' => array('Content-Type' => 'application/json'),
            'body'    => json_encode($message_data),
        ));

        if (is_wp_error($response)) {
            error_log('Plugin-Name: Wp-OrderAlerts-To-Feishu: ' . $response->get_error_message());
        } else {
            $body = wp_remote_retrieve_body($response);
            $decoded_body = json_decode($body, true);
        }
    }
}