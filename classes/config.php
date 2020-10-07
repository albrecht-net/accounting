<?php
class config {
    /**
     * @var array $_config Contains array from config file after load.
     */
    private static $_config = null;

    /**
     * Load config.php file
     * 
     * @return null Retrun null if including of config.php was successful otherwise output a http error 500.
     */
    private static function load() {
        self::$_config = include_once ROOT_PATH . 'config.php';

        // If config.php cannot get included
        if (self::$_config === false) {
            http_response_code(500);
        }

        return;
    }

    /**
     * Get config value
     * 
     * @param string $name Must be a name of a config array key.
     * @return mixed|void Configuration value, or null if name not found.
     */
    public static function get($name = null) {
        if ($name != null) {
            if (self::$_config == null) {
                self::load();
            }

            return self::$_config[$name];
        }

        return;
    }
}