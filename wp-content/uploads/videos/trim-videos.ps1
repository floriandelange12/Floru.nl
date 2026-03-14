# ============================================================
# Video Trim Script - Floru Client Pages
# ============================================================
# Trims each video to the best 12-second cinematic fragment.
# Videos play as muted autoplay loops on client detail pages.
#
# HOW TO USE:
#   1. Open PowerShell in this folder (wp-content/uploads/videos/)
#   2. Run: .\trim-videos.ps1
#   3. Originals are kept as {name}-original.mp4
#   4. Trimmed versions replace the original filenames
#
# REQUIRES: ffmpeg in PATH
# ============================================================

$videoDir = $PSScriptRoot

# Define trim points: [filename, start_seconds, duration_seconds, description]
$trims = @(
    @("g4s.mp4",            7,  12, "Skyline The Shard golden light > security operations"),
    @("cnim.mp4",          30,  12, "Military truck driving through water - big splash"),
    @("exensor.mp4",       25,  12, "Motion camera sensor hidden in grass - cinematic"),
    @("exensor-2.mp4",     30,  12, "Soldier with rugged PDA in forest"),
    @("everbridge.mp4",    42,  12, "Alert icon composition on orange (animated - best available)"),
    @("nu-security.mp4",   93,  12, "Server corridor walk with pink ceiling lighting"),
    @("saab.mp4",          59,  12, "Gripen fighter jet takeoff > in-flight > CGI visualization"),
    @("stratego-work.mp4",  3,  12, "TEDx Oakland stage - speaker with name title"),
    @("tbm.mp4",           40,  12, "Blue camo tank > futuristic unmanned vessel display"),
    @("x-systems.mp4",     25,  12, "Cyberpunk code visualization around secure phone")
)

Write-Host ""
Write-Host "=== Floru Video Trimmer ===" -ForegroundColor Cyan
Write-Host "Trimming $($trims.Count) videos to 12-second loops..."
Write-Host ""

foreach ($trim in $trims) {
    $file     = $trim[0]
    $start    = $trim[1]
    $duration = $trim[2]
    $desc     = $trim[3]
    
    $srcPath  = Join-Path $videoDir $file
    $bakPath  = Join-Path $videoDir ($file -replace '\.mp4$', '-original.mp4')
    $tmpPath  = Join-Path $videoDir ($file -replace '\.mp4$', '-trimmed.mp4')

    if (-not (Test-Path $srcPath)) {
        Write-Host "[SKIP] $file - not found" -ForegroundColor Yellow
        continue
    }

    if (Test-Path $bakPath) {
        Write-Host "[SKIP] $file - already trimmed (backup exists)" -ForegroundColor Yellow
        continue
    }

    Write-Host "[TRIM] $file  |  ${start}s + ${duration}s  |  $desc" -ForegroundColor Green

    # Trim: stream-copy (no re-encode), strip audio
    $stderrLog = Join-Path $videoDir "ffmpeg-log.tmp"
    $proc = Start-Process -FilePath "ffmpeg" -ArgumentList "-ss $start -i `"$srcPath`" -t $duration -c copy -an -y `"$tmpPath`"" -NoNewWindow -Wait -PassThru -RedirectStandardError $stderrLog
    if (Test-Path $stderrLog) { Remove-Item $stderrLog -Force }

    if ($proc.ExitCode -ne 0) {
        Write-Host "  ERROR: ffmpeg failed for $file" -ForegroundColor Red
        if (Test-Path $tmpPath) { Remove-Item $tmpPath }
        continue
    }

    # Backup original, replace with trimmed
    Rename-Item $srcPath $bakPath
    Rename-Item $tmpPath $srcPath

    $sizeMB = [math]::Round((Get-Item $srcPath).Length / 1MB, 1)
    Write-Host "  Done: ${sizeMB} MB" -ForegroundColor DarkGreen
}

# Handle g4s-2 separately (already trimmed by user)
$g4s2Trimmed = Join-Path $videoDir "g4s-2-trimmed.mp4"
$g4s2Target  = Join-Path $videoDir "g4s-2.mp4"
$g4s2Backup  = Join-Path $videoDir "g4s-2-original.mp4"

if ((Test-Path $g4s2Trimmed) -and -not (Test-Path $g4s2Backup)) {
    Write-Host "[RENAME] g4s-2-trimmed.mp4 > g4s-2.mp4" -ForegroundColor Green
    Rename-Item $g4s2Target $g4s2Backup
    Rename-Item $g4s2Trimmed $g4s2Target
    Write-Host "  Done" -ForegroundColor DarkGreen
}

Write-Host ""
Write-Host "=== Complete ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "NOTE: dujardin.mp4 is missing - it was never downloaded." -ForegroundColor Yellow
Write-Host "Original files are preserved as *-original.mp4" -ForegroundColor Gray
Write-Host ""
