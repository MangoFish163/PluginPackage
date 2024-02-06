<?php

/**
 *   批量修改产品在/shop页的排列数序
 *
 * @since 			1.0.0
 */

function custom_filter_woocommerce_products_by_attribute() {
    global $typenow;
  
    // 确保当前页面是产品页面
    if ($typenow == 'product') {
  
        // 获取所有产品属性
        $attributes = wc_get_attribute_taxonomies();
  
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $attribute_taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
  
                // 获取属性选项
                $attribute_terms = get_terms($attribute_taxonomy);
  
                echo '<select name="' . $attribute_taxonomy . '" id="' . $attribute_taxonomy . '" class="postform">';
                echo '<option value="">' . sprintf(__('属性筛选 %s', 'woocommerce'), wc_attribute_label($attribute->attribute_name)) . '</option>';
  
                foreach ($attribute_terms as $term) {
                    echo '<option value="' . $term->slug . '" ' . selected(isset($_GET[$attribute_taxonomy]) && $_GET[$attribute_taxonomy] == $term->slug, true, false) . '>' . $term->name . '</option>';
                }
  
                echo '</select>';
            }
        }
    }
}
  
// 添加产品属性筛选到产品列表页
add_action('restrict_manage_posts', 'custom_filter_woocommerce_products_by_attribute');
  
  /**
  * 处理属性筛选的查询
  */
  function custom_filter_woocommerce_products_query($query) {
    global $typenow;
  
    // 确保当前页面是产品页面
    if ($typenow == 'product') {
  
        $attributes = wc_get_attribute_taxonomies();
  
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $attribute_taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
  
                // 如果有设置属性筛选
                if (isset($_GET[$attribute_taxonomy]) && !empty($_GET[$attribute_taxonomy])) {
                    $query->set('tax_query', array(
                        array(
                            'taxonomy' => $attribute_taxonomy,
                            'field' => 'slug',
                            'terms' => $_GET[$attribute_taxonomy],
                        ),
                    ));
                }
            }
        }
    }
  
    return $query;
}

// 处理属性筛选的查询
add_filter('parse_query', 'custom_filter_woocommerce_products_query');


/**
 * 在批量修改功能中添加'产品排序'字段
*/
function custom_bulk_edit_function()
{
    ?>
    <div class="inline-edit-group">
        <label for="batch-edit-field">产品排序:</label>
        <input style="width:50%" type="number" id="batch-edit-field" name="batch_edit_field" value="" />
    </div>
    <?php
}
add_action('woocommerce_product_bulk_edit_start', 'custom_bulk_edit_function');


/**
 * 在批量修改保存时，保存对'产品字段的'修改
*/
function save_batch_edit_field_value($product)
{
    if (isset($_REQUEST['batch_edit_field'])) {
        $batch_edit_value = sanitize_text_field($_REQUEST['batch_edit_field']);
        $product->set_menu_order($batch_edit_value); //set_menu_order修改产品菜单排序的方法
        $product->save();
    }
}
add_action('woocommerce_product_bulk_edit_save', 'save_batch_edit_field_value');


/**
 * 在后台产品列表中，添加'产品排序'列
*/
function  add_batch_edit_column($columns)
{
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'name') {
            // 在'username'列之后添加自定义列
            $new_columns['product_sort'] = '产品排序';
        }
    }
    return $new_columns;
}
add_filter('manage_edit-product_columns', 'add_batch_edit_column',10);


/**
 * 在后台产品列表中，添加'产品排序'值
*/
function display_batch_edit_column_porduct_sort($columns,$post_id)
{
echo '<style type="text/css">';
echo '.column-product_sort{ width: 100px; }'; // 设置宽度，可以根据需要调整
echo '</style>';
global $product;
if ($columns === 'product_sort') {
    $menu_order = $product->get_menu_order();
    echo $menu_order;
}
}
add_action('manage_product_posts_custom_column', 'display_batch_edit_column_porduct_sort', 10, 2);


/**
 * 在后台产品列表中，添加'产品属性'列
*/
function custom_product_columns( $columns )
{
    $new_columns = array();
    foreach ($columns as $key => $value) {
        // print_r($key);
        $new_columns[$key] = $value;
        if ($key === 'product_sort') {
            // 在'username'列之后添加自定义列
            $new_columns['product_attributes'] = '属性';
        }
    }
    return $new_columns;
}
add_filter('manage_edit-product_columns', 'custom_product_columns',20);

/**
 * 在后台产品列表中，添加'产品属性'值
*/
function custom_product_column_values( $column, $post_id )
{
    echo '<style type="text/css">';
    echo '.column-product_attributes{ width: 150px; }'; // 设置宽度，可以根据需要调整
    echo '</style>';
    if ( 'product_attributes' === $column ) {
        $product = wc_get_product( $post_id );
        
        // 获取产品属性
        $attributes = $product->get_attributes();
        
        // 输出属性值
        foreach ( $attributes as $attribute ) {
            $taxonomy = $attribute->get_name();
            $term_id = $attribute->get_options()[0];
            $term_name = get_term( $term_id )->name;
            
            echo '<strong>' . esc_html( $taxonomy ) . ':</strong> ';
            echo esc_html( $term_name );
            echo '<br>';
        }
    }
}
add_action('manage_product_posts_custom_column', 'custom_product_column_values', 10, 2);

?>