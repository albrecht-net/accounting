<?php
class session {
    /**
     * Start new or start existing session and set options for security
     * 
     * @see https://www.php.net/manual/en/session.configuration.php
     * @return bool Returns ture if session was successfully started, otherwise false.
     */
    public static function start() {

        // session.sid_length
        if (empty($sid_length = config::get('session.sid_length'))) {
            if (($sid_length = ini_get('session.sid_length')) === false) {
                throw new Exception('Unable to get session.sid_length');
            }
        }

        // session.sid_bits_per_character
        if (empty($sid_bits_per_character = config::get('session.sid_bits_per_character'))) {
            if (($sid_bits_per_character = ini_get('session.sid_bits_per_character')) === false) {
                throw new Exception('Unable to get session.sid_bits_per_character');
            }
        }

        // session.cookie_secure
        if (empty($cookie_secure = config::get('session.cookie_secure'))) {
            if (($cookie_secure = ini_get('session.cookie_secure')) === false) {
                throw new Exception('Unable to get session.cookie_secure');
            }
        }

        // session.name
        if (empty($name = config::get('session.name'))) {
            if (($name = ini_get('session.name')) === false) {
                throw new Exception('Unable to get session.name');
            }
        }

        // Start session with options
        return session_start([
            'sid_length' => $sid_length,
            'sid_bits_per_character' => $sid_bits_per_character,
            'cookie_secure' => $cookie_secure,
            'name' => $name,
            'cookie_httponly' => true,
            'use_strict_mode' => true,
            'read_and_close' => true
        ]);
    }

    /**
     * Put session value
     * 
     * @param integer|string $name The name for the session array key.
     * @param mixed $value The value.
     * @return mixed Return the new value.
     */
    public static function put($name, $value) {
        if (session_status() != PHP_SESSION_ACTIVE) {
            self::start();
        }

        $_SESSION[$name] = $value;

        session_write_close();
        return $_SESSION[$name];
    }

    /**
     * Get session value
     * 
     * @param integer|string $name Must be a name of a session array key.
     * @param bool $delete If delete is true, the selected session key and value will get returned and deleted.
     * @return mixed|void|array Session value or null if name not found. If $name = null then the whole session array will get returned.
     */
    public static function get($name = null, $delete = false) {
        if (session_status() != PHP_SESSION_ACTIVE) {
            self::start();
        }

        if ($name != null) {

            // Return value when no deletion is made
            if (self::exists($name) && !$delete) {
                return $_SESSION[$name];

            // Return value with delete
            } elseif (self::exists($name) && $delete) {
                $var = $_SESSION[$name];
                self::delete($name);
                return $var;
            } else {
                return;
            }
        } else {
            return $_SESSION;
        }
    }

    /**
     * Check if session key exists
     * 
     * @param integer|string $name Must be a name of a session array key.
     * @return bool True if key exists otherwise false.
     */
    public static function exists($name) {
        if (session_status() != PHP_SESSION_ACTIVE) {
            self::start();
        }

        return isset($_SESSION[$name]);
    }

    /**
     * Delete session key and value
     * 
     * @param integer|string $name Must be a name of a session array key.
     * @return void
     */
    public static function delete($name) {
        if (session_status() != PHP_SESSION_ACTIVE) {
            self::start();
        }

        unset($_SESSION[$name]);

        session_write_close();
        return;
    }
}