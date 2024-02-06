<?php

/**
 * 当插件被卸载时清理回收工作。
 * @package    wptplus
 */

// 直接访问就该文件退出。
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// 移除插件数据和记录
function wptplus_uninstall(){
    $transients = array(
        'customizesign_open',
        'gtranslatetab_open',
        'gtranslatetab_mu_open',
        'tofeishu_open'
    );
    foreach($transients as $val){
        delete_transient($val);
    }
    $option = array(
        'wptplus_order_to_feishu',
        'CustomizeSignOption',
    );
    foreach($option as $val){
        delete_option($val);
    }
}

try {
    // 移除插件数据
    wptplus_uninstall();
    // 清除所有已删除的缓存数据
    wp_cache_flush();
} catch (\Throwable $th) {
	// echo "0";
}