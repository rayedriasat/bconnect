<?php

function getFlashMessage($type)
{
    if (isset($_SESSION[$type . '_message'])) {
        $message = $_SESSION[$type . '_message'];
        unset($_SESSION[$type . '_message']);
        return $message;
    }
    return '';
}
