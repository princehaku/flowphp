<?php

/**
 * 模板框架视图解析
 * 用于输出模版
 * @author princehaku
 * @site http://3haku.net
 */

/**
 * Class F_View_SViewEngine
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
                throw new Exception("Create Cache Dir " . $cache_dir . " Failed");
            }
        }
        // 模板文件
        $tplfile = $tpl_dir . $viewname . '.php';

        $tpl_path = $tplfile;
        // 缓存文件处理
        $tpl_cachepath = $cache_dir . str_replace("/", "__", $viewname) . "__cache.php";


        // 搜索模板文件是否存在
        if (DEV_MODE && !is_file($tplfile)) {
            throw new Exception("Template File Not Found " . $tplfile);
        }
        // 如果没有在dev_mode且缓存文件最后修改时间比templatefile旧 直接包含缓存
        if (file_exists($tpl_cachepath) && (filemtime($tpl_cachepath) > filemtime($tpl_path))
            && !DEV_MODE
        ) {
            include($tpl_cachepath);
            return;
        }
        Flow::Log()->info("Cache View Expired");
        // 读取模板文件
        $c = file_get_contents($tpl_path);
        // 读取tag
        $tagfilter = new F_View_BaseTags();

        $c = $tagfilter->apply($c);

        // 存储编译后的到文件
        if (strlen($c) > 0) {
            if (file_put_contents($tpl_cachepath, $c)) {
                Flow::Log()->info("Cache File {$tpl_cachepath} Created ");
                include($tpl_cachepath);
            } else {
                throw new Exception("Cache File Create Failed " . $viewname);
            }
        }
    }

}