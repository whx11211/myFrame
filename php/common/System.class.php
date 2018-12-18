<?php

/**
 * 系统抽象类，暂时只支持windows
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

abstract class System
{
    public static function delFile($file)
    {
        @shell_exec('del "' . $file . '"');
        if (file_exists($file)) {
            file_put_contents(LOG_PATH . 'system_fail.log', 'del "' . $file . '"' . "\r\n", FILE_APPEND);
        }
        return true;
    }

    public static function moveFile($file, $new_file, $force=1)
    {
        if (!$force && file_exists($new_file)) {
            throw new Error('file is existed');
        }
        @shell_exec('move "' . $file . '" "' . $new_file . '"');
        if (!file_exists($new_file) || file_exists($file)) {
            file_put_contents(LOG_PATH . 'system_fail.log', 'move "' . $file . '" "' . $new_file . '"' . "\r\n", FILE_APPEND);
        }
        return true;
    }


}

?>