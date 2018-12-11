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
        return @shell_exec('del "' . $file . '"');
    }

    public static function renameFile($file, $new_file, $force=1)
    {
        if (!$force && file_exists($new_file)) {
            throw new Error('file is existed');
        }
        return @shell_exec('move "' . $file . '" "' . $new_file . '"');
    }


}

?>