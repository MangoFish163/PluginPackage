<?php

/**
 * 这里定义的方法主要针对与 CustomizeSign 插件
 *
 * @since 			1.0.0
 */

// 遗留-用于基于cpt的订单
add_filter('manage_edit-shop_order_columns', 'misha_order_items_column');
// 基于hpos的订单
add_filter('manage_woocommerce_page_wc-orders_columns', 'misha_order_items_column');
function misha_order_items_column($columns)
{
    $columns = array_slice($columns, 0, 4, true)
        + array('customizesign_column' => '订单标签')
        + array_slice($columns, 4, NULL, true);
    return $columns;
}


// 遗留-用于基于cpt的订单
add_action('manage_shop_order_posts_custom_column', 'customize_sign_populate_order_items_column', 25, 2);
// 基于hpos的订单
add_action('manage_woocommerce_page_wc-orders_custom_column', 'customize_sign_populate_order_items_column', 25, 2);
function customize_sign_populate_order_items_column($column_name, $order_or_order_id)
{

    // 兼容基于cpt的订单
    $order = $order_or_order_id instanceof WC_Order ? $order_or_order_id : wc_get_order($order_or_order_id);
    if ('customizesign_column' === $column_name) {
        $customize_sign_column_data = '';
        $customize_sign = wp_cache_get($order->get_id(), 'customizesign_cache_column');
        $upCache = false;
        if ($customize_sign === false) {
            $upCache = true;
            $customize_sign = get_post_meta($order->get_id(), 'customize_sign', true);

        }
        if (gettype($customize_sign) == 'string') {
            $customize_sign = json_decode($customize_sign);
        }
        if (gettype($customize_sign) == 'array' || gettype($customize_sign) == 'object') {
            foreach ($customize_sign as $val) {
                $customize_sign_column_data .= '<span class="layui-badge layui-bg-' . $val['signColor'] . '">' . $val['signName'] . '</span><br />';
            }
            if ($upCache) {
                // 随机 25h ~ 55h 的过期时间，避免缓存雪崩
                $Timeout = mt_rand(100, 200);
                wp_cache_set($order->get_id(), $customize_sign, 'customizesign_cache_column', $Timeout * 1000);
            }
        }
        echo $customize_sign_column_data;
    }
}

add_action('woocommerce_admin_order_data_after_order_details', 'misha_editable_order_meta_general');
function misha_editable_order_meta_general($order)
{  ?>
    <style>
        .customizesign_span_field {
            display: none;
        }
    </style>
    <br class="clear" />
    <h4>标签 <a href="#" class="edit_address">Edit</a></h4>
    <?php
    /*
	* 获取我们需要的所有元数据值
	*/
    $customizeSignArr = customizesign_get_config();
    ?>
    <div class="address">
        <hr>
        <p><?php
            // echo json_encode(get_post_meta($order->get_id(), 'customize_sign', true))
            ?></p>
        <p>
            <strong>当前标签:</strong>
            <?php
            $customizeSignI = 0;
            $checkBox = array();
            $nullBool = true;
            $argcs = array();
            foreach ($customizeSignArr as $val) {
                $customizeSignI++;
                $checked = get_post_meta($order->get_id(), 'customizeSign_' . $val['signKey'], true);
                if (empty($val['signKey'])) {
                    continue;
                }
                $argc = array(
                    'id' => 'customizeSign_' . $val['signKey'],
                    'description' => '<span class="layui-badge layui-bg-' . $val['signColor'] . '">' . $val['signName'] . '</span>',
                    'style' => 'width:16px',
                );
                if (empty($checked)) {
                    $argc['value'] = '';
                } else {
                    $argc['value'] = 'yes';
                    // $argc['cbvalue'] = 'yes';
                }
                $argcs[] = $argc;
                $checkBox['customizeSign_' . $val['signKey']] = array(
                    'signColor' => $val['signColor'],
                    'signName' => $val['signName'],
                    'checked' => $checked,
                    'changeSign' => 0
                );
            }
            foreach ($checkBox as $val) {
                if (!empty($val['checked'])) {
                    echo '&nbsp;<span class="layui-badge layui-bg-' . $val['signColor'] . '">' . $val['signName'] . '</span>&nbsp;';
                    if ($nullBool) {
                        $nullBool = false;
                    }
                }
            }
            if ($nullBool) {
                echo '&nbsp;<span class="layui-badge">尚未标记</span>&nbsp;';
            }
            ?>
        </p>
    </div>
    <div class="edit_address">
        <p>
            <hr><strong>初始标签:</strong>
            <?php
            foreach ($checkBox as $val) {
                if (!empty($val['checked'])) {
                    echo '<span class="layui-badge layui-bg-' . $val['signColor'] . '">' . $val['signName'] . '</span>&nbsp;';
                    if ($nullBool) {
                        $nullBool = false;
                    }
                }
            }
            if ($nullBool) {
                echo '<span class="layui-badge">尚未标记</span>';
            }
            ?>
        </p>
        <hr>

        <p>
        <div style="position: relative;">
            <strong>可用标签概览:</strong>
            <button type="button" style="height: 24px;line-height:24px;position:absolute;right:30px;" class="layui-btn" id="customizeSignUpdate">更新</button>
        </div>
        <?php
        foreach ($argcs as $argc) {
            @woocommerce_wp_checkbox($argc);
        }
        if ($customizeSignI == 0) {
            echo '<br><span class="layui-badge"> 没有可用的标签,请先前往 CustomizeSign 后台添加哦 </span>';
        }
        ?>
        </p>
    </div>
    <script type="text/javascript">
        var checkBox = <?php echo json_encode($checkBox); ?>;
        jQuery("#customizeSignUpdate").click(function() {
            // jQuery("input[name^='customizeSign_'][type='checkbox']:checked").each(function() {
            //     console.log(jQuery(this).val() + " 被选中");
            // });
            console.log(checkBox,'可用标签');
            if (checkBox == []) {
                return false;
            }
            jQuery("input[name^='customizeSign_']").each(function() {
                let name = jQuery(this).attr("name");
                if (jQuery(this).is(':checked')) {
                    if (checkBox[name]['checked'] == '') {
                        checkBox[name]['changeSign'] = 1;
                    }
                    checkBox[name]['checked'] = name.replace("customizeSign_", "")
                } else {
                    if (checkBox[name]['checked'] != '') {
                        checkBox[name]['changeSign'] = 1;
                    }
                    checkBox[name]['checked'] = '';
                }
                // jQuery(this).prop('checked');
            });
            console.log(checkBox);
            jQuery.post(
                ajaxurl, {
                    'action': 'customizesign_Ajax_save',
                    'checkBox': checkBox,
                    'order_id': <?php echo $order->get_id(); ?>
                },
                function(response) {
                    layer.msg('改动刷新后可见')
                    window.location.reload()
                }
            );
        });
    </script>

<?php }
// 更新配置
function customizesign_Ajax_save()
{
    if (!empty($_POST)) {

        $checkBox = $_POST['checkBox'];
        $order_id = $_POST['order_id'];
        $customize_sign_column_data = array();
        foreach ($checkBox as $key => $val) {
            if ($val['changeSign'] == 1) {
                update_post_meta($order_id, $key, wc_clean($val['checked']));
            }
            if (!empty($val['checked'])) {
                $customize_sign_column_data[] = array(
                    'signColor' => $val['signColor'],
                    'signName' => $val['signName']
                );
                // $customize_sign_column_data .= '<span class="layui-badge layui-bg-' . $val['signColor'] . '">' . $val['signName'] . '</span><br />';
                // 随机 25h ~ 55h 的过期时间，避免缓存雪崩
                $Timeout = mt_rand(100, 200);
                wp_cache_set($order_id, $customize_sign_column_data, 'customizesign_cache_column', $Timeout * 1000);
            }
        }
        update_post_meta($order_id, 'customize_sign', wc_clean($customize_sign_column_data));
    }
    wp_die();
}
add_action('wp_ajax_customizesign_Ajax_save', 'customizesign_Ajax_save');
add_action('wp_ajax_nopriv_customizesign_Ajax_save', 'customizesign_Ajax_save');
