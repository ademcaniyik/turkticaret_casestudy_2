<?php

header('Content-Type: text/plain');
echo "Test4.php çalışıyor\n";

// Mevcut dizindeki dosyaları listele
$files = scandir(__DIR__);
echo "\n=== Dizin İçeriği ===\n";
foreach ($files as $file) {
    echo $file . "\n";
}

// Dosya izinlerini kontrol et
if (function_exists('posix_getpwuid')) {
    $owner = posix_getpwuid(fileowner(__FILE__));
    echo "\n=== Dosya İzinleri ===\n";
    echo "Dosya Sahibi: " . $owner['name'] . "\n";
    echo "Dosya Grubu: " . posix_getgrgid(filegroup(__FILE__))['name'] . "\n";
    echo "Dosya İzinleri: " . substr(sprintf('%o', fileperms(__FILE__)), -4) . "\n";
}

// PHP versiyonunu göster
echo "\n=== PHP Bilgisi ===\n";
phpinfo();
