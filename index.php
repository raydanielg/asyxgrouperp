<?php
/**
 * ASYX Group ERP System - Root Entry Point
 * Redirects visitors to the Laravel public/ directory.
 * SSL is handled by cPanel/server configuration.
 */

if (php_sapi_name() === 'cli') {
    return;
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

// Check if we're already in the public directory
if (strpos($scriptName, '/public/') !== false || strpos($requestUri, '/public/') === 0) {
    return;
}

// Redirect to public/ directory
$redirectUrl = '/public' . $requestUri;
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . $redirectUrl);
exit;
