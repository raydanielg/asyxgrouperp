<?php
$_SERVER['HTTP_HOST']='localhost';
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$views=['admin.users.login-history','admin.roles.index','admin.profile','admin.users.create','admin.roles.create','admin.roles.edit','dashboard.role','pdf.role-report'];
foreach($views as $v){
  try{
    view($v)->render();
    echo $v . ": OK\n";
  }catch(Throwable $e){
    echo $v . ": FAIL - " . $e->getMessage() . "\n";
  }
}
