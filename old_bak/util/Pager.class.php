<?php

/** 分页类
 * 
 * @author princehaku
 *
 */
class Pager {

    private $urlpagepre;

    private $parameter;

    private $totalpage;

    private $totalrows;

    private $perpage;

    private $nowpage;
    /**构造函数
     *
     * @param int $nowpage 当前页码
     * @param type $totalrows 总行数
     * @param type $perpage 每页多少条
     * @param type $parameter url上的其余参数
     * @param type $urlpagepre  翻页使用的参数名
     */
    public function __construct($nowpage = 1, $totalrows, $perpage, $parameter = '', $urlpagepre = 'p') {
        if ($nowpage < 1)
            $nowpage = 1;
        $this->nowpage = $nowpage;
        $this->totalrows = $totalrows;
        $this->perpage = $perpage;
        $this->totalpage = (int)($totalrows / $perpage + 1);
        if (($this->totalpage > 1) && ($totalrows % $perpage == 0)) {
            $this->totalpage = (int)($totalrows / $perpage);
        }
        $this->urlpagepre = $urlpagepre;
        $this->parameter = $parameter;
    }

    public function getLimitString() {
        $st = ($this->nowpage - 1) * $this->perpage;
        return " limit $st," . $this->perpage;
    }
    /**得到pager的div
     *
     * @return string 
     */
    public function getPage() {
        if ($this->totalrows == 0) {
            return "";
        }
        $content = "<div class='pager'>";
        $pageposa = $this->nowpage - 3;
        $pageposb = $this->nowpage + 3;
        if ($pageposa < 1)
            $pageposa = 1;
        if ($pageposb > $this->totalpage)
            $pageposb = $this->totalpage;
        $content .= $this->buildLink($this->urlpagepre, "s");
        for($i = $pageposa; $i <= $pageposb; $i++) {
            $content .= $this->buildLink($this->urlpagepre, $i);
        }
        $content .= $this->buildLink($this->urlpagepre, "e");
        $content .= "</div>";
        return $content;
    }
    /**内部函数 构造单条link
     *
     * @param type $page
     * @param type $mark
     * @return string 
     */
    private function buildLink($page, $mark) {
        
        if ($mark == 's') {
            $bar = "<a href='?$page=1&$this->parameter'>首页</a>";
        } else if ($mark == 'e') {
            $bar = "<a href='?$page=" . $this->totalpage . "&$this->parameter'>末页</a>";
        } else {
            $bar = "<a href='?$page=$mark&$this->parameter'>$mark</a>";
        }
        $class = "";
        if ($mark == $this->nowpage) {
            $class = "selected";
        }
        $bar = "<span class='$class'>" . $bar . "</span>";
        return $bar;
    }

}

?>