<?php
// Place this file in your Laravel public/ folder
// Visit: http://localhost/hrms/public/check_gd.php
// (or wherever your public folder is served)

echo "<h2>PHP Info Check</h2>";
echo "<p><strong>php.ini location:</strong> " . php_ini_loaded_file() . "</p>";
echo "<p><strong>GD loaded:</strong> " . (extension_loaded('gd') ? '✅ YES' : '❌ NO') . "</p>";
echo "<p><strong>imagecreatefrompng exists:</strong> " . (function_exists('imagecreatefrompng') ? '✅ YES' : '❌ NO') . "</p>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";

if (extension_loaded('gd')) {
    $info = gd_info();
    echo "<p><strong>GD Version:</strong> " . $info['GD Version'] . "</p>";
    echo "<p><strong>PNG Support:</strong> " . ($info['PNG Support'] ? '✅ YES' : '❌ NO') . "</p>";
}

echo "<hr>";
echo "<p><strong>All loaded extensions:</strong><br>";
echo implode(', ', get_loaded_extensions());
echo "</p>";
