<?php

class Validator
{
    public static function clean($data)
    {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    public static function amount($amount)
    {
        return is_numeric($amount) && $amount > 0;
    }

    public static function date($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    public static function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
