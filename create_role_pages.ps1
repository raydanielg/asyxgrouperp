$roles = @{
    'director' = @('reports','projects','sales-dashboard','employees','sales-invoices','purchase-invoices','expenses','tickets')
    'finance-officer' = @('sales-invoices','purchase-invoices','expenses','revenues','bills','bank-accounts','transfers','reports')
    'hr-officer' = @('employees','attendance','payroll','leaves','performance','training','recruitment','assets','policies')
    'auditor' = @('sales-invoices','purchase-invoices','expenses','revenues','bills','bank-accounts','reports')
    'admin-manager' = @('users','roles','employees','attendance','leaves','reports','settings')
    'cashier' = @('pos','pos-reports','sales-invoices','products','revenues')
    'technical-manager' = @('tickets','projects','timesheets','bugs','employees')
    'technician' = @('tickets','projects','timesheets','bugs')
    'ict-officer' = @('tickets','projects','bugs','assets','employees')
    'ict-engineer' = @('tickets','projects','bugs','assets','settings')
    'project-manager' = @('projects','timesheets','bugs','deals','reports')
    'operations-manager' = @('products','warehouses','stock-movements','sales-invoices','purchase-invoices','projects','reports')
    'logistics-officer' = @('products','warehouses','stock-movements','suppliers','inventory-transfers','purchase-invoices')
    'receptionist' = @('leads','contacts','tickets')
    'call-center-agent' = @('leads','contacts','tickets')
    'legal-officer' = @('contracts','contacts','projects','reports')
    'supervisor' = @('employees','attendance','leaves','projects','pos','products','reports')
    'administrator' = @('users','roles','employees','projects','products','settings','reports')
}

$basePath = "c:\Users\Administrator\Desktop\Djanproject\resources\views\roles"

foreach ($roleName in $roles.Keys) {
    $roleDir = Join-Path $basePath $roleName
    $pagesDir = Join-Path $roleDir "pages"
    if (!(Test-Path $pagesDir)) { New-Item -ItemType Directory -Path $pagesDir -Force | Out-Null }

    foreach ($module in $roles[$roleName]) {
        $filePath = Join-Path $pagesDir "$module.blade.php"
        if (Test-Path $filePath) { continue }

        $content = @"
@extends('roles.shared.page')
@section('title', ucfirst(str_replace('-', ' ', '$module')) . ' - ' . `$roleLabel)
@endsection
"@
        Set-Content -Path $filePath -Value $content -NoNewline -Encoding UTF8
        Write-Output "Created: $filePath"
    }
}
