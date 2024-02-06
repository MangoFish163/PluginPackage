<?php

/**
 * 这里定义的方法全局可用，当然，你没有必要把每个函数方法都定义在这里
 *
 * @since 			1.0.0
 */

// 更新customizesign配置
function customizesign_save_config()
{
    if (!empty($_POST)) {
        $res = $_POST['res'];
        if ($res['plugin'] != 3) {
            echo 102;
            wp_die();
        } elseif ( empty($res['open']) ) {
            echo 201;
            delete_transient('customizesign_open');
            wp_die();
        } elseif (preg_match("/[\x7f-\xff]/", $res['signKey'])) {
            echo 104;
            wp_die();
        }
        if (1 != get_transient('customizesign_open')) {
            set_transient('customizesign_open', 1, 3 * YEAR_IN_SECONDS);
        }
        if (empty($res['signName'])) {
            echo 202;
            wp_die();
        }
        // 获取选项
        $DBdata = get_option('CustomizeSignOption');
        if ($DBdata == '') {
            add_option('CustomizeSignOption', [], '', false);
        }
        $res['signKey'] = preg_replace('/\s+/', '', $res['signKey']); 
        if (empty($res['signUpdate'])) {
            if ($res['signKey'] === 0 || empty($res['signKey']) || $res['signKey'] === '0' || $res['signKey'] === false) {
                echo 105;
            }else{
                $DBdata[$res['signKey']] = array(
                    'signName' => $res['signName'],
                    'signKey' => $res['signKey'],
                    'signColor' => $res['signColor'],
                );
                echo 200;
            }
        } else {
            if ($res['signUpdate'] != $res['signKey']) {
                unset($DBdata[$res['signUpdate']]);
            }
            if ($res['signKey'] != 0 && empty($res['signKey'])) {
                $DBdata[$res['signKey']] = array(
                    'signName' => $res['signName'],
                    'signKey' => $res['signKey'],
                    'signColor' => $res['signColor'],
                );
                echo 203;
            }else{
                echo 204;
            }
        }
        // 更新记录
        update_option('CustomizeSignOption', $DBdata);
        // Tlook($DBdata);
    } else {
        echo 101;
    }
    wp_die();
}
add_action('wp_ajax_customizesign_save_config', 'customizesign_save_config');
add_action('wp_ajax_nopriv_customizesign_save_config', 'customizesign_save_config');

// 更新gtranslatetab配置
function gtranslatetab_save_config()
{
    if (!empty($_POST)) {
        $res = $_POST['res'];
        // Tlook($res);
        if ($res['plugin'] != 1) {
            echo 102;
            wp_die();
        } elseif (empty($res['open'])) {
            
            delete_transient('gtranslatetab_open');
            if ( empty($res['mu_open']) ) {
                delete_transient('gtranslatetab_mu_open');
            }
            echo 201;
            wp_die();
        } elseif ( empty($res['mu_open']) ) {
            delete_transient('gtranslatetab_mu_open');
            echo 203;
            wp_die();
        } else{
            if (1 != get_transient('gtranslatetab_open')) {
                set_transient('gtranslatetab_open', 1, 3 * YEAR_IN_SECONDS);
                echo 202;
            }
            if (1 != get_transient('gtranslatetab_mu_open')) {
                set_transient('gtranslatetab_mu_open', 1, 3 * YEAR_IN_SECONDS);
                echo 203;
            }
        }
    } else {
        echo 101;
    }
    wp_die();
}
add_action('wp_ajax_gtranslatetab_save_config', 'gtranslatetab_save_config');
add_action('wp_ajax_nopriv_gtranslatetab_save_config', 'gtranslatetab_save_config');

// 更新 tofeishu 配置
function tofeishu_save_config()
{
    if (!empty($_POST)) {
        $res = $_POST['res'];
        // Tlook($res);
        if ($res['plugin'] != 2) {
            echo 102;
            wp_die();
        } elseif (empty($res['open'])) {
            delete_transient('tofeishu_open');
            echo 201;
            wp_die();
        } else {
            if (1 != get_transient('tofeishu_open')) {
                set_transient('tofeishu_open', 1, 3 * YEAR_IN_SECONDS);
            }
            // 获取选项
            $DBdata = get_option('wptplus_order_to_feishu');
            if (!empty($res['feishu_webhook']) && $res['feishu_webhook']!=$DBdata) {
                if (empty($DBdata)) {
                    add_option('wptplus_order_to_feishu', '', '', false);
                }
                $DBdata = $res['feishu_webhook'];
                // 更新记录
                update_option('wptplus_order_to_feishu', $DBdata);
                echo 203;
            }else{
                echo 202;
            }
        }
    } else {
        echo 101;
    }
    wp_die();
}
add_action('wp_ajax_tofeishu_save_config', 'tofeishu_save_config');
add_action('wp_ajax_nopriv_tofeishu_save_config', 'tofeishu_save_config');

// 更新 产品属性过滤产品 配置
function BatchAlterProductSort_save_config()
{
    {
        if (!empty($_POST)) {
            $res = $_POST['res'];
            // Tlook($res);
            if ($res['plugin'] != 4) {
                echo 102;
                wp_die();
            } elseif (empty($res['open'])) {
                
                delete_transient('BatchAlterProductSort_open');
                echo 201;
                wp_die();
            } elseif (1 != get_transient('BatchAlterProductSort_open')) {
                    set_transient('BatchAlterProductSort_open', 1, 3 * YEAR_IN_SECONDS);
                    echo 202;
                }
            }
        } 
        wp_die();
}
add_action('wp_ajax_BatchAlterProductSort_save_config', 'BatchAlterProductSort_save_config');
add_action('wp_ajax_nopriv_BatchAlterProductSort_save_config', 'BatchAlterProductSort_save_config');

// 获取配置
function customizesign_get_config()
{
    // 获取选项
    $DBdata = get_option('CustomizeSignOption');
    if ($DBdata == '') {
        add_option('CustomizeSignOption', [], '', false);
        $DBdata = [];
    }
    // Tlook($DBdata);
    return $DBdata;
}


// 记录插件运行时的 主动/被动 抛出的异常
if (!function_exists('tDbug')) {
    function tDbug($errorMsg, $method = ' wptplus插件未指定 ')
    {
        // 记录当前插件的异常
        $out = '===================BEGIN====================' . PHP_EOL;
        $out .= 'Time   : ' . date("Y/m/d H:i:s") . PHP_EOL;
        $out .= 'Plugin :' . 'tongtool插件' . PHP_EOL;
        if (gettype($errorMsg) == 'string') {
            $out .= 'Error: ' . $errorMsg . PHP_EOL;
        } else {
            $out .= 'Error: ' . json_encode($errorMsg) . PHP_EOL;
        }
        $out .= 'method :' . $method . PHP_EOL;
        $out .= '=====================END====================' . PHP_EOL;
        file_put_contents(WPTPLUS_PLUGIN_DIR . '/logs/Fun_error_log.txt', $out . PHP_EOL, FILE_APPEND);
    }
}


// look look 参数内容
function Tlook($msg, $extra = '', $APPEND = false)
{
    if (gettype($msg) != 'string') {
        $msg = json_encode($msg);
        if ($extra != '') {
            $extra = gettype($extra) == 'string' ? $extra : json_encode($extra);
            $msg = $extra . ' | ' . $msg;
        }
        if ($APPEND) {
            file_put_contents(WPTPLUS_PLUGIN_DIR . 'logs/look.json',  $msg . PHP_EOL);
        } else {
            file_put_contents(WPTPLUS_PLUGIN_DIR . 'logs/look.json',  $msg . PHP_EOL, FILE_APPEND);
        }
    } else {
        if ($extra != '') {
            $extra = gettype($extra) == 'string' ? $extra : json_encode($extra);
            $msg = $extra . ' | ' . $msg;
        }
        if ($APPEND) {
            file_put_contents(WPTPLUS_PLUGIN_DIR . 'logs/look.json', $msg . PHP_EOL);
        } else {
            file_put_contents(WPTPLUS_PLUGIN_DIR . 'logs/look.json', $msg . PHP_EOL, FILE_APPEND);
        }
    }
}