<?php

use Illuminate\Support\Str;

if (!function_exists('hasAnyPermissionOnResource')) {
    function hasAnyPermissionOnResource(string $resource): bool
    {
        $user = auth('administration')->user();
        if (!$user) return false;

        foreach (['view', 'create', 'edit', 'delete'] as $action) {
            if ($user->can("{$resource}.{$action}")) return true;
        }
        return false;
    }
}

if (!function_exists('limit_description')) {
    function limit_description(string $text, int $length = 100, string $end = '...'): string
    {
        return Str::limit(strip_tags($text), $length, $end);
        // return Str::limit($text, $length, $end);
    }
}

if (!function_exists('getPageUrl')) {
    function getPageUrl($page)
    {
        if ($page->page_structure === 'URL' && $page->external_url) {
            return $page->external_url;
        } elseif ($page->page_structure === 'Page' && $page->connected_page) {
            return url(strtolower($page->connected_page));
        } else {
            return url('content/' . $page->slug);
        }
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date in a flexible, globally controlled way.
     *
     * @param string|\DateTimeInterface|null $date
     * @param string|null $format
     * @return string
     */
    function format_date($date, $format = null): string
    {
        if (!$date) {
            return '';
        }

        // You can define a global default format here
        $defaultFormat = config('app.date_format', 'd M Y'); // Example: 15 Jul 2025

        // If $date is string, convert to Carbon
        try {
            $dateObj = \Carbon\Carbon::parse($date);
        } catch (\Exception $e) {
            return '';
        }

        return $dateObj->format($format ?? $defaultFormat);
    }
}

