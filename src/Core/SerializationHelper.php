<?php

namespace RPLib\Core;

class SerializationHelper {
    /**
     * @param $string
     * @return mixed
     */
    public static function unserialize($string) {
        $string2 = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            function($m) {
                $len = strlen($m[2]);
                $result = "s:$len:\"{$m[2]}\";";
                return $result;
            },
            $string);
        return unserialize($string2);
    }
}