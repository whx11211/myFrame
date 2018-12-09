<?php 

// 数据库执行语句是否记录
define('DB_DEBUG', 1);

//错误DEGUE，=1表示直接显示错误，=0表示使用自定义错误处理
define('ERROR_DEBUG', 0);

//报错级别设置
define('ERROR_LEVEL', E_ALL);

include_once 'common/init.php';

$class = RemoteInfo::get('c');
$method = RemoteInfo::get('m');

//访问验证
Access::check($class, $method);


if ($class && class_exists($class.'Control')) {
    $class = $class.'Control';
    if (method_exists($class, $method)) {
        $control = new $class();
        $control->$method();
    }
    else {
        Output::fail(ErrorCode::METHOD_NOT_EXISTS);
    }
    
}
else {
    Output::fail(ErrorCode::CLASS_NOT_EXISTS);
}

