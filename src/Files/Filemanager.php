<?php

namespace Kitkat\Files;

use Kitkat\Helpers\Any;

class Filemanager{
    use FileType, DirMethods, FileMethods;


    /**
     * khoi tao doi tuong
     * @param string $dir
     */
    function __construct($dir = null)
    {
        $this->dirInit();
        $this->setDir($dir);
    }

    

    /**
     * full path
     * lấy dừng dẫn tuyệt đối của file hoạc thư mục
     */
    public function gwtPath($filename = null)
    {
        return $this->_dir . '/' . ($filename?$filename:$this->_filename);
    }

    /**
     * neu url
     */
    protected function joinPath($main, $sub)
    {
        return rtrim($main, '/') . '/' . ltrim($sub, '/');
    }

    /**
     * lấy danh sách file và thư mục
     * 
     * @param 
     */

    public function getList($dir=null,$ext=null,$sort = false){
        if(!$dir) $dir = $this->_dir;
        $list = [];
        $abc = [];
        $result = [];
        $e = is_string($ext)?strtolower($ext):null;
        if($e){
            $e = explode(',',$e);
            $b = [];
            for($i = 0; $i < count($e); $i++){
                $ei = trim($e[$i]);
                if($ei){
                    $b[] = $ei;
                }
            }
            $e = $b;
        }
        if (is_string($dir) && is_dir($dir)) {
            try{
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        $t = 1;
                        if($e){
                            $fs = explode('.',$file);
                            $ex = strtolower($fs[count($fs)-1]);
                            if(in_array($ex,$e)){
                                $t=1;
                            }else{
                                $t = 0;
                            }
                            if($t && $file!='..' && $file!='.'){
                                $path = $this->joinPath($dir,$file);
                                $sd = strtolower($file);
                                $abc[] = $sd;
                                $list[$sd] = new Any([
                                    'type' => 'file',
                                    'filename' => $file,
                                    'path' => $path,
                                    'extension' => $ex
                                ]);
                            }
                        }else{
                            if($file!='..' && $file!='.'){
                                $path = $this->joinPath($dir,$file);
                                $fs = explode('.',$file);
                                $ex = strtolower($fs[count($fs)-1]);
                                $type = is_dir($path)?'folder':'file';
                                $sd = strtolower($file);
                                $abc[] = $sd;
                                $list[$sd] = new Any([
                                    'type' => $type,
                                    'filename' => $file,
                                    'extension' => $ex,
                                    'path' => $path
                                ]);
                            }
                            
                        }
                        
                    }
                    closedir($dh);
                }
            }catch(exception $e){
                // $this->errors[__METHOD__] = $e->getMessage();
            }
        }
        if($list && $abc){
            if($sort){
                sort($abc);
            }
            $t = count($abc);
            $type_list = [
                'folder' => [],
                'file' => []
            ];
            
            for($i = 0; $i < $t; $i++){
                $item = $list[$abc[$i]];
                $type_list[$item['type']][] = $item;
            }
            foreach($type_list as $list_type){
                foreach($list_type as $it){
                    $result[] = $it;
                }
            }
        }
        return $result;
    }


    /**
     * xóa tất cả
     */
    public function deleteAll($dirname=null){
        if(is_string($dirname)){
            if(is_file($dirname)) return unlink($dirname);
            elseif(is_dir($dirname)){
                if(count(explode(base_path(), $dirname))<2) return false;
                try{
                    if($list = $this->getList($dirname)){
                        foreach($list as $item){
                            $d = $item->path;
                            if(is_dir($d)) $this->deleteAll($d);
                            else unlink($d);
                        }
                    }
                    return rmdir($dirname);
                }
                catch(exception $e){
                    // $this->errors[__METHOD__] = $e->getMessage();
                }
                
            }else{
                $dirname = $this->joinPath($this->_dir,$dirname);
                if(is_file($dirname)) return unlink($dirname);
                elseif(is_dir($dirname)){
                    try{
                        if($list = $this->getList($dirname)){
                            foreach($list as $item){
                                $d = $item->path;
                                if(is_dir($d)) $this->deleteAll($d);
                                else unlink($d);
                            }
                        }
                        return rmdir($dirname);
                    }
                    catch(exception $e){
                        // $this->errors[__METHOD__] = $e->getMessage();
                    }
                    
                }else{
                    // $this->errors[__METHOD__] = get_text(517);
                }
            }
        }return false;
    }
    

}