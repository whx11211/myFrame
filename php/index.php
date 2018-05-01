<?php 

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

