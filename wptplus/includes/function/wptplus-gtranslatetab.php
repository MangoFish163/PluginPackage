<?php

/**
 * 这里定义的方法主要针对与 GtranslateTab 插件
 *
 * @since 			1.0.0
 */

// 当短代码被调用时运行的函数
function HaiTao_SelectLanguage()
{
    include_once(WPTPLUS_PLUGIN_DIR . '/templates/gtranslatetab_template_file.php');
}

// 注册短代码
if (1 == get_transient('gtranslatetab_mu_open')) {
    // 检查前置插件是否存在且启用
    function checkGtranslateTabPluginActive()
    {
        if (is_plugin_active('gtranslate/gtranslate.php')) {
            // 插件已激活
            add_shortcode('HaiTaoLanguage', 'HaiTao_SelectLanguage');
        }
    }
    add_action('admin_init', 'checkGtranslateTabPluginActive');
} else {
    add_shortcode('HaiTaoLanguage', 'HaiTao_SelectLanguage');
}

