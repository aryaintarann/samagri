$path = ".env"
$content = Get-Content $path
$found = $false
$newContent = $content | ForEach-Object {
    if ($_ -match "^DB_DATABASE=") {
        $found = $true
        "DB_DATABASE=e:/Coding/samagri/database/samagri.sqlite"
    } else {
        $_
    }
}
if (-not $found) {
    $newContent += "DB_DATABASE=e:/Coding/samagri/database/samagri.sqlite"
}
$newContent | Set-Content $path
