<?php
/**Session管理类
 * @author princehaku
 * @site http://3haku.net
 */
class SessionManager {

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key) {
        if (!isset($_SESSION[$key])) {
            return null;
        }
        return $_SESSION[$key];
    }

}