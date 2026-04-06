<?php
$dir = "app";
$fixes = [];
$fixes["\xC3\x83\xC2\xB3"] = "\xC3\xB3";
$fixes["\xC3\x83\xC2\xA9"] = "\xC3\xA9";
$fixes["\xC3\x83\xC2\xA1"] = "\xC3\xA1";
$fixes["\xC3\x83\xC2\xAD"] = "\xC3\xAD";
$fixes["\xC3\x83\xC2\xBA"] = "\xC3\xBA";
$fixes["\xC3\x83\xC2\xB1"] = "\xC3\xB1";
$fixes["\xC3\x83\xC2\xBC"] = "\xC3\xBC";
$fixes["\xC3\x83\xE2\x80\x9C"] = "\xC3\x93";
$fixes["\xC3\x83\xC2\x93"] = "\xC3\x93";
$fixes["\xC3\x83\xC2\x89"] = "\xC3\x89";
$fixes["\xC3\x83\xC2\x81"] = "\xC3\x81";
$fixes["\xC3\x83\xC2\x8D"] = "\xC3\x8D";
$fixes["\xC3\x83\xC2\x9A"] = "\xC3\x9A";
$fixes["\xC3\x83\xC2\x91"] = "\xC3\x91";
$fixes["\xC3\x82\xC2\xBF"] = "\xC2\xBF";
$fixes["\xC3\x82\xC2\xA1"] = "\xC2\xA1";
$fixes["\xC3\x82\xC2\xB7"] = "\xC2\xB7";
$fixes["\xC3\x82\xC2\xB0"] = "\xC2\xB0";
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$count = 0;
foreach ($files as $file) {
    if (!($file instanceof SplFileInfo)) continue;
    if ($file->getExtension() !== 'php') continue;
    if (strpos($file->getPathname(), 'Libraries') !== false) continue;
    $content = file_get_contents($file->getPathname());
    $new = str_replace(array_keys($fixes), array_values($fixes), $content);
    if ($new !== $content) {
        file_put_contents($file->getPathname(), $new);
        echo "Fixed: " . $file->getFilename() . "\n";
        $count++;
    }
}
echo "Total: $count files fixed\n";
