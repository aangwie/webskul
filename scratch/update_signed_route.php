<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$count = 0;
foreach($files as $file) {
    if (is_array($file)) {
        $path = $file[0];
        $content = file_get_contents($path);
        if (strpos($content, "route('public.storage.view'") !== false) {
            $content = str_replace("route('public.storage.view'", "URL::signedRoute('public.storage.view'", $content);
            file_put_contents($path, $content);
            echo "Updated: $path\n";
            $count++;
        }
    }
}
echo "Total templates updated: $count\n";
