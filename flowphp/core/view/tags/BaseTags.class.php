<?php

/** 基本标签库
 * 
 * @author princehaku
 *
 */
class BaseTags {

    private $listTags = array();

    public function apply($source) {
        // %include标签替换
        $this->parseTplInclude($source);
        // LIST标签替换
        $this->parseList($source);
        // 替换{$_}标签为全局变量
        $this->parseGlobalToken($source);
        // 替换全局{$}标签为单词
        $this->parseToken($source);
        // 替换全局{_}标签为多语言词汇
        $this->parseLang($source);
        // 替换全局{#}标签为配置
        $this->parseConfig($source);
        // 替换全局(%)标签为函数
        $this->parseFunction($source);
        // 替换if标签对
        $this->parseIf($source);

        return $source;
    }

    /** 替换{$_xxx}标签为全局变量
     * 比如{$_RQUEST['a']}
     *
     * @param type $source
     * @return boolean 
     */
    private function parseGlobalToken(&$source) {
        $matches = array();

        preg_match_all("/\\{\\$\\_(.*?)\\}/", $source, $matches, PREG_SET_ORDER);

        if ($matches == null) {
            return false;
        }

        foreach ($matches as $i => $j) {
            $tagname = $j[1];
            $keyname = "\$_" . $tagname;
            // L()->i("实体".$keyname."解析成功");
            $val = "<?php echo " . $keyname . ";?>";
            $j[1] = regxp_convert($j[1]);
            $source = preg_replace("/\\{\\$\\_" . $j[1] . "\\}/", $val, $source);
        }
        return true;
    }

    /** 解析<if con="xxxx">替换if标签
     *
     * @param mixed $source
     */
    private function parseIf(&$source) {
        $matches = array();

        preg_match_all("/\<if\s*?con=\"([^\>]*)\"\>/", $source, $matches, PREG_SET_ORDER);

        if ($matches == null) {
            return false;
        }

        foreach ($matches as $i => $j) {
            $condition_tag = $j[1];
            $condition_val = $condition_tag;
            $condition_val = str_replace("==", " == ", $condition_val);
            $condition_val = str_replace(">=", " >= ", $condition_val);
            $condition_val = str_replace("<=", " <= ", $condition_val);
            $condition_val = str_replace(">", " > ", $condition_val);
            $condition_val = str_replace("<", " < ", $condition_val);
            $matches_token = array();
            // 对con控制内的$xxx标签进行资源符替换
            preg_match_all("/\\$([\S]*)(\s|$)/", $condition_val, $matches_token, PREG_SET_ORDER);

            if ($matches_token != null) {
                foreach ($matches_token as $i1 => $j1) {
                    $tagname = $j1[1];
                    // 带[号认为是数组,不进行变量更换
                    if (strpos("[", $tagname) !== false) {
                        continue;
                    }
                    $converted_token = $this->convertComma($tagname, true);
                    $tagname = regxp_convert($tagname);
                    $condition_val = preg_replace("/\\$" . $tagname . "/", $converted_token, $condition_val, 1);
                }
            }

            $val = "<?php if ($condition_val) { ?>";
            $condition_tag = regxp_convert($condition_tag);
            $source = preg_replace("/\<if\s*?con=[\'\"]" . $condition_tag . "[\'\"]\>/", $val, $source, 1);
            $source = preg_replace("/<else>/", "<?php } else { ?>", $source, 1);
            $source = preg_replace("/<\/if>/", "<?php } ?>", $source, 1);
        }

        return true;
    }

    /** 解析{#xxx}的内容 替换成config的内容
     *
     * @param mixed $source
     */
    private function parseConfig(&$source) {
        $matches = array();
        // 替换{#xxx} 为 config内容
        preg_match_all("/\\{#(.*?)\\}/", $source, $matches, PREG_SET_ORDER);

        if ($matches == null) {
            return false;
        }
        foreach ($matches as $i => $j) {
            $keyname = $j[1];
            if ($keyname == null) {
                L()->i("标签" . $keyname . "解析失败");
                continue;
            }
            $val = "<?php echo C(\"" . $keyname . "\"); ?>";
            $source = preg_replace("/\\{#" . $j[1] . "\\}/", $val, $source);
        }

        return true;
    }

    /** 解析{%include *}的内容 
     * 载入其他模板
     *
     * @param mixed $source
     */
    private function parseTplInclude(&$source) {
        $matches = array();
        // 替换{#xxx} 为 config内容
        preg_match_all("/\\{%include (.*?)\\}/", $source, $matches, PREG_SET_ORDER);

        if ($matches == null) {
            return false;
        }
        foreach ($matches as $i => $j) {
            $keyname = $j[1];
            if ($keyname == null) {
                L()->w("#include标签" . $keyname . "解析失败");
                continue;
            }
            $otplfilepath = C("VIEW_DIR") . $keyname;

            if (file_exists($otplfilepath)) {
                L()->i("Include模版文件载入完毕 " . C("VIEW_DIR") . $keyname);
            } else {
                L()->e("Include模版文件不存在  " . C("VIEW_DIR") . $keyname);
                throw new FlowException("Include模版文件不存在  " . C("VIEW_DIR") . $keyname);
                continue;
            }
            $val = file_get_contents($otplfilepath);
            $j[1] = regxp_convert($j[1]);
            $source = preg_replace("/\\{%include " . $j[1] . "\\}/", $val, $source);
        }

        return true;
    }

    /** 解析{%xxx argv1 argv2...}的内容 替换成function的内容
     *
     * @param mixed $source
     */
    private function parseFunction(&$source) {
        $matches = array();

        preg_match_all("/\\{%([\s\S]*?)\\}/", $source, $matches, PREG_SET_ORDER);

        if ($matches == null) {
            return false;
        }

        foreach ($matches as $i => $j) {
            if (empty($j[1])) {
                L()->w("标签" . $keyname . "解析失败");
                continue;
            }
            $func = explode(" ", $j[1]);

            $keyname = $func[0];
            unset($func[0]);
            $param = implode(",", $func);

            $val = "<?php echo $keyname($param); ?>";
            $j[1] = regxp_convert($j[1]);
            $source = preg_replace("/\\{%" . $j[1] . "\\}/", $val, $source);
        }

        return true;
    }

    private function convertComma($source_string, $forceRes = false) {

        $pos = strpos($source_string, ".");

        $converted_string = "";

        if ($pos === false || $pos <= 0) {
            $converted_string = "\$_res['" . $source_string . "']";
        } else {
            $strings = explode(".", $source_string);
            foreach ($strings as $i => $token) {
                // 强制变更list
                if ($i == 0 && in_array($token, $this->listTags)) {
                    $forceRes = false;
                }
                if ($i == 0 && $forceRes == false) {
                    $converted_string = "\$$token";
                    continue;
                }
                $converted_string .= "['$token']";
            }
            if ($forceRes) {
                $converted_string = "\$_res" . $converted_string;
            }
        }

        return $converted_string;
        ;
    }

    /** 解析{$xxx}标签为实体
     *
     * @param mixed $source
     */
    private function parseToken(&$source) {
        $matches = array();
        // 替换{$xxx}标签为实体
        preg_match_all("/\\{\\$(.*?)\\}/", $source, $matches, PREG_SET_ORDER);

        if ($matches == null) {
            return false;
        }

        foreach ($matches as $i => $j) {
            $tagname = $j[1];
            $converted_token = $this->convertComma($tagname, true);
            $val = "<?php echo $converted_token; ?>";
            $tagname = regxp_convert($tagname);
            $source = preg_replace("/\\{\\$" . $tagname . "\\}/", $val, $source);
        }

        return true;
    }

    /** 解析{_xxx}标签为多语言词汇
     *
     * @param mixed $source
     */
    private function parseLang(&$source) {
        $matches = array();

        preg_match_all("/\\{\\_(.*?)\\}/", $source, $matches, PREG_SET_ORDER);

        if ($matches == null) {
            return false;
        }

        foreach ($matches as $i => $j) {
            $val = "echo _e(\$$j)";
            $tagname = $j[1];
            // L()->i("实体".$tagname."解析成功");
            $val = "<?php echo _e(\"" . $tagname . "\"); ?>";
            $j[1] = regxp_convert($j[1]);
            $source = preg_replace("/\\{\\_" . $j[1] . "\\}/", $val, $source);
        }

        return true;
    }

    /**
     * 解析<list 列表标签
     *
     * @param mixed $source     html源
     * @param mixed $resource   数据源
     */
    private function parseList(&$source, $n = 0) {

        // 替换LIST标签
        $matches = array();

        preg_match("/<list ([\s|\S]*?)>/", $source, $matches);

        if (null == $matches) {
            return false;
        }
        // 参数分离
        $parm = $matches[1];
        preg_match("/.*?name=[\"\'\s](.*?)[\"\'\s].*?/", $parm, $tag);
        // list 的name
        $tagname = $tag[1];
        preg_match("/.*?id=[\"\'\s](.*?)[\"\'\s].*?/", $parm, $tag);
        // list 的id
        $tagid = $tag[1];
        array_push($this->listTags, $tagid);
        // 按照resource里面的东西替换标签;
        $converted_token = $this->convertComma($tagname);

        $parm = regxp_convert($parm);
        // 替换一层
        $source = preg_replace("/<list $parm>/", "<?php if(isset( $converted_token )) { foreach ( $converted_token as \$i$n=>\$$tagid){ ?>", $source, 1);

        $source = preg_replace("/<\/list>/", "<?php } } ?>", $source, 1);

        while ($this->parseList($source, ++$n)) {
            return true;
        }
    }

}
