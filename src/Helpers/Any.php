<?php

/**
 * @author DoanLN
 * @date 2018-12-25
 * @description 
 * cho phép tạo ra các đối tượng từ màng, 
 * truy cập cào các phần tử của mảng thông qua key bằng tên thuộc tính của đối tượng
 * 
 */

namespace Kitkat\Helpers;

class Any {
    /**
     * @var array $__data mảng dữ liệu
     */
    protected $__data = [];

    /**
     * @var mixed $__origin Dữ liệu gốc
     */
    protected $__origin = null;

    protected $__prefix = '';

    /**
     * @var boolean $__isAutoConvert trạng thái dữ liệu trả về có convert hay không
     */
    protected $__isAutoConvert = false;

    /**
     * hàm khởi tạo
     * @param array $data
     * @param string $prefix
     * @param boolean $autoConvert 
     */

    function __construct($data = null, $prefix = '', $autoConvert=false)
    {
        $this->__init($data, $prefix, $autoConvert);
    }


    /**
     * thiết lập data
     * 
     * @param array|object $data mảng dữ liệu
     * @param string $prefix
     * @param boolean      $autoConvert
     */
    public function __init($data = null, $prefix='', $autoConvert=false)
    {
        $this->__origin = $data;
        if(is_array($data)){
            $this->__data = [];
            foreach($data as $key => $value){
                $this->__data[$key] = $value;
            }
        }
        $this->setPrefix($prefix);
        $this->autoConvert($autoConvert);

    }

    /**
     * Kích hoạt tự động convert
     * @param boolean $active
     */
    public function autoConvert($active = true)
    {
        $this->__isAutoConvert = $active ? true : false;
    }

    /**
     * @param string $prefix
     * 
     * @return object
     */

    public function setPrefix(string $prefix = '')
    {
        $this->__prefix = $prefix;
        return $this;
    }
    
    public function getAll()
    {
        return $this->__data;
    }

    public function getOrigin()
    {
        return $this->__origin;
    }
    public function hasData()
    {
        return count($this->__data);
    }


    /**
     * gắn giá trị cho thuộc tính với name là tên thuộc tính
     * value là giá trị của thuộc tính
     * @param string $name
     * 
     * 
     */
	public function set($name, $value = null)
    {
        if(is_string($name) || is_numeric($name)) $this->__data[$this->__prefix.$name] = $value;
        return $this;
        
    }
    
    
    /**
     * lấy giá trị 
     * @param string $name
     * 
     * @param mixed  $value
     */
	public function get($name = null)
    {
        if(is_null($name)) return $this->__data;
        // nếu tồn tải key name trong mang data
        if (array_key_exists($this->__prefix.$name, $this->__data)) {
            $data = $this->__data[$this->__prefix.$name]; 
            // nếu giá trị tìm được là mãng hoặc object và autoConvert được kích hoạt thì sẽ tạo object Light 
            if((is_array($data) && is_object($data)) && $this->__isAutoConvert){
                $light = new static($data, $autoConvert);
                $light->setPrefix($this->__prefix);
                return $light;
            }
            return $data;
        }
        return null;
    }


    /**
     * gộp 2 mảng lại với nhau
     * @param array $array              Mảng cần gộp với mảng chính
     * @param boolean $is_main          Ưu tiên tuộc tính của mảng gộp hay mảng gốc. true là mảng gộp
     * @param boolean $origin_prefix    có sử dụng prefx của mảng chính hay không
     * 
     * @return object                   Trả về dối tượng đang thao tác
     */
    public function merge(array $array = [], $is_main = false, $origin_prefix=false)
    {
        if($origin_prefix){
            if($is_main){
                foreach ($array as $key => $value) {
                    if(!isset($this->{$key})){
                        $this->{$key} = $value;
                    }
                }
            }else{
                foreach ($array as $key => $value) {
                    $this->{$key} = $value;
                }
            }
        }else{
            if($is_main){
                $this->__data = array_merge($this->__data, $array);
            }else{
                $this->__data = array_merge($array, $this->__data);
            }
        }
        return $this;
    }

    /**
     * gắn giá trị cho thuộc tính với name là tên thuộc tính
     * value là giá trị của thuộc tính
     * @param string $name
     * 
     * 
     */
	public function __set($name, $value)
    {
        $this->set($name,$value);
        
    }
    
    
    /**
     * lấy giá trị 
     * @param string $name
     * 
     * @param mixed  $value
     */
	public function __get($name)
    {
        return $this->get($name);
    }
    /**
     * kiểm tra isset vd isset($light->prop)
     * @param string $name
     */
	
    public function __isset($name)
    {
        return isset($this->__data[$this->__prefix.$name]);
    }

    /**
     * loại bỏ thuộc tính qua tên thuộc tính
     */
    public function __unset($name)
    {
        unset($this->__data[$this->__prefix.$name]);
	}
    
    
    /**
     * truy cập thuộc tính bằng cách gọi hàm với tham số mặc định
     * @param string $method
     * @param array $params
     */
	public function __call($method, $params)
	{
		$t = (is_array($params) && isset($params[0])) ? $params[0] : null;
		if (array_key_exists($this->__prefix.$method, $this->__data)) {
			if(!$this->__data[$this->__prefix.$method] && !is_null($t)){
				return $t;
			}
			return $this->__data[$this->__prefix.$method];
        }
        return $t;
    }
    
    public function __toString()
    {
        return json_encode($this->__data);
    }
}