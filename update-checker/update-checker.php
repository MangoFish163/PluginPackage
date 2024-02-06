<?php
/*
* 负责检查和更新当前插件
* Author: Mango Fish
*/

if (!class_exists('UpdateChecker')) {
    class UpdateChecker
    {
        public $plugin_slug;
        public $version;
        public $cache_key;
        public $interval;
        public $updateNum = 0;
        public $plugin_entrance;
        public $timeout;
        static $responseBody;
        private $github_api_url;
        private $headers;

        public function __construct($init = ['plugin_slug' => false, 'cache_key' => false, 'timeout' => 60])
        {
            // 如果不是管理员页面，什么也不做
            if (!is_admin()) return false;
            $bool = is_array($init);
            $this->plugin_slug = $bool ? $init['plugin_slug'] : $init;
            // 标识
            $this->cache_key = $bool ? $init['cache_key'] : $this->plugin_slug;
            $this->interval = $bool && isset($init['cache_key']) ? $init['cache_key'] : false;
            // 缓存时长
            $this->timeout = $bool ? $init['timeout'] : 60;
            // 获取当前插件版本号
            $this->version = UPDATECHECKER_DATA['Version'];
            // 插件入口文件
            $this->plugin_entrance = $this->plugin_slug;
            // 向 GitHub API 发送请求，检查是否有新版本 地址/token换成自己的就行
            if ($this->cache_key == false || get_transient($this->cache_key) === false) {

                $this->github_api_url = 'https://api.github.com/repos/MangoFish163/wptplus';
                $personal_access_token = json_decode(file_get_contents(WP_CONTENT_DIR . '/text-config.json'))->token;
                $Agents = array(
                    'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.71 Safari/537.1 LBBROWSER',
                    'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E)',
                    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11',
                    'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.133 Safari/534.16',
                    'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/534.57.2 (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
                    'Mozilla/5.0 (Windows NT 5.1; U; en; rv:1.8.1) Gecko/20061208 Firefox/2.0.0 Opera 9.50'
                );
                $this->headers = array(
                    'Authorization: Bearer ' . $personal_access_token,
                    'Accept: application/vnd.github+json',
                    'User-Agent: ' . $Agents[array_rand($Agents)],
                );

                add_filter('plugins_api', array($this, 'info'), 20, 3);
                add_filter('site_transient_update_plugins', array($this, 'check_for_plugin_update'));
                add_filter('upgrader_source_selection', array($this, 'custom_upgrade_directory'), 10, 3);
            }
        }

        public function check_for_plugin_update($transient)
        {
            if (empty($transient->checked) || !isset($transient->checked[$this->plugin_slug])) {
                return $transient;
            }
            if (self::$responseBody) {
                if (version_compare($transient->checked[$this->plugin_entrance], self::$responseBody->new_version, '<')) {
                    $transient->response[$this->plugin_slug] = self::$responseBody;
                };
                return $transient;
            }
            $response = wp_remote_get($this->github_api_url.'/releases/latest', array('headers' => $this->headers));
            $body = wp_remote_retrieve_body($response);
            if ($body) {
                $release_info = json_decode($body, true);
                $latest_version = $release_info['tag_name'];
                // 检查最新版本与当前版本的比较 设置更新信息
                $obj = new stdClass();
                $obj->slug = $this->plugin_slug;
                $obj->name = $release_info['name'];
                $obj->author = $release_info['author']['login'];
                $obj->new_version = $latest_version ?? 0;
                $obj->url = $release_info['html_url'];
                $obj->package = $release_info['zipball_url'];
                $obj->download_link = $release_info['zipball_url'];
                $obj->trunk = $release_info['zipball_url'];
                $obj->last_updated = $release_info['published_at'];
                self::$responseBody = $obj;
                if (version_compare($transient->checked[$this->plugin_entrance], $latest_version, '<')) {
                    $transient->response[$obj->slug] = $obj;
                }
            }

            return $transient;
        }
        public function custom_upgrade_directory($source, $remote_source, $upgrader)
        {
            if (strpos($source, 'MangoFish163') !== false) {
                $target_directory = WP_CONTENT_DIR . "/upgrade/" . UPDATECHECKER_DIR_NAME . "/";
                return $this->move_folder_contents($source, $target_directory) ? $target_directory : $source;
            }
            self::$responseBody = false;
            if ($this->interval) {
                set_transient($this->cache_key, $this->version, $this->timeout);
            }
            return $source;
        }
        // 避免写入官方默认分配的去重目录
        public function move_folder_contents($source, $destination)
        {
            if (!file_exists($source)) {
                return false;
            }
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $items = glob($source . '/*');
            foreach ($items as $item) {
                $new_item = $destination . '/' . basename($item);
                rename($item, $new_item);
            }
            return true;
        }
        function info($result, $action, $args)
        {
            // 如果你现在没有得到插件信息 或者 它不是我们的插件 或者 不是更新动作 那么什么都不做
            if ('plugin_information' !== $action || $this->plugin_slug !== $args->slug) {
                return $result;
            }
            // 得到更新数据
            $remote = self::$responseBody;
            if (!$remote) {
                return $result;
            }
            $result = new stdClass();
            $result->name = $remote->name;
            $result->slug = $remote->slug;
            $result->version = $remote->new_version;
            $result->author = $remote->author;
            $result->download_link = $remote->download_link;
            $result->trunk = $remote->trunk;
            $result->last_updated = $remote->last_updated;

            $modify_particulars_url = $this->github_api_url.'/contents/modify-particulars.json';
            $response = wp_remote_get($modify_particulars_url, array('headers' => $this->headers));
            $body = wp_remote_retrieve_body($response);
            if (wp_remote_retrieve_response_code($response) === 200) {
                $body = wp_remote_retrieve_body($response);
                $sectionsRes = base64_decode(json_decode($body,true)['content']);
                $sectionsRes = json_decode($sectionsRes);
                foreach ($sectionsRes as $key => $value) {
                    $result->sections[$key] = $value;
                }
            }else{
                $result->sections = array(
                    'description' => "<p>如果您确认版本发生了更迭。则可以忽视此处的描述直接更新。</p>
                    <p>此处也许git网络出现了问题,或者开发小哥忘记了更新此处内容。</p>
                    <span>如果您接收到了可用更新通知,则此处详情展示不会影响到更新</span>",
                );
            }
            $result->banners = array(
                'low' => WP_PLUGIN_URL.'/'.UPDATECHECKER_DIR_NAME.'/MX0001.jpeg',
                'high' => WP_PLUGIN_URL.'/'.UPDATECHECKER_DIR_NAME.'/MX0001.jpeg',
            );

            return $result;
        }
        public function purge($upgrader, $options)
        {
        }

        public function log(...$args) {
            $timestamp = date('Y-m-d H:i:s');
            $log_message = $timestamp . ' - ' . implode(' ', array_map('json_encode', $args)) . PHP_EOL;
            $log_file = UPDATECHECKER_DIR.'/logs/look.log';
            file_put_contents($log_file, $log_message, FILE_APPEND);
        }
    }
}