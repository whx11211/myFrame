<?php 
include_once 'common/init.php';

$post = Instance::getMovie('post');

print_r($post->select(array('title'))->getAll());