<?php

/**
 * Asset Helper for Cache-Busting Implementation
 * 
 * This helper provides comprehensive cache-busting functionality for CSS and JavaScript files
 * across the entire application including agent dashboard, admin dashboard, and public website.
 * 
 * Features:
 * - Automatic cache-busting using file modification timestamps
 * - Support for both local assets and CDN-hosted libraries
 * - Fallback mechanisms for missing files
 * - Environment-aware versioning
 * - Memory-efficient file checking
 * 
 * @author White Rock Realtor Team
 * @version 1.0 - Initial cache-busting implementation
 * @since 2025-08-26
 */

if (!function_exists('asset_url')) {
    /**
     * Generate cache-busted URL for local asset files
     * 
     * This function automatically appends a cache-busting parameter based on
     * the file's modification timestamp. If the file doesn't exist, it falls
     * back to a version number to prevent broken links.
     * 
     * @param string $path Asset path relative to public directory (e.g., 'assets/css/style.css')
     * @param bool $absolute Whether to return absolute URL (default: true)
     * @return string Cache-busted asset URL
     * 
     * @example
     * asset_url('assets/css/style.css') 
     * // Returns: http://example.com/assets/css/style.css?v=1693123456
     * 
     * asset_url('assets/js/dashboard.js')
     * // Returns: http://example.com/assets/js/dashboard.js?v=1693123789
     */
    function asset_url(string $path, bool $absolute = true): string
    {
        // Remove leading slash if present for consistency
        $path = ltrim($path, '/');
        
        // Get the full file path
        $filePath = FCPATH . $path;
        
        // Generate cache-busting parameter
        $version = get_asset_version($filePath);
        
        // Build the URL with cache-busting parameter
        $baseUrl = $absolute ? base_url($path) : '/' . $path;
        
        return $baseUrl . '?v=' . $version;
    }
}

if (!function_exists('cdn_url')) {
    /**
     * Generate cache-busted URL for CDN-hosted libraries
     * 
     * For CDN assets, we use a combination of application version and
     * environment-specific versioning to ensure cache invalidation when needed.
     * 
     * @param string $url CDN URL
     * @param string $fallbackVersion Optional fallback version (default: app version)
     * @return string Cache-busted CDN URL
     * 
     * @example
     * cdn_url('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css')
     * // Returns: https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css?v=1.0.0
     */
    function cdn_url(string $url, string $fallbackVersion = null): string
    {
        // Get application version for CDN cache-busting
        $version = $fallbackVersion ?? get_app_version();
        
        // Check if URL already has query parameters
        $separator = strpos($url, '?') !== false ? '&' : '?';
        
        return $url . $separator . 'v=' . $version;
    }
}

if (!function_exists('get_asset_version')) {
    /**
     * Get cache-busting version for an asset file
     * 
     * Uses file modification timestamp for accurate cache invalidation.
     * Falls back to application version if file doesn't exist.
     * 
     * @param string $filePath Full path to the asset file
     * @return string Version string for cache-busting
     */
    function get_asset_version(string $filePath): string
    {
        // Check if file exists and get modification time
        if (file_exists($filePath) && is_readable($filePath)) {
            $mtime = filemtime($filePath);
            if ($mtime !== false) {
                return (string) $mtime;
            }
        }
        
        // Fallback to application version
        return get_app_version();
    }
}

if (!function_exists('get_app_version')) {
    /**
     * Get application version for cache-busting
     *
     * Returns a version string based on environment and configuration.
     * In development, uses current timestamp for aggressive cache-busting.
     * In production, uses a stable version number from App config.
     *
     * @return string Application version string
     */
    function get_app_version(): string
    {
        // In development, use timestamp for aggressive cache-busting
        if (ENVIRONMENT === 'development') {
            return (string) time();
        }

        // In production, use stable version number from App config
        try {
            $config = config('App');
            return $config->appVersion ?? '1.0.0';
        } catch (\Exception $e) {
            // Fallback if config is not available
            return '1.0.0';
        }
    }
}

if (!function_exists('css_link')) {
    /**
     * Generate HTML link tag for CSS files with cache-busting
     * 
     * Automatically determines if the URL is local or CDN and applies
     * appropriate cache-busting strategy.
     * 
     * @param string $href CSS file URL or path
     * @param array $attributes Additional HTML attributes
     * @return string HTML link tag
     * 
     * @example
     * css_link('assets/css/style.css')
     * // Returns: <link href="/assets/css/style.css?v=1693123456" rel="stylesheet">
     * 
     * css_link('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css')
     * // Returns: <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css?v=1.0.0" rel="stylesheet">
     */
    function css_link(string $href, array $attributes = []): string
    {
        // Determine if this is a local asset or CDN
        $isLocal = !filter_var($href, FILTER_VALIDATE_URL);
        
        // Apply appropriate cache-busting
        $url = $isLocal ? asset_url($href) : cdn_url($href);
        
        // Build attributes
        $attrs = array_merge([
            'href' => $url,
            'rel' => 'stylesheet'
        ], $attributes);
        
        // Generate HTML
        $attrString = '';
        foreach ($attrs as $key => $value) {
            $attrString .= ' ' . esc($key) . '="' . esc($value) . '"';
        }
        
        return '<link' . $attrString . '>';
    }
}

if (!function_exists('js_script')) {
    /**
     * Generate HTML script tag for JavaScript files with cache-busting
     * 
     * Automatically determines if the URL is local or CDN and applies
     * appropriate cache-busting strategy.
     * 
     * @param string $src JavaScript file URL or path
     * @param array $attributes Additional HTML attributes
     * @return string HTML script tag
     * 
     * @example
     * js_script('assets/js/website.js')
     * // Returns: <script src="/assets/js/website.js?v=1693123456"></script>
     * 
     * js_script('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js')
     * // Returns: <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js?v=1.0.0"></script>
     */
    function js_script(string $src, array $attributes = []): string
    {
        // Determine if this is a local asset or CDN
        $isLocal = !filter_var($src, FILTER_VALIDATE_URL);
        
        // Apply appropriate cache-busting
        $url = $isLocal ? asset_url($src) : cdn_url($src);
        
        // Build attributes
        $attrs = array_merge([
            'src' => $url
        ], $attributes);
        
        // Generate HTML
        $attrString = '';
        foreach ($attrs as $key => $value) {
            $attrString .= ' ' . esc($key) . '="' . esc($value) . '"';
        }
        
        return '<script' . $attrString . '></script>';
    }
}

if (!function_exists('preload_asset')) {
    /**
     * Generate HTML link tag for asset preloading with cache-busting
     * 
     * Useful for critical CSS and JavaScript files that should be preloaded
     * for better performance.
     * 
     * @param string $href Asset URL or path
     * @param string $as Resource type (style, script, font, etc.)
     * @param array $attributes Additional HTML attributes
     * @return string HTML link tag for preloading
     * 
     * @example
     * preload_asset('assets/css/style.css', 'style')
     * // Returns: <link href="/assets/css/style.css?v=1693123456" rel="preload" as="style">
     */
    function preload_asset(string $href, string $as, array $attributes = []): string
    {
        // Determine if this is a local asset or CDN
        $isLocal = !filter_var($href, FILTER_VALIDATE_URL);
        
        // Apply appropriate cache-busting
        $url = $isLocal ? asset_url($href) : cdn_url($href);
        
        // Build attributes
        $attrs = array_merge([
            'href' => $url,
            'rel' => 'preload',
            'as' => $as
        ], $attributes);
        
        // Generate HTML
        $attrString = '';
        foreach ($attrs as $key => $value) {
            $attrString .= ' ' . esc($key) . '="' . esc($value) . '"';
        }
        
        return '<link' . $attrString . '>';
    }
}
