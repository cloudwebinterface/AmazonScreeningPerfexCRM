$ErrorActionPreference = "Stop"

$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

$env:Path = [System.Environment]::GetEnvironmentVariable("Path", "Machine") + ";" +
    [System.Environment]::GetEnvironmentVariable("Path", "User")

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Error "PHP is not installed or not on PATH. Install PHP 8.1+ and restart your terminal."
}

if (-not (Test-Path "application\config\app-config.php")) {
    Copy-Item "application\config\app-config-sample.php" "application\config\app-config.php"
    Write-Host "Created application/config/app-config.php from sample. Edit database credentials before continuing."
}

Write-Host "Starting AmazonScreening at http://localhost:8080/"
Write-Host "Admin login: http://localhost:8080/admin/authentication"
Write-Host "Press Ctrl+C to stop."

php -S localhost:8080 router.php
