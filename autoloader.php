<?php

spl_autoload_register(function ($class) {

    $root_namespace = 'App\\';
    $source_path = 'src/';
    $dirname = dirname(__FILE__);

    $relative_path = str_replace([$root_namespace, '\\'], [$source_path, '/'], $class) . '.php';
    $path = $dirname.'/'.$relative_path;

    if (! file_exists($path)) {
        throw new Exception("File {$path} not found!");
    }

    include $path;
});