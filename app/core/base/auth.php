<?php namespace core\base;

final class Auth
{
    public static function make($user) {
        $_SESSION['app_authenticated_user'] = Encryption::encode(serialize($user));
    }

    public static function user() {
        if (Session::get('app_authenticated_user')) {
            return unserialize(Encryption::decode(Session::get('app_authenticated_user')));
        }
        return null;
    }
}