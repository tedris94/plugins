<?php
// Simple SMTP Test Script
// Place this file in your WordPress root directory and access via browser

echo "<h2>SMTP Connection Test</h2>";

// Test 1: Check if fsockopen is available
echo "<h3>1. Checking fsockopen availability:</h3>";
if (function_exists('fsockopen')) {
    echo "✓ fsockopen is available<br>";
} else {
    echo "✗ fsockopen is not available<br>";
}

// Test 2: Check if stream_socket_client is available
echo "<h3>2. Checking stream_socket_client availability:</h3>";
if (function_exists('stream_socket_client')) {
    echo "✓ stream_socket_client is available<br>";
} else {
    echo "✗ stream_socket_client is not available<br>";
}

// Test 3: Test basic connection to Brevo SMTP
echo "<h3>3. Testing connection to smtp-relay.brevo.com:</h3>";
$host = 'smtp-relay.brevo.com';
$ports = [587, 465, 25];

foreach ($ports as $port) {
    echo "Testing port $port: ";
    $connection = @fsockopen($host, $port, $errno, $errstr, 10);
    if ($connection) {
        echo "✓ Connected successfully<br>";
        fclose($connection);
    } else {
        echo "✗ Failed to connect (Error: $errno - $errstr)<br>";
    }
}

// Test 4: Check PHP extensions
echo "<h3>4. Checking required PHP extensions:</h3>";
$required_extensions = ['openssl', 'mbstring', 'iconv'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ $ext extension is loaded<br>";
    } else {
        echo "✗ $ext extension is not loaded<br>";
    }
}

// Test 5: Check allow_url_fopen
echo "<h3>5. Checking allow_url_fopen:</h3>";
if (ini_get('allow_url_fopen')) {
    echo "✓ allow_url_fopen is enabled<br>";
} else {
    echo "✗ allow_url_fopen is disabled<br>";
}

// Test 6: Check PHP version
echo "<h3>6. PHP Version:</h3>";
echo "Current PHP version: " . phpversion() . "<br>";

// Test 7: Check if we can resolve the hostname
echo "<h3>7. DNS Resolution Test:</h3>";
$ip = gethostbyname($host);
if ($ip != $host) {
    echo "✓ DNS resolution successful: $host resolves to $ip<br>";
} else {
    echo "✗ DNS resolution failed for $host<br>";
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>If connection tests fail, check your firewall/antivirus settings</li>";
echo "<li>If DNS resolution fails, check your internet connection</li>";
echo "<li>If extensions are missing, contact your hosting provider</li>";
echo "</ul>";
?> 