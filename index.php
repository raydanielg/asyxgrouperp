<?php
/**
 * ASYX Group ERP System - Root Entry Point
 * 
 * This file ensures that visitors to the root directory
 * are properly redirected to the Laravel public/ directory.
 * It also handles SSL enforcement.
 */

// Detect if we're behind a proxy/load balancer that terminates SSL
$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
    || (isset($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false);

// Force HTTPS redirect if accessing via HTTP (and not localhost)
if (!$isSecure
    && (php_sapi_name() !== 'cli-server')
    && !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])
) {
    $httpsUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost')
        . $_SERVER['REQUEST_URI'] ?? '/';
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $httpsUrl);
    exit;
}

// If this is a CLI request, don't redirect
if (php_sapi_name() === 'cli') {
    return;
}

// If the request is already inside /public, don't loop
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

// Check if we're already in the public directory
if (strpos($scriptName, '/public/') !== false || strpos($requestUri, '/public/') === 0) {
    // Already in public, let the request proceed
    return;
}

// Redirect to public/ directory
$redirectUrl = '/public' . $requestUri;
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . $redirectUrl);
exit;
