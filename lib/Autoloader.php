<?php

namespace cbrfservice;

/**
 * Autoloader class.
 */
class Autoloader
{
    /**
     * @param string path to project folder
     */
    protected static $_path = null;

    /**
     * Register new autoloader.
     *
     * @param string $path
     */
    public static function register($path = null)
    {
        self::$_path = $path ? $path : dirname(__FILE__);

        return spl_autoload_register(array('\\' . self::getNamespace() . '\\Autoloader', 'load'), true, true);
    }

    /**
     * Class loader method.
     */
    public static function load($class)
    {
        $prefix = self::getNamespace() . '\\';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = self::$_path . '/' . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }

    /**
     * Returns project's namespace.
     *
     * @return string
     */
    protected static function getNamespace()
    {
        return __NAMESPACE__;
    }
}

\cbrfservice\Autoloader::register();
