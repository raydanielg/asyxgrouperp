<?php
$_SERVER['HTTP_HOST']='localhost'; $_SERVER['REQUEST_URI']='/login'; $_SERVER['REQUEST_METHOD']='GET';
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
try {
  $request = Illuminate\Http\Request::capture();
  $response = $kernel->handle($request);
  echo 'Status: ' . $response->getStatusCode() . "\n";
  $html = $response->getContent();
  echo 'Size: ' . strlen($html) . " bytes\n";
  if (strpos($html, 'Sign In') !== false) echo "Has 'Sign In': yes\n";
  if (strpos($html, 'swetalert') !== false || strpos($html, 'SweetAlert') !== false || strpos($html, 'Swal') !== false) echo "Has SweetAlert: yes\n";
  if (strpos($html, 'tailwind') !== false || strpos($html, 'cdn.tailwindcss') !== false) echo "Has Tailwind: yes\n";
} catch (Throwable $e) {
  echo 'Error: ' . $e->getMessage() . "\n";
}
