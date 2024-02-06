<?php

/**
 * 注册插件的所有动作和过滤器
 *
 * 维护所有已注册的钩子列表
 * 调用run函数执行动作和过滤器列表来管理插件。
 *
 * @package 		wptplus
 * @subpackage 		wptplus/includes
 */

class Wp_Wptplus_Plugin
{
	/**
	 * 该属性指定插件ID
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 		$plugin_name 	插件的ID
	 */
	private $plugin_name;

	/**
	 * 该属性指定插件的版本
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 		$version 		插件当前版本
	 */
	private $version;

	/**
	 * 该属性负责维护和注册所有运行的钩子的加载器
	 *
	 * @since 		1.0.0
	 * @access 		protected
	 * @var 		 				$loader		类
	 */
	protected $loader;


	public function __construct()
	{

		// 将其更改为插件的名称(这将是wp记录的插件名称)
		// 这必须与传入的更新程序相同，否则会导致无法更新
		$this->plugin_name = 'wptplus';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->define_admin_hooks();
	} // __construct()

	// 自动加载类
	private function load_dependencies()
	{

		/**
		 * 如果你不希望自己的文件显得分成杂乱无序，就将可用方法写入该文件维护。该文件负责导入您为该插件编写的所有可用方法
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/function.php';

		/**
		 * 类中负责编排动作和过滤器的类
		 */
		// -----------------------------------！！！important ！！！-----------------------------------
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-' . WPTPLUS_PLUGIN_NAME . '-plugin-loader.php';
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/function/config.php';

		/**
		 * 负责定义在管理页面中使用的所有操作的类。
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'static/class-' . WPTPLUS_PLUGIN_NAME . '-static.php';


		// -----------------------------------！！！extension ！！！-----------------------------------
		/**
		 * 为了方便维护，后面的页面构建请基于该文件来添加和维护 =》背景页面构建器类
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class/Wptplus_Menus.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class/Wptplus_MenusPage.php';
		function wptplus_enqueue_layui_script()
		{
			// 引入指定的CSS/js资源 / 已存在则跳过
			if (!wp_script_is('layui-style', 'enqueued')) {
				wp_enqueue_style('layui-style', plugins_url('wptplus') . '/static/plugins/layui/css/layui.css', array(), null, false);
			}
			if (!wp_script_is('layui-style', 'enqueued')) {
				wp_enqueue_script('layui-script', plugins_url('wptplus') . '/static/plugins/layui/layui.js', array('jquery'), null, false);
			}
		}
		add_action('wp_enqueue_scripts', 'wptplus_enqueue_layui_script', 0);
		// ... 如果你有其他 import 要求写后面...

		// 订单信息推送到飞书群
		if (1 == get_transient('tofeishu_open')) {
			require_once  plugin_dir_path(dirname(__FILE__)) . 'includes/class/' . 'Wptplus_tofeishu.php';
			WPTPLUS_Tofeishu::get_instance();
		}

		// 自定义订单标签
		if (1 == get_transient('customizesign_open')) {
			require_once  plugin_dir_path(dirname(__FILE__)) . 'includes/function/' . WPTPLUS_PLUGIN_NAME . '-customizesign.php';
		}
		
		// 自定义多语言切换 弹出层
		if (1 == get_transient('gtranslatetab_open')) {
			require_once  plugin_dir_path(dirname(__FILE__)) . 'includes/function/' . WPTPLUS_PLUGIN_NAME . '-gtranslatetab.php';
		}

		// 产品列表页添加产品属性筛选
		if (1 == get_transient('BatchAlterProductSort_open')) {
			require_once  plugin_dir_path(dirname(__FILE__)) . 'includes/function/' . WPTPLUS_PLUGIN_NAME . '-batchalterproductsort.php';
		}

		$this->loader = new Wp_Wptplus_Plugin_Loader();
	} // load_dependencies()

	/**
	 * 注册所有与后台管理页面功能相关的钩子
	 *
	 * @since 		1.0.0
	 * @access 		private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Wp_Wptplus_Plugin_CS_JS($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
	} // define_admin_hooks()


	// 对外提供定义的插件名称
	public function get_plugin_name()
	{

		return $this->plugin_name;
	} // get_plugin_name()

	// 对外提供所定义钩子的类的引用。
	public function get_loader()
	{

		return $this->loader;
	} // get_loader()

	// 对外提供定义的插件版本
	public function get_version()
	{

		return $this->version;
	} // get_version()

	/**
	 * 运行加载器来执行向WordPress添加所有的钩子。
	 *
	 * @since 		1.0.0
	 */
	public function run()
	{

		$this->loader->run();
	} // run()
}
