<?php

spl_autoload_register(function (string $class): void {
    if (str_starts_with($class, 'App\\')) {
        $classPath = preg_replace('/^App/', '', $class);
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $classPath);
        $filePath = __DIR__ . $classPath . '.php';
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }
});