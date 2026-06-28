<?php
$_SERVER['HTTP_HOST']='localhost';
$_SERVER['REQUEST_URI']='/login';
$_SERVER['REQUEST_METHOD']='GET';

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
  $request = Illuminate\Http\Request::capture();
  $response = $kernel->handle($request);
  echo 'Status: ' . $response->getStatusCode() . "\n";
  if ($response->isRedirection()) {
    echo 'Redirect to: ' . $response->headers->get('Location') . "\n";
  } else {
    echo 'OK - ' . strlen($response->getContent()) . " bytes\n";
    // Check if it contains login form
    if (strpos($response->getContent(), 'Sign In') !== false) {
      echo "Login form found!\n";
    }
  }
} catch (Throwable $e) {
  echo 'Error: ' . $e->getMessage() . "\n";
  echo $e->getTraceAsString() . "\n";
}
