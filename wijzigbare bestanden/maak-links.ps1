$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
$sourceRoot = Join-Path $projectRoot 'wp-content\themes\astra-child'
$targetRoot = Join-Path $projectRoot 'wijzigbare bestanden\astra-child'

if (-not (Test-Path $sourceRoot)) {
    throw "Bronmap niet gevonden: $sourceRoot"
}

New-Item -ItemType Directory -Path $targetRoot -Force | Out-Null

function Remove-ExistingTarget {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Path
    )

    if (Test-Path $Path) {
        Remove-Item -Path $Path -Recurse -Force
    }
}

function New-SyncedFileLink {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Source,
        [Parameter(Mandatory = $true)]
        [string]$Target
    )

    Remove-ExistingTarget -Path $Target
    New-Item -ItemType HardLink -Path $Target -Target $Source | Out-Null
}

function New-SyncedDirectoryLink {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Source,
        [Parameter(Mandatory = $true)]
        [string]$Target
    )

    Remove-ExistingTarget -Path $Target
    New-Item -ItemType Junction -Path $Target -Target $Source | Out-Null
}

New-SyncedFileLink -Source (Join-Path $sourceRoot 'style.css') -Target (Join-Path $targetRoot 'style.css')
New-SyncedFileLink -Source (Join-Path $sourceRoot 'functions.php') -Target (Join-Path $targetRoot 'functions.php')
New-SyncedFileLink -Source (Join-Path $sourceRoot 'footer.php') -Target (Join-Path $targetRoot 'footer.php')
New-SyncedFileLink -Source (Join-Path $sourceRoot 'single-floru_client.php') -Target (Join-Path $targetRoot 'single-floru_client.php')

New-SyncedDirectoryLink -Source (Join-Path $sourceRoot 'assets') -Target (Join-Path $targetRoot 'assets')
New-SyncedDirectoryLink -Source (Join-Path $sourceRoot 'inc') -Target (Join-Path $targetRoot 'inc')
New-SyncedDirectoryLink -Source (Join-Path $sourceRoot 'template-parts') -Target (Join-Path $targetRoot 'template-parts')
New-SyncedDirectoryLink -Source (Join-Path $sourceRoot 'templates') -Target (Join-Path $targetRoot 'templates')

Write-Host 'Klaar. Gesynchroniseerde links zijn aangemaakt in:'
Write-Host $targetRoot
