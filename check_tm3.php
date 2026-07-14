<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "payrolls has company_id: " . (Schema::hasColumn('payrolls', 'company_id') ? 'YES' : 'NO') . "\n";
echo "payrolls has overtime: " . (Schema::hasColumn('payrolls', 'overtime') ? 'YES' : 'NO') . "\n";
echo "payrolls columns: " . implode(', ', Schema::getColumnListing('payrolls')) . "\n";
