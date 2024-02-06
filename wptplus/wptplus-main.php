<?php
/*
 * @wordpress-plugin
 * Plugin Name:         WPTPLUS
 * Description:         WPTPLUS 是多个插件的集合体
 * Version:             2.0
 * Author:              Taotens
*/

// 试图直接访问该文件?终止操作
if (!defined('WPINC')) {
    die;
}

/**
 * 基本常量默认值
*/
if(!defined('WPTPLUS_PLUGIN_DIR'))
{
	define( 'WPTPLUS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    define( 'WPTPLUS_PLUGIN_URL', plugin_dir_url(__FILE__) );
    define( 'WPTPLUS_PLUGIN_FILE', __FILE__ );
    define( 'WPTPLUS_PLUGIN_NAME', 'wptplus' );
}



//Todo: 自动加载php文件
// require_once  __DIR__.'/includes/class-wptplus-tofeishu.php';	// 订单信息推送到飞书群


// 引入入口文件
require_once WPTPLUS_PLUGIN_DIR . 'includes/class-wptplus-plugin.php';
// 插件激活
function activate_Wp_Wptplus_Plugin() {

    
}

// 停用插件
function deactivate_Wp_Wptplus_Plugin() {
    
}
register_activation_hook( __FILE__, 'activate_Wp_Wptplus_Plugin' );
register_deactivation_hook( __FILE__, 'deactivate_Wp_Wptplus_Plugin' );
// 开始插件运行
function run_Wp_Wptplus_Plugin() {
	$plugin = new Wp_Wptplus_Plugin();
	$plugin->run();
    // 添加设置页面
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('Wptplus_Menus', 'settings_link'));
    // add_action('admin_menu', 'Wptplus_Menus::outPutSubmenu');
    add_action('admin_menu', 'Wptplus_Menus::outPutMenus');
    // add_action('admin_init', 'Wptplus_Menus::tt_remove_options_page');
}
run_Wp_Wptplus_Plugin();

