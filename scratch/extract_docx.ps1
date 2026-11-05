Add-Type -AssemblyName System.IO.Compression.FileSystem

function Read-DocxText($path) {
    $zip = [System.IO.Compression.ZipFile]::OpenRead($path)
    $entry = $zip.GetEntry('word/document.xml')
    $stream = $entry.Open()
    $reader = New-Object System.IO.StreamReader($stream)
    $xml = $reader.ReadToEnd()
    $reader.Close()
    $stream.Close()
    $zip.Dispose()
    
    # Strip XML tags with regex
    $text = [System.Text.RegularExpressions.Regex]::Replace($xml, '<[^>]+>', ' ')
    $text = [System.Text.RegularExpressions.Regex]::Replace($text, '\s+', ' ')
    return $text
}

$files = @(
    'client_docs/AptiMedha_Website_SRS_Reviewed.docx',
    'Aptimedha MCQ Website SRS final.docx',
    'client_docs/Placement Preparation  Topics.docx',
    'client_docs/AptiMedha_Placement_Preparation_Roadmap.docx'
)

foreach ($f in $files) {
    if (Test-Path $f) {
        Write-Host "================== FILE: $f =================="
        $txt = Read-DocxText $f
        Write-Host "Length: $($txt.Length)"
        $matches = [regex]::Matches($txt, '.{0,100}(?:image|upload|pdf|current affairs|placement|answer image|diagram|photo).{0,150}', 'IgnoreCase')
        foreach ($m in $matches | Select-Object -First 30) {
            Write-Host "--- MATCH ---"
            Write-Host $m.Value
        }
    }
}

