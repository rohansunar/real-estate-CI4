<?php

if (!function_exists('getYouTubeEmbedUrl')) {
    /**
     * Convert YouTube URL to embed URL
     */
    function getYouTubeEmbedUrl($url)
    {
        if (empty($url)) {
            return '';
        }

        // Extract video ID from various YouTube URL formats
        $videoId = '';
        
        if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $matches)) {
            $videoId = $matches[1];
        } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $matches)) {
            $videoId = $matches[1];
        } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }

        if ($videoId) {
            return "https://www.youtube.com/embed/{$videoId}?rel=0&showinfo=0&modestbranding=1";
        }

        return $url; // Return original URL if not a recognized YouTube format
    }
}

if (!function_exists('formatPropertyPrice')) {
    /**
     * Format property price for display
     */
    function formatPropertyPrice($price)
    {
        if (empty($price) || $price <= 0) {
            return 'Price on Request';
        }

        if ($price >= 10000000) { // 1 crore
            return '₹' . number_format($price / 10000000, 2) . ' Cr';
        } elseif ($price >= 100000) { // 1 lakh
            return '₹' . number_format($price / 100000, 2) . ' L';
        } else {
            return '₹' . number_format($price);
        }
    }
}

if (!function_exists('getPropertyTypeIcon')) {
    /**
     * Get icon class for property type
     */
    function getPropertyTypeIcon($type)
    {
        $icons = [
            'house' => 'fas fa-home',
            'apartment' => 'fas fa-building',
            'villa' => 'fas fa-crown',
            'land' => 'fas fa-map',
            'commercial' => 'fas fa-store',
            'office' => 'fas fa-briefcase'
        ];

        return $icons[$type] ?? 'fas fa-home';
    }
}

if (!function_exists('getPropertyStatusBadge')) {
    /**
     * Get status badge class for property
     */
    function getPropertyStatusBadge($status)
    {
        $badges = [
            'available' => 'badge bg-success',
            'sold' => 'badge bg-danger',
            'rented' => 'badge bg-warning',
            'pending' => 'badge bg-info'
        ];

        return $badges[$status] ?? 'badge bg-secondary';
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Get human readable time difference
     */
    function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) {
            return 'just now';
        } elseif ($time < 3600) {
            return floor($time / 60) . ' minutes ago';
        } elseif ($time < 86400) {
            return floor($time / 3600) . ' hours ago';
        } elseif ($time < 2592000) {
            return floor($time / 86400) . ' days ago';
        } elseif ($time < 31536000) {
            return floor($time / 2592000) . ' months ago';
        } else {
            return floor($time / 31536000) . ' years ago';
        }
    }
}
