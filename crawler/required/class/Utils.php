<?php
/**
 * Author: Will Smelser
 * Date: 1/11/14
 * Time: 7:09 PM
 * Project: openProjects
 */

class Utils {
    public static function getBaseHost($url){
        $host = parse_url($url,PHP_URL_HOST);

        if(empty($host))
            return null;

        $parts = explode('.',$host);
        $tld = array_pop($parts);
        $name = array_pop($parts);
        return "$name.$tld";
    }

    public static function getHost($url,$curUrl){
        $host = parse_url($url,PHP_URL_HOST);
        if(empty($host))
            return parse_url($curUrl,PHP_URL_HOST);
        return $host;
    }

    public static function getSubDomain($host){
        $parts = explode('.',$host);
        array_pop($parts);
        array_pop($parts);
        return implode('.',$parts);
    }

    public static function stripProtocol($url){
        return preg_replace('/^(https?\:\/\/)/i','',$url);
    }

    public static function stripHost($url,$host){
        $url = self::stripProtocol($url);
        $host = str_replace('.','\.',$host);
        return preg_replace("@^(.*?$host)@i",'',$url);
    }

    public static function canonicalize($address){
        $address = explode('/', $address);
        $keys = array_keys($address, '..');

        foreach($keys AS $keypos => $key)
        {
            array_splice($address, $key - ($keypos * 2 + 1), 2);
        }

        $address = implode('/', $address);
        $address = str_replace('./', '', $address);
        return $address;
    }

    public static function normalizePath($url,$curUrl){
        $url = self::stripProtocol($url);
        $host = parse_url($curUrl,PHP_URL_HOST);
        $url = self::stripHost($url,$host);

        //url hast no protocol or host now
        $url = ltrim($url,'/');
        $url = Utils::stripAnchor($url);

        //handle ../
        if(preg_match('@^(\.?\.\/)@',$url)){

            $temp = self::stripProtocol($curUrl);
            $temp = self::stripHost($temp,$host);
            $temp = trim($temp,'/');

            $base = basename($temp);
            $temp = preg_replace("@($base)\$@i",'',$temp);
            $temp = rtrim($temp,'/');

            $url = self::canonicalize($temp.'/'.$url);

        }

        return rtrim('http://'.self::getHost($url,$curUrl).'/'.$url,'/');
    }

    public static function stripAnchor($url){
        return preg_replace('@\#.*@','',$url);
    }
} 