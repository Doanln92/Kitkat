<?php

/**
 * @author DoanLN
 * @date 2018-12-26
 * @description 
 * cho phép tạo ra các đối tượng từ màng, 
 * truy cập cào các phần tử của mảng thông qua key bằng tên thuộc tính của đối tượng
 * 
 */

namespace Kitkat\Helpers;

class Arr extends Any {
    // code here

    /**
     * chuyển toản bộ object thành array
     * @param object $object
     */
    public static function parse($object)
    {
        $d = $object;
        if (is_object($d)) {
            $d = get_object_vars($d);
        }
    
        if (is_array($d)) {
            return array_map(__METHOD__, $d);
        }
        else {
            return $d;
        }

    }
}