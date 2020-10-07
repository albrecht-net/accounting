<?php
class token {
    /**
     * @var string $_tokenName Token name for session store.
     */
    private static $_tokenName = 'token';

    /**
     * Generate random token and store it in the session.
     * 
     * @return string Random token
     */
    public static function generate() {
        return session::put(self::$_tokenName, bin2hex(random_bytes(32)));
    }

    /**
     * Compares token for equality and delete stored token in session if matched.
     * 
     * @param string $token Token to compare with the stored value in session.
     * @return bool Return true if entered token matches the one in the session otherwise false.
     */
    public static function check($token) {
        if (session::exists(self::$_tokenName) && hash_equals(session::get(self::$_tokenName), $token)) {
            session::delete(self::$_tokenName);
            return true;
        }

        return false;
    }
}