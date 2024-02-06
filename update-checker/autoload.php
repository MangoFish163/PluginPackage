<?php
/*
 * @wordpress-plugin
 * Plugin Name:         UpdateChecker
 * Description:         在线更新功能
 * Version:             0.0.5
 * Author:              Taotens
*/

function activate_Wp_UpdateChecker_Plugin()
{
}
function deactivate_Wp_UpdateChecker_Plugin()
{
}
try {
    register_activation_hook(__FILE__, 'activate_Wp_UpdateChecker_Plugin');
    register_deactivation_hook(__FILE__, 'deactivate_Wp_UpdateChecker_Plugin');
} catch (\Throwable $th) {
}

/**
* 加载自动检查更新程序
*/
if (!defined('UPDATECHECKER_DATA')) {
    require_once(plugin_dir_path(__FILE__) . '/update-checker.php');
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    define('UPDATECHECKER_DATA', get_plugin_data(__FILE__));
    define('UPDATECHECKER_DIR_NAME', basename(plugin_dir_path(__FILE__)));
    define('UPDATECHECKER_DIR',plugin_dir_path(__FILE__));
    if (is_admin()) new UpdateChecker(plugin_basename(__FILE__));
}