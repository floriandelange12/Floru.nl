# fix-videos.ps1 — Remove x-systems video, re-trim stratego-work + saab WITH audio
# Run from: wp-content/uploads/videos/

$ffmpeg = "ffmpeg"
$dir = $PSScriptRoot
if (-not $dir) { $dir = Get-Location }

Set-Location $dir

Write-Host "`n=== Step 1: Remove x-systems video ===" -ForegroundColor Cyan

if (Test-Path "x-systems.mp4") {
    if (-not (Test-Path "x-systems-removed.mp4")) {
        Rename-Item "x-systems.mp4" "x-systems-removed.mp4"
        Write-Host "  Renamed x-systems.mp4 -> x-systems-removed.mp4 (page will no longer show video)" -ForegroundColor Green
    } else {
        Remove-Item "x-systems.mp4"
        Write-Host "  Deleted x-systems.mp4 (backup already exists)" -ForegroundColor Green
    }
} else {
    Write-Host "  x-systems.mp4 not found, already removed" -ForegroundColor Yellow
}

Write-Host "`n=== Step 2: Re-trim videos WITH audio ===" -ForegroundColor Cyan

# Videos that benefit from audio:
# - stratego-work: TEDx talk (speech)
# - saab: Gripen jet (engine sounds, narration)
$trims = @(
    @{ name="stratego-work"; start="3";  duration="12"; reason="TEDx talk - speech" },
    @{ name="saab";          start="59"; duration="12"; reason="Gripen jet - engine sound" }
)

foreach ($t in $trims) {
    $original = "$($t.name)-original.mp4"
    $output   = "$($t.name).mp4"

    if (-not (Test-Path $original)) {
        Write-Host "  SKIP $($t.name): no original file found ($original)" -ForegroundColor Yellow
        continue
    }

    Write-Host "  Trimming $($t.name) from ${original} (start=$($t.start)s, dur=$($t.duration)s) - $($t.reason)" -ForegroundColor White

    $tmpFile = "$($t.name)-withaudio.mp4"
    $errLog  = [System.IO.Path]::GetTempFileName()

    # Trim WITH audio (no -an flag)
    $proc = Start-Process -FilePath $ffmpeg `
        -ArgumentList "-y -ss $($t.start) -t $($t.duration) -i `"$original`" -c copy `"$tmpFile`"" `
        -NoNewWindow -Wait -PassThru -RedirectStandardError $errLog

    if ($proc.ExitCode -eq 0 -and (Test-Path $tmpFile)) {
        # Replace current (muted) trimmed version
        if (Test-Path $output) { Remove-Item $output }
        Rename-Item $tmpFile $output
        $size = [math]::Round((Get-Item $output).Length / 1MB, 1)
        Write-Host "  OK: $output (${size} MB, with audio)" -ForegroundColor Green
    } else {
        Write-Host "  FAIL: $($t.name) - check $errLog" -ForegroundColor Red
        if (Test-Path $tmpFile) { Remove-Item $tmpFile }
    }

    if (Test-Path $errLog) { Remove-Item $errLog }
}

Write-Host "`n=== Done ===" -ForegroundColor Cyan
Write-Host "Refresh your browser to see changes.`n"
