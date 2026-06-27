<?php

if (!function_exists('tzs')) {
    /**
     * Format a number as Tanzanian Shillings.
     * e.g. tzs(1500000) => "TZS 1,500,000"
     */
    function tzs($amount, bool $withSymbol = true): string
    {
        $value = number_format((float) ($amount ?? 0), 0, '.', ',');
        return $withSymbol ? 'TZS ' . $value : $value;
    }
}

if (!function_exists('tzs_short')) {
    /**
     * Format TZS in short form for compact UI.
     * e.g. tzs_short(1500000) => "1.5M"
     */
    function tzs_short($amount): string
    {
        $val = (float) ($amount ?? 0);
        if ($val >= 1000000000) return 'TZS ' . round($val / 1000000000, 1) . 'B';
        if ($val >= 1000000) return 'TZS ' . round($val / 1000000, 1) . 'M';
        if ($val >= 1000) return 'TZS ' . round($val / 1000, 1) . 'K';
        return 'TZS ' . number_format($val, 0, '.', ',');
    }
}
