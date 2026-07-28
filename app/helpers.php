<?php

if (!function_exists('formatSisaWaktu')) {
    function formatSisaWaktu(int $seconds): string
    {
        if ($seconds <= 0)   return '0 jam';
        $hari = (int) floor($seconds / 86400);
        if ($hari >= 1)      return $hari . ' hari';
        $jam  = (int) floor($seconds / 3600);
        if ($jam  >= 1)      return $jam  . ' jam';
        return '< 1 jam';
    }
}
