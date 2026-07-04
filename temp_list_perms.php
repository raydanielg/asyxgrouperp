<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$perms = App\Models\Permission::orderBy('module')->orderBy('name')->limit(200)->get();
foreach ($perms as $p) {
    echo $p->module . '/' . $p->name . PHP_EOL;
}
