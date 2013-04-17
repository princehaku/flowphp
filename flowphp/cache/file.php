<?php
/**
 * Copyright 2013 princehaku
 *
 *  FileName   : file.php
 *  Created on : 13-3-24 , 上午12:43
 *  Author     : haku
 *  Blog       : http://3haku.net
 */

class F_Cache_File implements ArrayAccess {

    private $_cachedValues = array();
    private $_baseDir;

    public function F_Cache_File() {
        $this->_baseDir = $_SERVER['TMP'];
    }

    public function setBaseDir($base_dir) {
        $this->_baseDir = $base_dir;
    }

    public function put($key, $value) {
        $key = str_replace(".", "/", $key);
        $dir = dirname($this->_baseDir . "/" . $key . ".php");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, 1);
        }
        file_put_contents($this->_baseDir . "/" . $key . ".php", '<?php return ' . var_export($value, 1) . ';');
        $this->_cachedValues[$key] = $value;
    }

    public function get($key) {
        $key = str_replace(".", "/", $key);
        $value = null;
        if (isset($this->_cachedValues[$key])) {
            $value = $this->_cachedValues[$key];
        } else if (file_exists($this->_baseDir . "/" . $key . ".php")) {
            $value = include $this->_baseDir . "/" . $key . ".php";
            $this->_cachedValues[$key] = $value;
        }
        return $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset) {
        return $this->get($offset) === null;
    }
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value) {
        $this->put($offset, $value);
    }
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset) {
        $key = str_replace(".", "/", $offset);
        if (file_exists($this->_baseDir . "/" . $key . ".php")) {
            unlink($this->_baseDir . "/" . $key . ".php");
            unset($this->_cachedValues[$key]);
        }
    }

}