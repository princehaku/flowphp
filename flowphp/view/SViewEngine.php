<?php

/**
 * 模板框架视图解析
 * 用于输出模版
 * @author princehaku
 * @site http://3haku.net
 */

class F_View_SViewEngine {

    public function init() {

    }
    /**
     * 资源列表
     *
     */
    private $_resource;

    /**
     * 登记资源到列表
     * @param string $key
     * @param string|array|class $res
     */

    public function assign($key, $res) {
        $this->_resource[$key] = $res;
        return $res;
    }

    /**
     * 得到应该输出的结果串
     *
     * @param unknown_type $viewname
     * @return string
     */

    public function getHtml($viewname) {
        $oldcache = ob_get_contents();
        if (false === $oldcache) {

        }
        ob_clean();
        ob_start();
        $this->display($viewname);
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }

    /**
     * @param null $layout_name
     * @param null $view_data
     */
    public function displayLayout($layout_name = null, $view_data = null) {
        // 调用模板引擎
        $this->display("layouts/" . $layout_name, $view_data);
    }

    /**
     * @param null $view_name
     * @param null $view_data
     */
    public function displayView($view_name = null, $view_data = null) {
        if ($view_name == null) {
            $view_name = Flow::app()->url_router->getMethod();
        }
        $action = Flow::app()->url_router->getAction();
        $view_path = strtolower($action) . "/" . $view_name;
        // 调用模板引擎
        $this->display("views/" . $view_path, $view_data);
    }

    /**
     * 打印输出
     *
     * @param $viewname
     */

    public function display($viewname, $view_data = array()) {

        $_res = $this->_resource;

        if (!empty($_res)) {
            extract($_res);
        }

        if (!empty($view_data)) {
            extract($view_data);
        }

        $appcache_dir = isset(Flow::$cfg["appcache_dir"]) ? Flow::$cfg["appcache_dir"] : APP_PATH . "/appcache/";

        $cache_dir = $appcache_dir . '/templates/';

        $tpl_dir = APP_PATH . "/templates/";

        // 检测缓存文件夹是否存在
        if (!file_exists($cache_dir)) {
            if (!mkdir($cache_dir, 0777, 1)) {
                throw new Exception("缓存文件夹" . $cache_dir . "创建失败");
            }
        }
        // 模板文件
        $tplfile = $tpl_dir . $viewname . '.php';

        $tpl_path = $tplfile;
        // 缓存文件处理
        $tpl_cachepath = $cache_dir . str_replace("/", "__", $viewname) . "__cache.php";


        // 搜索模板文件是否存在
        if (DEV_MODE && !file_exists($tplfile)) {
            throw new Exception("模版文件不存在  " . $tplfile);
        }
        // 如果没有在dev_mode且缓存文件最后修改时间比templatefile旧 直接包含缓存
        if (file_exists($tpl_cachepath) && (filemtime($tpl_cachepath) > filemtime($tpl_path))
            && !DEV_MODE
        ) {
            include($tpl_cachepath);
            return;
        }
        Flow::Log()->info("缓存过期 重新编译");
        // 读取模板文件
        $c = file_get_contents($tpl_path);
        // 读取tag
        $tagfilter = new F_View_BaseTags();

        $c = $tagfilter->apply($c);

        // 存储编译后的到文件
        if (strlen($c) > 0) {
            if (file_put_contents($tpl_cachepath, $c)) {
                Flow::Log()->info("缓存文件{$tpl_cachepath}创建完成");
                include($tpl_cachepath);
            } else {
                throw new Exception("缓存文件创建失败" . $viewname);
            }
        }
    }

}