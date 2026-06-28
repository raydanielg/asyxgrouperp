<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach (App\Models\Permission::orderBy('module')->orderBy('name')->get() as $p) {
    echo $p->module . ' | ' . $p->name . ' | ' . $p->label . PHP_EOL;
}
