<?php

namespace Kitkat\Files;

use Kitkat\Helpers\Any;

class Filemanager{
    use FileType, DirMethods, FileMethods, ZipMethods;


    /**
     * khoi tao doi tuong
     * @param string $dir
     */
    function __construct($dir = null)
    {
        $this->dirInit();
        $this->zipInit();
        $this->setDir($dir);
    }

    

    /**
     * full path
     * lấy dừng dẫn tuyệt đối của file hoạc thư mục
     * @param string $filename
     * @return string
     */
    public function getPath($filename = null)
    {
        $path = null;
        if($filename){
            if(!$this->checkDirAccepted($filename)) $path = $this->_dir . '/' . $filename;
            else $path = $filename;
        }
        else $path = $this->_dir . '/' . $this->_filename;

        return $path;
    }

    

}