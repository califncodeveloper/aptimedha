Add-Type -AssemblyName System.IO.Compression.FileSystem

$zip = [System.IO.Compression.ZipFile]::OpenRead('client_docs/Placement_All_Topics_2_Sample_MCQs.xlsx')

function Get-SharedStrings($zip) {
    $entry = $zip.GetEntry('xl/sharedStrings.xml')
    if (!$entry) { return @() }
    $reader = New-Object System.IO.StreamReader($entry.Open())
    $xml = $reader.ReadToEnd()
    $reader.Close()
    $doc = [xml]$xml
    $ns = New-Object System.Xml.XmlNamespaceManager -ArgumentList $doc.NameTable
    $ns.AddNamespace("d", "http://schemas.openxmlformats.org/spreadsheetml/2006/main")
    $list = New-Object System.Collections.Generic.List[string]
    foreach ($si in $doc.SelectNodes("//d:si", $ns)) {
        $txtNodes = $si.SelectNodes(".//d:t", $ns)
        $s = ""
        foreach ($t in $txtNodes) { $s += $t.InnerText }
        $list.Add($s)
    }
    return $list
}

$strings = Get-SharedStrings $zip
Write-Host "Total shared strings: $($strings.Count)"

# Let's inspect the headers and first rows of sheet 12 (Image References) or sheet 2 (Data Interpretation)
function Dump-Sheet($zip, $sheetName, $strings) {
    $entry = $zip.GetEntry($sheetName)
    if (!$entry) { return }
    $reader = New-Object System.IO.StreamReader($entry.Open())
    $xml = $reader.ReadToEnd()
    $reader.Close()
    $doc = [xml]$xml
    $ns = New-Object System.Xml.XmlNamespaceManager -ArgumentList $doc.NameTable
    $ns.AddNamespace("d", "http://schemas.openxmlformats.org/spreadsheetml/2006/main")
    
    $rows = $doc.SelectNodes("//d:row", $ns)
    Write-Host "=== $sheetName (Total rows: $($rows.Count)) ==="
    foreach ($row in ($rows | Select-Object -First 10)) {
        $cells = $row.SelectNodes("d:c", $ns)
        $line = @()
        foreach ($c in $cells) {
            $t = $c.GetAttribute("t")
            $v = $c.SelectSingleNode("d:v", $ns)
            $val = if ($v) { $v.InnerText } else { "" }
            if ($t -eq "s" -and $val -ne "") {
                $idx = [int]$val
                if ($idx -lt $strings.Count) { $val = $strings[$idx] }
            }
            $line += "$($c.GetAttribute('r')): $val"
        }
        Write-Host ($line -join " | ")
    }
}

# sheet 2 is Data Interpretation (rId2 is sheet2.xml or sheet3.xml, let's check sheet12.xml for Image References)
Dump-Sheet $zip 'xl/worksheets/sheet12.xml' $strings
Dump-Sheet $zip 'xl/worksheets/sheet13.xml' $strings
Dump-Sheet $zip 'xl/worksheets/sheet2.xml' $strings
Dump-Sheet $zip 'xl/worksheets/sheet6.xml' $strings

$zip.Dispose()

