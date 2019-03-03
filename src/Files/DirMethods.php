<?php
namespace Kitkat\Files;
trait DirMethods{
    protected $_dir = null;

    public function dirInit()
    {
        $this->_dir = base_path();
    }
    /**
     * thiết lập dường dẫn để quản lý file
     * @param string $dir
     * @param boolean $make_dir_if_not_exists
     * 
     * @return object instance
     */
    public function setDir($dir = null, $make_dir_if_not_exists = false)
    {
        if($dir && is_string($dir)){
            $dir = rtrim($dir,'/');
            // nếu không bắt dầu từ thư mục gốc
            if(!$this->checkDirAccepted($dir)) $dir = public_path($dir);
            if(is_dir($dir)){
                // $this->_dir = $dir;
            }elseif($make_dir_if_not_exists){
                // nếu thư mục không tồn tại và có yêu cầu tạo thư mục
                $this->makeDir($dir, 777, true);
                // $this->_dir = $dir;
            }
            $this->_dir = $dir;
        }
        return $this;
    }

    /**
     * tạo dường dẫn mới
     * @param string $dir
     * @param int $mode 
     * @param boolean $recursive
     * @param boolean $check_accepted
     * 
     * @return boolean
     */
    public function makeDir(string $dir, $mode = 777, $recursive = false, $check_accepted = true)
    {
        if($dir && is_string($dir)){
            // nếu không bắt dầu từ thư mục gốc
            if(substr($dir, 0, 1) != '/') $dir = public_path($dir);
            if($check_accepted && !$this->checkDirAccepted($dir)) return false;
            if(!is_dir($dir)){
                return @mkdir($dir, $mode, $recursive);
            }
            return true;
        }
        return false;
    }

    /**
     * kiểm tra xem dường dẫn có dc cho phép hay ko
     * @param string $dir
     * 
     * @return boolean
     */
    public function checkDirAccepted(string $dir)
    {
        if(count($p = explode(base_path(), $dir)) == 2) return true;
        return false;
    }

    /**
     * chuyển dường dẫn hiện tại
     * 
     * @param string $dir
     * @param boolean $make_dir_if_not_exists
     * 
     * @return object
     */
    public function cd($dir = null, $make_dir_if_not_exists = false)
    {
        if($this->checkDirAccepted($dir)) return $this->setDir($dir);
        $fullDir = $this->_dir.'/'.trim($dir, '/');
        if(!is_dir($fullDir) && $make_dir_if_not_exists){
            $this->makeDir($fullDir, 777, false, false);
        }
        return $this;
    }

}