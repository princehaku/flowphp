<?php

/**图片类
 * 
 */
class Image {

    /**创建一个验证码
     * 
     * Enter description here ...
     * @param unknown_type $length
     * @param unknown_type $mode
     * @param unknown_type $width
     * @param unknown_type $height
     * @param unknown_type $verifyName default is verify
     */
    public static function buildVerifyImage($length = 4, $width = 48, $height = 22, $verifyName = 'verify') {
        $randval = substr(md5(time() . microtime()), 5, $length);
        
        header('Content-Type: image/gif');
        
        $_SESSION[$verifyName] = md5($randval);
        
        $width = ($length * 9 + 10) > $width ? $length * 9 + 10 : $width;
        
        $im = @imagecreate($width, $height);
        
        $r = Array(
            225, 255, 255, 223
        );
        $g = Array(
            225, 236, 237, 255
        );
        $b = Array(
            225, 236, 166, 125
        );
        $key = mt_rand(0, 3);
        
        $backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]); //背景色（随机）
        $borderColor = imagecolorallocate($im, 100, 100, 100); //边框色
        $pointColor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)); //点颜色
        

        @imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        //@imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
        $stringColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
        //干扰
        for($i = 0; $i < 5; $i++) {
            $fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $fontcolor);
        }
        for($i = 0; $i < 10; $i++) {
            $fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pointColor);
        }
        
        @imagestring($im, 5, 5, 3, $randval, $stringColor);
        
        imagegif($im);
    }

}