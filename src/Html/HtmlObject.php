<?php

namespace Kitkat\Html;

class HtmlObject{
    public $tagName = null;

    protected $innerContent = [];

    protected $contentType = 'html';

    protected $_data = [];

    protected $_attrs = [];

    protected $_before = [];

    protected $_after = [];

    protected $_children = [];

    protected $_parent = null; 

    private static $simpleTag = ['img','meta','link', 'input', 'br', 'hr'];

    /**
     * khoi tao doi tuong html
     * @param string $tagName thẻ html
     * @param mixed nội dung
     * @param array $properties thuộc tính của thẻ
     * 
     */
    public function __construct($tag=null, $content=null, $properties=null)
    {
        $this->tagName = $tag;
        $this->setContent($content);
        $this->attr($properties);
    }

    /**
     * thiet lap noi dung
     */
    protected function setContent($content = null)
    {
        if(!is_null($content)){
            if(is_array($content)){
                $this->innerContent = [];
                foreach ($content as $key => $cnt) {
                    if(is_a($cnt,self::class) || is_string($cnt) || is_numeric($cnt)){
                        $this->innerContent[] = $cnt;
                    }
                }
            }
            else{
                $this->innerContent = [$content];
            }
        }
    }
    public function setOption($name = null,$value = null)
    {
        if(is_string($name)){
            return $this->setOpt($name,$value);
        }elseif(is_array($name)){
            foreach($name as $key => $val){
                $this->setOpt($key,$val);
            }
        }
        return $this;
    }

    
    protected function setOpt($name,$value=null)
    {
        if (array_key_exists($name, $this->_data)) {
            $this->_data[$name] = $value;
        }else{
            return $this->attr($name,$value);
        }
    }


    public function render($action=null)
    {
        if(is_callable($action)){
            $action($this);
        }
        $html = $this->_renderBefore().$this->openTag().(
            (!$this->isSimpleTag())?$this->_renderContent():''
            ).$this->closeTag().$this->_renderAfter();
        return $html;
    }

    public function __toString()
    {
        $a = clone $this;
        return $a->render();
    }

    public function openTag($tag=null)
    {
        if(!$tag) $tag = $this->tagName;
        if($tag){
            $html = "<$tag";
            if(count($this->_attrs)){
                foreach($this->_attrs as $attr => $val){
                    if(strtolower($attr)=='classname'){
                        $attr = 'class';
                        $vv = explode(' ',$val);
                        $cl = array_unique($vv);
                        $val = implode(' ',$cl);
                        $val = trim($val);
                    }
                    if(is_string($val) || is_numeric($val) || $val){
                        if(is_array($val)){
                            $html = $this->arrayToAttr($html,$val,$attr);
                        }
                        elseif(strlen($val) || is_numeric($val)){
                            $html .= ' '.$attr.'="'.htmlentities($val).'"';
                        }elseif($val === true){
                            $html .= ' '.$attr;
                        }
                    }
                }
            }

            if($this->isSimpleTag()){
                $html .= ' /';
            }

            $html.=">";
            return $html;
        }
        return null;
    }


    public function closeTag($tag=null)
    {
        if(!$tag) $tag = $this->tagName;
        if($tag && !$this->isSimpleTag($tag)){
            return "</$tag>";;
        }
        return null;
    }

    public function isSimpleTag($tag = null)
    {
        if(!$tag) $tag = $this->tagName;
        if(!$tag) return null;
        return in_array($tag, self::$simpleTag);
    }
    
    protected function arrayToAttr($html = '', $arr = [], $k='data')
    {
        if(is_array($arr)){
            if($k!='style'){
                foreach($arr as $key => $value){
                    $kk = $k.'-'.$key;
                    if((is_string($value) && strlen($value)>0) || is_numeric($value) || $value==true){
                        $html.=' '.$kk .'="'.$value.'"';
                    }elseif(is_array($value)){
                        $html.=$this->arrayToAttr($html,$value,$kk);
                    }
                }
            }
            else{
                $st = '';
                foreach($arr as $key => $value){
                    if((is_string($value) && strlen($value)>0) || is_numeric($value) || $value==true){
                        $st.=' '.$key .': '.$value.'; ';
                    }
                }
                $st = trim($st);
                $html.=' '.$k .'="'.$st.'"';
            }
        }
        return $html;
    }
    
    public function attr($name=null,$value='<!---DEFAULT--->')
    {
        if(is_string($name)){
            if($name=='class') $name = 'className';
            if($value==='<!---DEFAULT--->'){
                if (array_key_exists($name, $this->_attrs)) {
                    return $this->_attrs[$name];
                }
                return null;
            }
            return $this->setAttr($name,$value);
        }
        elseif(is_array($name)){
            foreach($name as $k => $v){
                if(!is_numeric($k)){
                    $this->setAttr($k,$v);
                }
            }
        }
        return $this;
    }

    public function setAttr($name=null,$value='<!---DEFAULT--->')
    {
        if($value!=='<!---DEFAULT--->'){
            if(strtolower($name)=='class'){
                return $this->addClass($value,true);
            }
            if(is_array($value)){
                foreach ($value as $key => $val) {
                    $this->_attrs[$name.'-'.$key] = $val;
                }
                return $this;
            }
            
            $this->_attrs[$name] = $value;
        }
        
        return $this;
    }

    /**
     * thiết lập parent
     * @param HtmlObject
     * @return HtmlObject
     */
    public function setParent($parent)
    {
        if(is_a($parent, self::class)){
            $this->__parent = $parent;
        }
        return $this;
    }
    /**
     * parent
     * @return HtmlObject
     */
    
    public function parent()
    {
        if($this->__parent) return $this->__parent;
        return (new static());
    }

    /**
     * thêm class cho doi tuong
     * @param string $class
     * 
     */

    public function addClass($class=null,$new=false)
    {
        if(!is_null($class)){
            if (array_key_exists('className', $this->_attrs)) {
                if(!$new){
                    $cls = explode(' ', $this->_attrs['className']);
                    $cls[] = $class;

                    $this->_attrs['className'] = implode(' ',array_unique($cls));
                }else{
                    $this->_attrs['className'] = $class;
                }
            }else{
                $this->_attrs['className'] = $class;
            }
        }
        return $this;
    }

    public function removeClass($class=null)
    {
        
        if(!is_null($class)){
            if (array_key_exists('className', $this->_attrs)) {
                $cls = explode(' ', $this->_attrs['className']);
                if($cls){
                    $t = count($cls);
                    $cl = [];
                    for($i=0; $i<$t; $i++){
                        if($cls[$i] != $class){
                            $cl[] = $cls[$i];
                        }
                    }
                }
                $this->_attrs['className'] = implode(' ',array_unique($cl));
            }
        }
        else{
            if (array_key_exists('className', $this->_attrs)) {
                $this->_attrs['className'] = '';
            }
        
        }
        return $this;
    }
    

    public function data($name = null, $value = null)
    {
        $d = [];
        if(is_string($name) && !is_null($value)){
            if(!is_array($value)){
                $d[$name] = $value;
            }else{
                foreach($value as $k => $v){
                    $d[$name.'-'.$k] = $v;
                }
            }
        }elseif(is_array($name)){
            foreach($name as $k => $v){
                $d[$k] = $v;
            }
        }
        if($d){
            foreach ($d as $key => $val) {
                $this->_attrs['data-'.$key] = $val;
            }
            return $this;
        }
        $arr = [];
        if($this->_attrs){
            $a = strtolower($name);
            foreach ($this->_attrs as $key => $value) {
                if(preg_match('/^data\-/i', $key)){
                    if($name){
                        if(strtolower($key) == 'data-'.$a){
                            return $value;
                        }
                        $n = str_replace('-', '\-', $a);
                        $p = '/^data\-'.$n.'($|\-)/i';
                        if(preg_match($p, $key)){
                            $arr[substr($key, 5)] = $value;
                        }
                    }
                }
            }
        }
        return $arr;
    }


    public function before($content=null)
    {
        if(!is_null($content)){
            $bf = $this->_before;
            array_unshift($bf,$content);
            $this->_before = $bf;
        }
        
        return $this;
    }

    public function after($content=null)
    {
        if(!is_null($content)){
            $this->_after[] = $content;
        }
        return $this;
    }

    public function prepend($content=null)
    {
        if(!is_null($content)){
            if(is_a($content, self::class)){
                $content->setParent($this);
            }
            $bf = $this->innerContent;
            array_unshift($bf,$content);
            $this->innerContent = $bf;
        }
        return $this;
    }

    public function append($content='')
    {
        if(!is_null($content)){
            if(is_a($content, self::class)){
                $content->setParent($this);
            }
            $this->innerContent[] = $content;
        }
        return $this;
    }

    public function prependTo($htmlObject=null)
    {
        if(is_object($htmlObject) && is_a($htmlObject,self::class)){
            $instance = $this->copy();
            $htmlObject->prepend($instance);
        }
        return $htmlObject;
    }

    public function appendTo($htmlObject=null)
    {
        if(is_object($htmlObject) && is_a($htmlObject,self::class)){
            $instance = $this->copy();
            $htmlObject->append($this);
        }
        return $htmlObject;
    }

    public function removeBefore($content=null)
    {
        if($content){
            if (($key = array_search($content, $this->_before)) !== false) {
                unset($this->_before[$key]);
            }
        }
        else $this->_before = [];
        return $this;
    }

    public function removeAfter($content=null)
    {
        if($content){
            if (($key = array_search($content, $this->_after)) !== false) {
                unset($this->_after[$key]);
            }
        }
        else $this->_after = [];
        return $this;
    }

    public function html($content=null)
    {
        if(is_null($content)){
            return $this->_renderArr($this->innerContent);
        }
        $this->contentType = 'html';
        $this->innerContent = [$content];
        return $this;
    }
    public function text($content=null)
    {
        if(is_null($content)){
            $cont = $this->_renderArr($this->innerContent);
            return ($this->contentType == 'html')?htmlentities($cont):$cont;
        }
        $this->contentType = 'text';
        $this->innerContent = [htmlentities($content)];
        return $this;
        
    }
    
    public function _renderContent()
    {
        return $this->_renderArr($this->innerContent);
    }
    public function _renderBefore()
    {
        return $this->_renderArr($this->_before);
    }
    public function _renderAfter()
    {
        return $this->_renderArr($this->_after);
    }
    
    protected function _renderArr($arr=[]){
        $html = '';
        if($arr){
            foreach($arr as $elem){
                $html.=$elem.'';
            }
        }
        return $html;
    }
    
    public function copy()
    {
        return clone $this;
    }
    
    
    
    public function remove()
    {
        $this->innerContent = [];
        $this->_before = [];
        $this->_after = [];
        $this->tagName = null;
        $this->_attrs = [];
    }


    public function __set($name, $value)
    {
        $n = strtolower($name);
        if($n == 'innertext'){
            $this->contentType = 'text';
            $this->text($value);
        }elseif($n == 'innerhtml'){
            $this->contentType = 'html';
            $this->html($value);
        }if (array_key_exists($name, $this->_data)) {
            $this->_data[$name] = $value;
        }else{
            $this->_attrs[$name] = $value;
        }
        
    }
    public function __get($name)
    {
        $n = strtolower($name);
        if(in_array($n,['innerhtml', 'innertext'])){
            if($n=='innertext'){
                return $this->text();
            }
            return $this->html();
        }
        if(array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }
        elseif (array_key_exists($name, $this->_attrs)) {
            return $this->_attrs[$name];
        }
        return null;
    }

    public function __isset($name)
    {
        return (isset($this->_attrs[$name])||isset($this->_data[$name]));
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
        unset($this->_attrs[$name]);
    }
}