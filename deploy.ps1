<#
.SYNOPSIS
    RoL d20 - Deployment Script for QNAP NAS

.DESCRIPTION
    Syncs the project files to the QNAP network share while excluding
    sensitive files, IDE metadata, test directories, and temporary data.

.PARAMETER Destination
    Target network share path (Default: \\ROL-NAS-MINI\Container\rold20)
#>

param(
    [string]$Destination = "\\ROL-NAS-MINI\Container\rold20"
)

$Source = $PSScriptRoot

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  RoL d20 Deployment to QNAP NAS" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Source:      $Source" -ForegroundColor Gray
Write-Host "Destination: $Destination" -ForegroundColor Gray
Write-Host ""

if (-not (Test-Path $Destination)) {
    Write-Host "Destination path does not exist. Creating: $Destination" -ForegroundColor Yellow
    try {
        New-Item -ItemType Directory -Path $Destination -Force | Out-Null
    }
    catch {
        Write-Error "Failed to create or access destination: $_"
        exit 1
    }
}

# Directories to exclude from deployment and deletion
$ExcludeDirs = @(
    ".git",
    ".phpunit.cache",
    "nbproject",
    "tests",
    "ToDo",
    "logs",
    "node_modules",
    "container-station-data",
    "@Recycle",
    ".qpkg"
)

# Files to exclude from deployment
$ExcludeFiles = @(
    "*.sln",
    "*.log",
    "phpunit.xml",
    ".gitignore",
    ".dockerignore"
)

Write-Host "Starting Robocopy sync..." -ForegroundColor Green
$RoboArgs = @(
    $Source,
    $Destination,
    "/MIR",
    "/FFT",
    "/Z",
    "/R:2",
    "/W:3",
    "/XD"
) + $ExcludeDirs + @("/XF") + $ExcludeFiles

& robocopy @RoboArgs

$RoboExitCode = $LASTEXITCODE

# Ensure lowercase 'styles' directory exists for Linux case-sensitivity
$StylesSrc = Join-Path $Destination "Styles"
$StylesDst = Join-Path $Destination "styles"
if ((Test-Path $StylesSrc) -and -not (Test-Path $StylesDst)) {
    try {
        Copy-Item -Path $StylesSrc -Destination $StylesDst -Recurse -Force
        Write-Host "Created lowercase styles/ directory for Linux web compatibility." -ForegroundColor Cyan
    } catch {
        # Ignore if junction or filesystem is case-insensitive
    }
}

# Robocopy exit codes: 0-7 mean success/copies occurred; 8+ means errors
if ($RoboExitCode -lt 8) {
    Write-Host "`nDeployment completed successfully! (Robocopy Code: $RoboExitCode)" -ForegroundColor Green
} else {
    Write-Host "`nDeployment encountered errors! (Robocopy Code: $RoboExitCode)" -ForegroundColor Red
}
