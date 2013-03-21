<?php

/**
 * 模板框架
 * 用于输出模版
 * @author princehaku
 * @site http://3haku.net
 */

class View {
    /** 资源列表
     *
     */
    private $resource;

    /** 模板路径
     *
     */
    private $tplpath;

    protected $taglibs = array(
        "Base"
    );

    /**
     * 登记资源到列表
     * @param string $key
     * @param string|array|class $res
     */

    public function assign($key, $res) {
        $this->resource[$key] = $res;
        return $res;
    }

    /** 得到应该输出的结果串
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

    /** 打印输出
     *
     * @param $viewname
     */

    public function display($viewname) {

        global $_res;

        $_res = $this->getRes();

        $cachedir = C("CACHE_DIR");

        $tpldir = C("VIEW_DIR");

        //检测缓存文件夹是否存在
        if (!file_exists($cachedir)) {
            Flowphp::Log()->w("缓存文件夹不存在 自动创建");
            if (!mkdir($cachedir)) {
                Flowphp::Log()->w("缓存文件夹" . $cachedir . "创建失败");
            }
        }
        //模板文件
        $tplfile = $tpldir . $viewname;

        $this->tplpath = $tplfile;

        //搜索模板文件是否存在
        if (file_exists($tplfile)) {
            Flowphp::Log()->i("模版文件载入完毕 " . $tplfile);
        } else {
            throw new FlowException("模版文件不存在  " . $tplfile);
            return;
        }
        //缓存文件处理
        $cachefile = $cachedir . str_replace("/", "__", $viewname) . "__cache.php";

        //如果缓存文件不比templatefile新  而且缓存文件存在 而且没有开启debug 直接包含缓存
        if (file_exists($cachefile) && (filemtime($cachefile) > filemtime($tplfile)) && !C("DEBUG")) {
            include_once ($cachefile);
            return;
        }
        Flowphp::Log()->i("缓存过期 重新编译");
        //读取模板文件
        $c = file_get_contents($tplfile);
        //读取tag
        foreach ($this->taglibs as $tagi => $tagj) {
            Flowphp::Log()->i("标签库载入完成");
            $tagname = $tagj . "Tags";
            import("core.view.tags.$tagname");
            $tagfilter = new $tagname();
            $c = $tagfilter->apply($c);
        }
        //转换成linux换行方式
        if (C("FORCE_UNINX_BR")) {
            $c = preg_replace("/\r\n/", "\n", $c);
        }

        if (!C("DEBUG")) {
            $c = preg_replace("/<!--(.*?)-->/", "", $c);
        }

        //存储编译后的到文件
        if (file_put_contents($cachefile, $c)) {
            Flowphp::Log()->i("缓存文件{$cachefile}创建完成");
            include_once ($cachefile);
        } else {
            throw new FlowException("缓存文件创建失败" . $viewname);
        }
    }

    protected function getRes() {
        return $this->resource;
    }

}