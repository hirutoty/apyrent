#!/usr/bin/env pwsh
# Batch screenshot capture for APYRent documentation

$urls = @(
    @{url="http://127.0.0.1:8000/admin/pajak"; name="04-pajak-kendaraan.png"},
    @{url="http://127.0.0.1:8000/admin/asuransi-kendaraan"; name="05-asuransi-kendaraan.png"},
    @{url="http://127.0.0.1:8000/admin/gps-kendaraan"; name="06-gps-kendaraan.png"},
    @{url="http://127.0.0.1:8000/admin/kir"; name="07-kir.png"},
    @{url="http://127.0.0.1:8000/admin/laporan-kehilangan"; name="08-laporan-kehilangan.png"},
    @{url="http://127.0.0.1:8000/admin/pembayaran"; name="09-pembayaran.png"},
    @{url="http://127.0.0.1:8000/admin/members"; name="10-member.png"},
    @{url="http://127.0.0.1:8000/admin/member-rental"; name="11-booking-rental.png"},
    @{url="http://127.0.0.1:8000/admin/history"; name="12-rental-history.png"},
    @{url="http://127.0.0.1:8000/admin/service"; name="13-service.png"},
    @{url="http://127.0.0.1:8000/admin/data-leasing"; name="14-data-leasing.png"}
)

$count = 0
foreach ($item in $urls) {
    $count++
    Write-Host "[$count/$($urls.Count)] Capturing: $($item.name)"
    
    playwright-cli goto $item.url | Out-Null
    Start-Sleep -Seconds 1.5
    playwright-cli screenshot | Out-Null
    
    $latest = Get-ChildItem "$env:PLAYWRIGHT_MCP_OUTPUT_DIR\*.png" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
    Copy-Item $latest.FullName "C:\laragon\www\apyrent\docs\images\$($item.name)" -Force
    
    Write-Host "  ✓ Saved"
}

Write-Host "`n✅ Captured $count screenshots"
