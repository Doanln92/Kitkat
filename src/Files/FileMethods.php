<?php

namespace Kitkat\Files;

use Kitkat\Helpers\Any;

trait FileMethods{
    
    protected $_filename = null;

    protected $_content = null;

    protected $_filetype = null;

    protected $_extension = null;


    /**
     * chõn file để đọc hoặc ghi
     * 
     * @param string $filename
     * 
     * @return object $instance
     */
    public function file($filename)
    {
        $this->_filename = $filename;
        $this->_filetype = null;
        $this->_filedata = null;
        
        if(file_exists($path = $this->getPath($filename))){
            $this->_filetype = $this->getType($this->_filetype);
            if($info = $this->getMimeType($this->getType($filename))){
                $this->_extension = $info->extension;
            }
        }
    }


    /**
     * lấy loại file trên server
     * @param string $filename
     * 
     * @return string
     */
    public function getType($filename = null)
    {
        return file_exists($path = $this->getPath($filename))?mime_content_type($path):null;
    }

    /**
     * thiết lập định dangg5 file
     * @param string $type
     */
    public function setType($type)
    {
        if($info = $this->getMimeType($type)){
            $this->_extension = $info->extension;
            $this->_filetype = $type;
        }
        return $this;
    }

    /**
     * thiết lập hoặc lấy loại tệp tin
     * @param string $type
     * @return mixed
     */
    public function type($type = null)
    {
        return is_null($type)?$this->getType():$this->setType($type);
    }
    /**
     * lấy nội dung file
     * @param string $filename
     * 
     * @return string
     */
    public function getContent($filename = null)
    {
        if(file_exists($path = $this->getPath($filename))){
            return file_get_contents($path);
        }
        return null;
    }

    /**
     * cài đặt nội dung
     * @param mixed $content
     * @return object $instance
     */
    public function setContent($content = null)
    {
        $this->_content = $content;
        return $this;
    }



    /**
     * chèn nội dung hoặc lấy nội dung
     * @param mixed $content
     * 
     * @return mixed ($instance | $content)
     * 
     */
    public function content($content = null)
    {
        return is_null($content)?$this->getContent():$this->setContent($content);
    }

    /**
     * lưu file
     * @param string $filename
     * @param mixed $content
     * 
     * @return boolean
     */

    public function save($filename = null, $content = null)
    {
        $f = $this->parseFilename($filename);
        $c = is_null($content)?$this->_content:$content;
        file_get_contents($f, $c);
        if(file_exists($f)){
            if($info = $this->getMimeType($f)){
                $data = [
                    'filename' => $this->_filename,
                    'path' => $f,
                    'type' => $info->type,
                    'extension' => $info->extension
                ];
            }else{
                $data = [
                    'filename' => $this->_filename,
                    'path' => $f,
                    'type' => $this->_filetype,
                    'extension' => $this->_extension
                ];
            }
            return (new Any($data));
        }
        return false;
    }

    /**
     * chuẩn hóa file và đừng dẩn trước khi lưu
     */
    protected function parseFilename($filename = null)
    {
        $name = $filename?$filename:$this->_filename;
        if(!$name) return false;
        $filepath = $this->getPath($name);
        $pp = explode('/', $filepath);
        $fn = array_pop($pp);
        $this->setDir(implode('/', $pp), true);
        if($this->_filetype){
            if($info = $this->getMimeType($this->_filetype)){
                $ext = $info->extension;
                if(!preg_match('/\.'.$ext.'$/i')){
                    $fn .= '.'.$ext;
                }
            }
        }else{
            $fs = explode('.', $fn);
            if(($t = count($fs))>=2){
                if($fs[$t-1]){
                    if($info = $this->getMimeType($fs[$t-1])){
                        $this->_filetype = $info->type;
                        
                    }
                }
                $this->_extension = $fs[$t-1];
            }
        }
        $this->_filename = $fn;
        return $this->getPath($fn);
    }

    /**
     * xóa file
     * @param string $filename
     * 
     * @return boolean
     */
    public function delete($filename = null)
    {
        if(file_exists($f = $this->getPath($filename))){
            return unlink($f);
        }
        return false;
    }

}