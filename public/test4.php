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
$permissions = fileperms(__FILE__);
$owner = fileowner(__FILE__);
$group = filegroup(__FILE__);

echo "\n=== Dosya İzinleri ===\n";
echo "Dosya Sahibi: " . $owner . "\n";
echo "Dosya Grubu: " . $group . "\n";
echo "Dosya İzinleri: " . substr(sprintf('%o', $permissions), -4) . "\n";

// PHP versiyonunu göster
echo "\n=== PHP Bilgisi ===\n";
phpinfo();
