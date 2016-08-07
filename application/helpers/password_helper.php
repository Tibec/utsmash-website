<?php

if ( ! function_exists('generate_password')) {

    function generate_password()
    {
        srand(time());

        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        for ($i = 0; $i < 8; ++$i) {
            $n = rand() % strlen($alphabet);
            $pass[$i] = $alphabet[$n];
        }
        return implode($pass);
    }
}