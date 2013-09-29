<?php

/**
 * 基本标签库
 *
 * @author princehaku
 *
 */
class F_View_BaseTags {

    /**
     * 把正则符进行转义
     * 比如.转义为\.
     *
     * @param type $source
     */
    private function _regxpConvert($source) {
        $source = str_replace("\\", "\\\\", $source);
        $source = str_replace("/", "\\/", $source);
        $source = str_replace("$", "\\$", $source);
        $source = str_replace("[", "\\[", $source);
        $source = str_replace("]", "\\]", $source);
        $source = str_replace("(", "\\(", $source);
        $source = str_replace(")", "\\)", $source);
        $source = str_replace(".", "\\.", $source);
        return $source;
    }
    /**
     * 把$a.cc<time('abc')转换成php的语法
     * 结果为$a["cc"]<time('abc')
     * 函数依然可以被调用
     */
    private function _tokenParser($token) {
        // 对$xxx标签进行资源符替换
        preg_match_all("/\\$([a-zA-Z\\d_\\.]*)/", $token, $matches_token, PREG_SET_ORDER);
        foreach ($matches_token as $arr) {
            $m_token = $arr[1];
            if (strpos($m_token, '.') !== false) {
                $cv_m_token = $this->_dotToArr($m_token);
                $token = preg_replace("/\\$$m_token/", $cv_m_token, $token);
            }
        }

        return $token;
    }
    /**
     * 把形如$xx.cc 转换成 xx["cc"]
     * @param $source_string
     * @return string
     */
    private function _dotToArr($source_string) {
        $strings = explode(".", $source_string);
        foreach ($strings as $i => $token) {
            if ($i == 0) {
                $converted_string = "\$$token";
                continue;
            }
            $converted_string .= "[\"$token\"]";
        }
        return $converted_string;
    }

    public function apply($source) {
        // LIST标签替换
        $this->parseList($source);
        // 替换全局{{$}}标签为单词
        $this->parseToken($source);
        // 替换if标签对
        $this->parseIf($source);

        return $source;
    }

    /**
     * 解析<if con="xxxx">替换if标签
     *
     * @param mixed $source
     */
    private function parseIf(&$source) {
        $matches = array();
        // 替换if标签
        preg_match_all("/\\<if\\s*?con=\"(.*?)\">/", $source, $matches, PREG_SET_ORDER);

        if ($matches == null) {
            $matches = array();
        }

        foreach ($matches as $i => $j) {
            $tagname = $j[1];
            $condition_val = $this->_tokenParser($tagname);
            $val = "<?php if ($condition_val) { ?>";
            $source = preg_replace("/\\<if\\s*?con=\"" . $this->_regxpConvert($tagname) . "\"\\>/", $val, $source, 1);
        }
        // 替换elseif标签
        preg_match_all("/\\<elseif\\s*?con=\"(.*?)\"\\/>/", $source, $matches, PREG_SET_ORDER);

        if ($matches == null) {
            $matches = array();
        }

        foreach ($matches as $i => $j) {
            $tagname = $j[1];
            $condition_val = $this->_tokenParser($tagname);
            $val = "<?php } else if ($condition_val) { ?>";
            $source = preg_replace("/\\<elseif\\s*?con=\"" . $this->_regxpConvert($tagname) . "\"\\/\\>/", $val, $source, 1);
        }

        // 替换<else/>
        $source = preg_replace("/<else\\/>/", "<?php } else { ?>", $source);
        // 替换</if>
        $source = preg_replace("/<\\/if>/", "<?php } ?>", $source);
        return true;
    }

    /**
     * 解析${$xxx}标签为实体
     *
     * @param mixed $source
     */
    private function parseToken(&$source) {
        $matches = array();
        // 替换{$xxx}标签为实体
        preg_match_all("/\\{\\{(.*?)\\}\\}/", $source, $matches, PREG_SET_ORDER);

        if ($matches == null) {
            return false;
        }

        foreach ($matches as $i => $j) {
            $tagname = $j[1];
            $converted_token = $this->_tokenParser($tagname);
            $val = "<?php echo $converted_token; ?>";
            $source = str_replace("{{" . $tagname . "}}", $val, $source);
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

        preg_match("/<list (.*?)>/", $source, $matches);

        if (null == $matches) {
            return false;
        }
        // 参数分离
        $parm = $matches[1];
        // list 的name
        preg_match("/.*?from=\"(.*?)\"/", $parm, $tag);
        if (empty($tag[1])) {
            throw new Exception("list tags Must have property from and as");
        }
        $tagname = $tag[1];
        // list 的key
        preg_match("/.*?key=\"(.*?)\"/", $parm, $tag);
        $tag_key = isset($tag[1]) ? $tag[1] : "";
        // list 的val
        preg_match("/.*?as=\"(.*?)\"/", $parm, $tag);
        if (empty($tag[1])) {
            throw new Exception("list tags Must have property from and as");
        }
        $tag_val = $tag[1];
        // 替换标签;
        $converted_token = $this->_tokenParser($tagname);
        $tag_val = $this->_tokenParser($tag_val);

        $parm = $this->_regxpConvert($parm);

        $keyname = "\$i$n";
        if (!empty($tag_key)) {
            $keyname = $tag_key;
        }

        $source = preg_replace("/<list $parm>/", "<?php foreach ($converted_token as $keyname=>$tag_val){ ?>", $source, 1);
        $source = preg_replace("/<\\/list>/", "<?php } ?>", $source, 1);

        while ($this->parseList($source, ++$n)) {
            return true;
        }
    }

}
