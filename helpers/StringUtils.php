<?php
namespace Helpers;

class StringUtils
{
    public static function cleanUrl($url)
    {
        $url = str_replace(array("¿", "?", ",", ".", "/", ":", "@", "!", "¡", "=", "'", "\"", "#", "$", "%", "&", "(", ")", "'", "°", "|", "[", "]", "{", "}"), "", $url);
        $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        // Check if $url is already UTF-8, if not, decode it.
        if (mb_check_encoding($url, 'UTF-8')) {
            $url_decoded = $url;
        } else {
            $url_decoded = utf8_decode($url); // Assuming input might not be UTF-8
        }
        $url_tr = strtr($url_decoded, utf8_decode($originales), $modificadas);
        $clear = str_replace(array("¿", "", ",", ".", "/", ":", "@", "!", "¡", "=", "'", "\"", "#", "$", "%", "&", "(", ")", "'", "°", "|", "[", "]", "{", "}"), "", str_replace(" ", "-", $url_tr));
        $clear = strtolower($clear);
        return $clear;
    }

    public static function StringExplode($string, $delimiter, $indexId)
    {
        $delimiter = trim($delimiter);
        $dato = explode($delimiter, $string);
        return $dato[$indexId] ?? null; // Add null coalesce for safety
    }

    public static function cleanHtml($cadena)
    {
        // It's generally better to ensure consistent UTF-8 handling.
        // If $cadena is not UTF-8, it might need conversion.
        // html_entity_decode defaults to UTF-8 in modern PHP.
        if (!mb_check_encoding($cadena, 'UTF-8')) {
           $cadena = utf8_decode($cadena); // Assuming input might not be UTF-8
        }
        $cadena = html_entity_decode($cadena, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cadena = strip_tags($cadena);
        return $cadena;
    }
}
?>
