<?php

if (!function_exists('format_wa')) {
    function format_wa($phone)
    {
        if (empty($phone)) return '';
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($cleanPhone, 0, 1) === '0') {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (substr($cleanPhone, 0, 1) === '8') {
            $cleanPhone = '62' . $cleanPhone;
        }
        return $cleanPhone;
    }
}
