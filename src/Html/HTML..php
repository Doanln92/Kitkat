<?php

namespace Kitkat\Html;

use Kitkat\Helpers\Any;

class HTML extends HtmlObject{

    
    /**
     * render
     */
    public function render($Action = null)
    {
        return parent::render($Action);
    }
    
    public function __toString()
    {
        $a = clone $this;
        return $a->render();
    }
    
    public static function __callStatic($name, $arguments)
    {
        $simpleTag = ['link','img','input','meta','br','hr','base','param','source'];
        if(!is_array($arguments)) $arguments = [];
        $tag_properties = [
            'a' => ['href', 'innercontent', 'properties'],
            'img' => ['src', 'properties'],
            'link' => ['href', 'rel',  'properties'],
            'input' => ['type', 'name', 'value', 'properties'],
            'meta' => ['name', 'content', 'properties'],
        ];

        $n = strtolower($name);
        $tag = null;
        if(isset($tag_properties[$n])){
            $prt = [];
            $prop = [];
            $inc = null;
                
            foreach($arguments as $ind => $val){
                if(array_key_exists($ind,$tag_properties[$n])){
                    $na = $tag_properties[$n][$ind];
                    if($na == 'innercontent'){
                        $inc = $val;
                    }elseif($na == 'properties'){
                        $prop = $val;
                    }
                    else{
                        $prt[$na] = $val;
                    }
                }
            }
            
            $pp = new Any($prop);
            $pp->merge($prt);
            $tag = new static($n,$inc,$pp->get());
        }else{
            $pp = new Any($arguments);
            $tag = new static($n,$pp->get(0),$pp->get(1));
        }
        return $tag;

    
    }
}
