#Requires -Version 5.1
<#
  Antrean generate gambar properti via ComfyUI lokal.
  Pakai: powershell -NoProfile -ExecutionPolicy Bypass -File tools\comfy-queue.ps1 [-Limit 5] [-DryRun] [-Ckpt 'model.safetensors']
#>
param(
    [string]$ApiHost = 'http://127.0.0.1:8188',
    [string]$JobsFile = '',
    [int]$Limit = 0,
    [switch]$DryRun,
    [string]$Ckpt = '',
    [int]$WaitSec = 10,
    [int]$TimeoutMin = 25
)

$ErrorActionPreference = 'Stop'
if ($JobsFile -eq '') { $JobsFile = (Join-Path $PSScriptRoot 'comfy-jobs.json') }

function New-ComfyWorkflow {
    param([string]$ckptName, [string]$pos, [string]$neg, [string]$prefix)
    $w = [ordered]@{
        '1' = @{ class_type = 'CheckpointLoaderSimple'; inputs = @{ ckpt_name = $ckptName } }
        '2' = @{ class_type = 'EmptyLatentImage'; inputs = @{ width = 800; height = 600; batch_size = 1 } }
        '3' = @{ class_type = 'CLIPTextEncode'; inputs = @{ text = $pos; clip = @('1', 1) } }
        '4' = @{ class_type = 'CLIPTextEncode'; inputs = @{ text = $neg; clip = @('1', 1) } }
        '5' = @{ class_type = 'KSampler'; inputs = @{ model = @('1', 0); positive = @('3', 0); negative = @('4', 0); latent_image = @('2', 0); seed = (Get-Random -Minimum 1 -Maximum 999999999); steps = 28; cfg = 6.5; sampler_name = 'euler_ancestral'; scheduler = 'normal'; denoise = 1.0 } }
        '6' = @{ class_type = 'VAEDecode'; inputs = @{ samples = @('5', 0); vae = @('1', 2) } }
        '7' = @{ class_type = 'SaveImage'; inputs = @{ images = @('6', 0); filename_prefix = $prefix } }
    }
    return @{ prompt = $w }
}

try {
    Invoke-RestMethod -Uri ($ApiHost + '/system_stats') -TimeoutSec 10 | Out-Null
    Write-Output 'ComfyUI ONLINE'
} catch {
    Write-Output 'COMFYUI_OFFLINE: nyalakan dulu `python main.py --listen 127.0.0.1 --port 8188` di folder ComfyUI, lalu ulangi.'
    exit 2
}

if (-not (Test-Path $JobsFile)) { Write-Output "Jobs file tidak ada: $JobsFile"; exit 3 }
$jobs = Get-Content $JobsFile -Raw | ConvertFrom-Json
if ($Limit -gt 0) { $jobs = @($jobs | Select-Object -First $Limit) }

if ([string]::IsNullOrEmpty($Ckpt)) {
    $oi = Invoke-RestMethod -Uri ($ApiHost + '/object_info/CheckpointLoaderSimple') -TimeoutSec 20
    $names = @($oi.CheckpointLoaderSimple.input.required.ckpt_name[0])
    if ($names.Count -eq 0) { Write-Output 'Tidak ada checkpoint di ComfyUI.'; exit 4 }
    $Ckpt = $names[0]
    Write-Output ('Checkpoint otomatis: ' + $Ckpt)
}

$outDir = Join-Path (Split-Path $PSScriptRoot -Parent) 'storage\app\public\properties'
if ($DryRun) {
    Write-Output ('DRYRUN OK: ' + $jobs.Count + ' job siap, ckpt=' + $Ckpt)
    exit 0
}

$projRoot = Split-Path $PSScriptRoot -Parent
$n = 0
foreach ($job in $jobs) {
    $n++
    $prefix = 'rumahku_' + $job.property_id
    $wf = New-ComfyWorkflow -ckptName $Ckpt -pos $job.positive -neg $job.negative -prefix $prefix
    $body = ($wf | ConvertTo-Json -Depth 12)
    $res = Invoke-RestMethod -Uri ($ApiHost + '/prompt') -Method Post -Body $body -ContentType 'application/json'
    $pid_ = $res.prompt_id
    Write-Output ("[$n/$($jobs.Count)] property $($job.property_id) -> $pid_")
    $deadline = (Get-Date).AddMinutes($TimeoutMin)
    $file = $null
    while ((Get-Date) -lt $deadline) {
        Start-Sleep -Seconds $WaitSec
        try { $hist = Invoke-RestMethod -Uri ($ApiHost + '/history/' + $pid_) -TimeoutSec 15 } catch { continue }
        $entry = $hist.$pid_
        if ($null -ne $entry -and $null -ne $entry.outputs.'7' -and $null -ne $entry.outputs.'7'.images) {
            $file = $entry.outputs.'7'.images[0]
            break
        }
        if ($null -ne $entry -and $entry.status.completed) { break }
    }
    if ($null -eq $file) { Write-Output ('  GAGAL/TIMEOUT property ' + $job.property_id); continue }
    $qs = 'filename=' + $file.filename + '&subfolder=' + $file.subfolder + '&type=' + $file.type
    $tmp = Join-Path $env:TEMP ('rumahku_' + $job.property_id + '.png')
    Invoke-WebRequest -Uri ($ApiHost + '/view?' + $qs) -OutFile $tmp -UseBasicParsing
    $dst = Join-Path $outDir $job.file
    & php (Join-Path $projRoot 'tools\png2jpg.php') $tmp $dst
    Remove-Item $tmp -Force
    Write-Output ('  tersimpan: ' + $job.file)
}
Write-Output 'SELESAI'
