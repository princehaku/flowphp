<?php

/** ajax控制器类
 * 特性:
 * 不会生成错误日志
 * @author princehaku
 * @site http://3haku.net
 */

//载入模版引擎
import("core.view.View");

class FlowAjaxAction extends FlowAction {
    
    function dieWithJson($var){
        echo json_encode($var);
        die;
    }
}
