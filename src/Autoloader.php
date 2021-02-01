<?php

namespace Marvin255\CbrfService;

/**
 * Autoloader's class.
 */
class Autoloader
{
    /**
     * Path to project folder.
     *
     * @var string|null
     */
    protected static $path = null;

    /**
     * Register autoloader.
     *
     * @param string $path Path to lib folder, get current if it's null
     *
     * @return bool
     */
    public static function register($path = null)
    {
        self::$path = $path ? $path : dirname(__FILE__);

        return spl_autoload_register([self::class, 'load'], true, true);
    }

    /**
     * Class loader method.
     *
     * @param string $class Class that's need to be loaded
     *
     * @return void
     */
    public static function load($class)
    {
        if (self::$path === null) {
            return;
        }

        $prefix = __NAMESPACE__ . '\\';
        $len = strlen($prefix);

        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = self::$path . '/' . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require $file;
            }
        }
    }
}

\Marvin255\CbrfService\Autoloader::register();
