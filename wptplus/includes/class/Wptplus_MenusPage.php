<?php
/**
 * 建立后台菜单管理
 */
class Wptplus_MenusPage
{
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
    public static function tofeishu()
    {
        if ( !current_user_can('manage_options') )
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?>
        <div style='padding:60px 0' class='explanation'>
               <h1 style='padding:20px 0'>插件详情</h1>
               <p class='introduce-item'>在飞书群聊中,添加自定义机器人到群聊111</p>
        </div>
       <?php
    }
}
?>