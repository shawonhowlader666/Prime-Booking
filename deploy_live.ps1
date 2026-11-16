<#
.SYNOPSIS
    Prime Booking 1-Click Live VPS Deployment Script (PowerShell for Windows)
.DESCRIPTION
    Safely deploys changes to live VPS hostinger-prime (31.97.238.194:65002) and clears server cache.
#>

param(
    [string]$KeyPath = "$HOME\.ssh\id_vps_deploy",
    [string]$RemoteHost = "31.97.238.194",
    [int]$Port = 65002,
    [string]$User = "u345402313",
    [string]$RemotePath = "/home/u345402313/domains/primebooking.com.bd/prime-booking/"
)

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "  PRIME BOOKING - LIVE VPS 1-CLICK DEPLOYER" -ForegroundColor Yellow
Write-Host "==========================================================" -ForegroundColor Cyan

if (-not (Test-Path $KeyPath)) {
    Write-Host "[ERROR] SSH key not found at $KeyPath" -ForegroundColor Red
    Write-Host "Please place your 'id_vps_deploy' file in '$HOME\.ssh\id_vps_deploy'" -ForegroundColor Yellow
    exit 1
}

Write-Host "[1/3] Checking Git Status..." -ForegroundColor Green
git status --short

Write-Host "[2/3] Syncing latest Git changes on VPS..." -ForegroundColor Green
ssh -p $Port -i $KeyPath -o StrictHostKeyChecking=no "$User@$RemoteHost" "cd $RemotePath && git pull origin master"

Write-Host "[3/3] Clearing Laravel View and Application Cache..." -ForegroundColor Green
ssh -p $Port -i $KeyPath -o StrictHostKeyChecking=no "$User@$RemoteHost" "cd $RemotePath && php artisan view:clear && php artisan cache:clear && php artisan route:clear"

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "  SUCCESS! Live server updated and caches cleared!" -ForegroundColor Green
Write-Host "  Website: https://primebooking.com.bd" -ForegroundColor Yellow
Write-Host "==========================================================" -ForegroundColor Cyan

