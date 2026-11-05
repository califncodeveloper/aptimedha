Add-Type -AssemblyName System.IO.Compression.FileSystem

$zip = [System.IO.Compression.ZipFile]::OpenRead('client_docs/Placement_All_Topics_2_Sample_MCQs.xlsx')
Write-Host "Total entries in sample excel: $($zip.Entries.Count)"
foreach ($entry in $zip.Entries) {
    if ($entry.FullName -match 'sheet|media|drawings') {
        Write-Host "$($entry.FullName) ($($entry.Length) bytes)"
    }
}
$zip.Dispose()

