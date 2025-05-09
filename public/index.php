<?php
header('Content-Type: text/html; charset=utf-8');

// Check if this is a direct PHP file access
if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
    http_response_code(403);
    echo "Access denied";
    exit;
}

// Serve the React app
readfile(__DIR__ . '/index.html');
?>