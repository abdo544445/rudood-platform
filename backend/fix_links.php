<?php

$dir = __DIR__ . '/resources/views';
$files = glob($dir . '/*.blade.php');

foreach ($files as $file) {
    if (basename($file) === 'app.blade.php' || basename($file) === 'dashboard.blade.php') {
        // We already fixed these manually
        continue;
    }

    $content = file_get_contents($file);
    
    // Fix page links (e.g. dash.html -> url('/dashboard') or index.html -> url('/index'))
    $content = preg_replace_callback('/href="([^"]+)\.html"/', function($matches) {
        $name = $matches[1];
        if ($name === 'dash') $name = 'dashboard';
        return 'href="{{ url(\'/' . $name . '\') }}"';
    }, $content);

    // Fix CSS links
    $content = preg_replace('/href="css\//', 'href="{{ asset(\'css/', $content);
    $content = preg_replace('/(\{\{ asset\(\'css\/[^\'"]+)(\")/', '$1\') }}"', $content);

    // Fix image src
    $content = preg_replace('/src="images\//', 'src="{{ asset(\'images/', $content);
    $content = preg_replace('/(\{\{ asset\(\'images\/[^\'"]+)(\")/', '$1\') }}"', $content);

    file_put_contents($file, $content);
}
echo "All Blade templates fixed!\n";
