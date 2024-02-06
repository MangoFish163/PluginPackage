<?php

/**
 * 构建后台菜单管理
 */
class Wptplus_Menus
{
    public static function outPutSubmenu()
    {
        add_submenu_page(
            'options-general.php',
            'WPTPLUS', 
            'WPTPLUS', 
            'manage_options', 
            'WPTPLUS', 
            'Wptplus_Menus::page', 
            '99'
        );
    }
    public static function outPutMenus()
    {
        add_menu_page(
            'wptplus_main_menu', 
            'WPTPLUS', 
            'manage_options', 
            'wptplus_main_menu',
            // 'wptplus_main_page',
            'Wptplus_Menus::page',
            WPTPLUS_PLUGIN_URL . 'static/wptplus-logo.svg'
        );
        wp_enqueue_style( 'WPTplusSideLogoStylesheet', WPTPLUS_PLUGIN_URL.'static/wptplus.css', [], '1.0' );
    }
    public static function settings_link($links) {
        $settings_link = array('<a href="' . admin_url('admin.php?page=wptplus_main_menu') . '">'.'配置'.'</a>');
        return array_merge($links, $settings_link);
    }
    public static function tt_remove_options_page(){
        // remove_submenu_page('options-general.php','WPTPLUS');
    }
    /**
    * [page输出页面，判断当前用户是否有权限]
    */
    public static function page()
    {
        if ( !current_user_can('manage_options') )
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        self::outputHtml();
    }
    public static function outputHtml()
    {
        include_once WPTPLUS_PLUGIN_DIR.'templates/Wptplus_admin_template_file.php';
    }
}
