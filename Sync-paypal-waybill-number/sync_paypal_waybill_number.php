<?php
/*
 * @wordpress-plugin
 * Plugin Name:         Sync paypal waybill-number
 * Description:         同步paypal运单号
 * Version:             0.0.1
 * Author:              Taotens
*/
// Sync_paypal_waybill_number

if (!defined('SYNC_PAYPAL_WAYBILL_NUMBER_DIR')) {
    define('SYNC_PAYPAL_WAYBILL_NUMBER_DIR', plugin_dir_path(__FILE__));
    define('SYNC_PAYPAL_WAYBILL_NUMBER_URL', plugin_dir_url(__FILE__));
}
require_once(SYNC_PAYPAL_WAYBILL_NUMBER_DIR . '/kernel/function.php');

function activate_Wp_Sync_paypal_waybill_number_Plugin()
{
    // testExcel();
}
function deactivate_Wp_Sync_paypal_waybill_number_Plugin()
{
}
try {
    register_activation_hook(__FILE__, 'activate_Wp_Sync_paypal_waybill_number_Plugin');
    register_deactivation_hook(__FILE__, 'deactivate_Wp_Sync_paypal_waybill_number_Plugin');
    require_once(SYNC_PAYPAL_WAYBILL_NUMBER_DIR . '/kernel/sync_pwn.php');
    $SyncPWN = new SyncPWN();
    add_action('admin_menu', 'SyncPWN::outPutMenus');
} catch (\Throwable $th) {
    $last_error = error_get_last();
    $errMsg = array(
        'Type'=>$last_error['type'],
        'Message'=>$last_error['message'],
        'File'=>$last_error['file'],
        'Line'=>$last_error['line'],
    );
    looklog($errMsg);
}
