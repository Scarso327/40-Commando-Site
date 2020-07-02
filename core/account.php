<?php

class Account {

    public static $steamid = null;
    public static $member = null;

    // Create the new "Account Session"
    public function __construct($steamid) {
        self::$steamid = $steamid;
        self::$member = Member::getMember($steamid);
    }

    public function isLoggedIn() {
        if (self::$steamid != null) { return true; } // If the steamid is not null, we're logged in...
        return false;
    }

    public function logout($redirect = true) {
        // Session Handling...
        Session::start();
        Session::close();

        // Cookie Clearing...
        unset($_COOKIE['steam_id']);
        unset($_COOKIE['remember_token']);
        setcookie("steam_id", null, -1, "/");
        setcookie("remember_token", null, -1, "/");

        if ($redirect) {
            header("Location: ".URL);
        }
    }
}