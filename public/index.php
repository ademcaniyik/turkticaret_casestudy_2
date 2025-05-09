<?php
header('Content-Type: text/html; charset=utf-8');

// Serve the React app
readfile(__DIR__ . '/index.html');
?>