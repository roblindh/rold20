<#
.SYNOPSIS
    RoL d20 - Deployment Script for QNAP NAS

.DESCRIPTION
    Syncs the project files to the QNAP network share while excluding
    sensitive files, IDE metadata, test directories, temporary data,
    the legacy backup directory, and other co-hosted projects
    (RPGMapperTool, RPGWorldAtlas).

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

# Directories to exclude from deployment and deletion (protects other co-hosted projects and runtime state)
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
    ".qpkg",
    "storage",
    "bootstrap/cache",
    "legacy",
    "RPGWorldAtlas",
    "rpgworldatlas",
    "RPGMapperTool",
    "rpgmappertool"
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

$DstSiteUpper = Join-Path $Destination "public\styles\Site.css"
$DstSiteLower = Join-Path $Destination "public\styles\site.css"
if ((Test-Path $DstSiteUpper) -and !(Test-Path $DstSiteLower)) {
    try {
        Copy-Item -Path $DstSiteUpper -Destination $DstSiteLower -Force -ErrorAction SilentlyContinue
    } catch {}
}

# Clear stale compiled views, page caches, and config cache on destination
$DstViewsCache = Join-Path $Destination "storage\framework\views"
if (Test-Path $DstViewsCache) {
    try {
        Get-ChildItem -Path $DstViewsCache -Filter "*.php" -File | Remove-Item -Force -ErrorAction SilentlyContinue
        Write-Host "Cleared stale Blade view cache on destination." -ForegroundColor Cyan
    } catch {}
}

$DstPageCache = Join-Path $Destination "storage\framework\cache\pages"
if (Test-Path $DstPageCache) {
    try {
        Get-ChildItem -Path $DstPageCache -File | Remove-Item -Force -ErrorAction SilentlyContinue
        Write-Host "Cleared stale page HTML cache on destination." -ForegroundColor Cyan
    } catch {}
}

$DstConfigCache = Join-Path $Destination "bootstrap\cache\config.php"
if (Test-Path $DstConfigCache) {
    try {
        Remove-Item -Path $DstConfigCache -Force -ErrorAction SilentlyContinue
        Write-Host "Cleared stale bootstrap config cache on destination." -ForegroundColor Cyan
    } catch {}
}

# Automatically trigger OPcache & in-container cache purge via HTTP if server is up
try {
    $ResetUri = "http://ROL-NAS-MINI:8090/opcache_reset.php"
    $Response = Invoke-WebRequest -Uri $ResetUri -UseBasicParsing -TimeoutSec 5 -ErrorAction SilentlyContinue
    if ($Response.StatusCode -eq 200) {
        Write-Host "Triggered OPcache reset & in-container cache purge via HTTP ($ResetUri)." -ForegroundColor Green
    }
} catch {}

# Robocopy exit codes: 0-7 mean success/copies occurred; 8+ means errors
if ($RoboExitCode -lt 8) {
    Write-Host "`nDeployment completed successfully! (Robocopy Code: $RoboExitCode)" -ForegroundColor Green
} else {
    Write-Host "`nDeployment encountered errors! (Robocopy Code: $RoboExitCode)" -ForegroundColor Red
}
