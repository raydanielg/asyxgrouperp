<?php
$_SERVER['HTTP_HOST']='localhost';
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
view()->share('errors', new Illuminate\Support\ViewErrorBag);
try {
  $html = view('auth.login')->render();
  echo "auth.login: OK (" . strlen($html) . " bytes)\n";
} catch (Throwable $e) {
  echo "auth.login: FAIL - " . $e->getMessage() . "\n";
}
