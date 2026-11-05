Add-Type -AssemblyName System.IO.Compression.FileSystem

$zip = [System.IO.Compression.ZipFile]::OpenRead('client_docs/Placement_All_Topics_2_Sample_MCQs.xlsx')

# Read workbook.xml to get sheet names
$wbEntry = $zip.GetEntry('xl/workbook.xml')
$reader = New-Object System.IO.StreamReader($wbEntry.Open())
$wbXml = $reader.ReadToEnd()
$reader.Close()

$wbDoc = [xml]$wbXml
$ns = New-Object System.Xml.XmlNamespaceManager -ArgumentList $wbDoc.NameTable
$ns.AddNamespace("d", "http://schemas.openxmlformats.org/spreadsheetml/2006/main")

$sheets = $wbDoc.SelectNodes("//d:sheets/d:sheet", $ns)
foreach ($s in $sheets) {
    Write-Host "Sheet: $($s.name) (id: $($s.sheetId), r:id: $($s.GetAttribute('id', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships')))"
}

$zip.Dispose()

