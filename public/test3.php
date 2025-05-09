<?php

header('Content-Type: text/plain');
echo "Test3.php çalışıyor\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Current Directory: " . getcwd() . "\n";

// Show all PHP configuration
phpinfo();
