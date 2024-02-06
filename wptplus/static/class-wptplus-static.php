<?php
/**
 * 两个钩子队列管理特定的样式表和JavaScript
 *
 * @package 		wptplus
 * @subpackage 		wptplus/static
 */
class Wp_Wptplus_Plugin_CS_JS {
// class Wp_Tongtool_Plugin_Admin {

	/**
	 * 插件ID
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 		$plugin_name 		The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * 这个插件的版本
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 		$version 		插件当前版本
	 */
	private $version;

	/**
	 * 初始化类时设置其属性
	 *
	 * @since 		1.0.0
	 * @param 		string 		$plugin_name 		插件名称
	 * @param 		string 		$version 			插件版本
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * 注册后台样式表
	 *
	 * @since 		1.0.0
	 */
	public function enqueue_styles() {
		wp_register_style(
			'xinrong',
			plugin_dir_url( __FILE__ ).'/css/xinrong.css'
		);   
		wp_register_style(
			'xr-jquery-editable-style',
			plugin_dir_url( __FILE__ ) . '/css/jquery.edittable.css'
		);
		wp_register_style(
			'layui',
			plugin_dir_url( __FILE__ ).'plugins/layui/css/layui.css'
		);
		wp_enqueue_style( 'layui' );
		wp_enqueue_style('xr-jquery-editable-style');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-starter-plugin-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * 注册后台Javascript 基础
	 *
	 * @since 		1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-starter-plugin-admin.js', array( 'jquery' ), $this->version, false );


		wp_register_script(
			'layui',
			plugin_dir_url( __FILE__ ).'plugins/layui/layui.js'
		);
		wp_register_script(
			'xr-jquery-editable-js',
			plugin_dir_url( __FILE__ ). '/js/jquery.edittable.js',
			array( 'jquery' )
		);
		wp_register_script(
			'jquery',
			plugin_dir_url( __FILE__ ).'/js/jquery.js'
		);
		wp_register_script(
			'cookie',
			plugin_dir_url( __FILE__ ).'/js/jquery.cookie.min.js'
		); 
		wp_enqueue_script('jquery'); 
		wp_enqueue_script('cookie');
		wp_enqueue_script('xr-jquery-editable-js'); 
		wp_enqueue_script('layui');
	}
}
