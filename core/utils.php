<?php

/**
 * _import
 * @param $path
 * @param $segment
 */
function _import($path, $segment = null)
{
    $imports = [];
    $files = glob($path . '/*');
    
    foreach ($files as $file) {
        $export = null;
        $name = ($segment ? "{$segment}/" : '') . basename($file, '.php');
        if (is_file($file)) {
            ob_start();
            include $file;
            if (is_callable($export)) {
                $imports[$name] = $export;
            }
            ob_get_clean();
        }

        if (is_dir($file)) {
            $dir = _import($file, $name);
            if (sizeof($dir)) {
                $imports[$name] = $dir;
            }
        }

    }
    return $imports;
} 
