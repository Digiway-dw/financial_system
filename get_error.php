<?php
$ch = curl_init('http://127.0.0.1:8000/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// Need to login so try to get the CSRF token and login first
$html = curl_exec($ch);

if (strpos($html, 'form') === false && strpos($html, 'Exception') !== false) {
    echo "Got exception immediately:\n";
    if (preg_match('/<div class="exception-message">\s*<strong>(.*?)<\/strong>/is', $html, $m)) {
        echo trim(strip_tags($m[1])) . "\n";
    }
}

// But since the dashboard uses auth to set data for each role, we must authenticate
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

// Boot the application first so DB is set up!
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$user = \App\Domain\Entities\User::where('email', 'admin@financial.system')->first();
\Illuminate\Support\Facades\Auth::setUser($user);

// Execute request through kernel
$request = \Illuminate\Http\Request::create('/dashboard', 'GET');
$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
$content = $response->getContent();

if ($response->getStatusCode() >= 400) {
    if (preg_match('/<title>(.*?)<\/title>/is', $content, $m)) {
        echo "Title: " . trim($m[1]) . "\n";
    }
    
    // Look for exact PHP error message
    if (preg_match('/"exception_message":"([^"]+)"/', $content, $m)) {
        echo "Exception: " . $m[1] . "\n";
    } else {
        // Try other common patterns
        preg_match_all('/<span class="exception_message">([^<]+)<\/span>/i', $content, $m) || 
        preg_match_all('/<h1[^>]*>([^<]+)<\/h1>/i', $content, $m);
        
        if (!empty($m[1])) {
            echo "Error text: " . implode(" - ", array_slice($m[1], 0, 2)) . "\n";
        }
    }
    
    // File info
    if (preg_match('/div class="exception_file">\s*in\s*<span title="([^"]+)">/i', $content, $m)) {
        echo "In file: " . $m[1] . "\n";
    } elseif (preg_match('/<div class="exception_file">\s*at\s*<[^\/]+>\s*([^<]+)/i', $content, $m)) {
        echo "At file: " . trim($m[1]) . "\n";
    }
    
    // Give us more text if needed
    if (strpos($content, 'Livewire') !== false) {
        echo "[Note: Livewire component error detected]\n";
    }
}
