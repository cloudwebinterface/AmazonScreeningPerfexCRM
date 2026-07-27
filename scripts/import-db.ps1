param(
    [Parameter(Mandatory = $true)]
    [string]$Password,

    [string]$SqlFile = "D:\Dropbox\Work\Projects\amazonscreening.com\backups\crm_az.sql",

    [string]$Database = "crm_az"
)

$ErrorActionPreference = "Stop"
$mysql = "C:\Program Files\MySQL\MySQL Server 8.4\bin\mysql.exe"

if (-not (Test-Path $mysql)) {
    Write-Error "MySQL client not found at $mysql"
}

if (-not (Test-Path $SqlFile)) {
    Write-Error "SQL dump not found: $SqlFile"
}

Write-Host "Creating database '$Database'..."
& $mysql -u root "-p$Password" -e "CREATE DATABASE IF NOT EXISTS ``$Database`` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

Write-Host "Importing dump (this may take a few minutes)..."
Get-Content $SqlFile -Raw | & $mysql -u root "-p$Password" $Database

Write-Host "Done. Database '$Database' is ready."
