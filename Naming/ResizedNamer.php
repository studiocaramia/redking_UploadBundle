<?php
/**
 * Util class to generate name based on resized conf
 */
namespace Redking\Bundle\UploadBundle\Naming;

class ResizedNamer
{
    /**
     * Retourne le nom du fichier suffixé avec le nom du resize
     * @param  [type] $filename [description]
     * @param  [type] $suffix   [description]
     * @return [type]           [description]
     */
    public static function getName($filename, $suffix)
    {
        if (is_null($filename)) {
            return null;
        }
        $filename_parts = pathinfo($filename);
        if (!isset($filename_parts['extension'])) {
            return null;
        }
        $resize_file = $filename_parts['filename'].'_'.$suffix.'.'.$filename_parts['extension'];
        return $resize_file;
    }

    /**
     * Retourne l'url avec le nom du fichier suffixé
     * @param  [type] $url    [description]
     * @param  [type] $suffix [description]
     * @return [type]         [description]
     */
    public static function getUrl($url, $suffix)
    {
        $url_parts = parse_url($url);
        
        if ($url_parts === false || !is_array($url_parts) || !isset($url_parts['scheme']) || !isset($url_parts['host'])) {
            static::getName($url, $suffix);
        }

        $path_parts = pathinfo($url_parts['path']);
        if (!isset($path_parts['extension'])) {
            return null;
        }

        return $url_parts['scheme'].'://'.$url_parts['host'].'/'.$path_parts['dirname'].'/'.static::getName($path_parts['basename'], $suffix);
    }
}
