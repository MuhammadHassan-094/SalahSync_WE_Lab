<?php
// Set headers for CORS and JSON response
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight requests for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Response structure
$response = [
    'code' => 500,
    'message' => 'Internal Server Error',
    'data' => null
];

// Get action parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($action) {
        case 'ip_location':
            // Get user's IP address
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            if ($ipAddress == '::1' || $ipAddress == '127.0.0.1') {
                // Default location for localhost (New York City coordinates)
                $response = [
                    'code' => 200,
                    'message' => 'Default location used for localhost',
                    'data' => null,
                    'latitude' => 40.7128,
                    'longitude' => -74.0060
                ];
            } else {
                // Use the IP geolocation API
                $geoApiUrl = "http://ip-api.com/json/{$ipAddress}?fields=status,lat,lon,timezone,city,country";
                $geoData = json_decode(file_get_contents($geoApiUrl), true);
                
                if ($geoData && $geoData['status'] === 'success') {
                    $response = [
                        'code' => 200,
                        'message' => 'Location determined by IP',
                        'data' => null,
                        'latitude' => $geoData['lat'],
                        'longitude' => $geoData['lon'],
                        'timezone' => $geoData['timezone'],
                        'city' => $geoData['city'],
                        'country' => $geoData['country']
                    ];
                } else {
                    throw new Exception('Failed to determine location from IP');
                }
            }
            break;
            
        case 'quran_verse':
            // Cache the Quran verse for a day to avoid hitting external API too often
            $cacheFile = '../cache/daily_verse.json';
            $cacheDir = '../cache';
            
            // Create cache directory if it doesn't exist
            if (!file_exists($cacheDir)) {
                mkdir($cacheDir, 0777, true);
            }
            
            // Check if we have a cached verse that's less than a day old
            $useCache = false;
            if (file_exists($cacheFile)) {
                $cacheData = json_decode(file_get_contents($cacheFile), true);
                if ($cacheData && isset($cacheData['timestamp'])) {
                    $cacheAge = time() - $cacheData['timestamp'];
                    if ($cacheAge < 86400) { // Less than a day old
                        $useCache = true;
                        $response = [
                            'code' => 200,
                            'message' => 'Daily verse retrieved from cache',
                            'data' => $cacheData['verse']
                        ];
                    }
                }
            }
            
            if (!$useCache) {
                // Fetch a new verse from the API
                $apiUrl = 'https://api.alquran.cloud/v1/ayah/random';
                $apiResponse = file_get_contents($apiUrl);
                $verseData = json_decode($apiResponse, true);
                
                if (isset($verseData['data'])) {
                    // Also fetch English translation
                    $verseNumber = $verseData['data']['number'];
                    $translationUrl = "https://api.alquran.cloud/v1/ayah/{$verseNumber}/en.asad";
                    $translationResponse = file_get_contents($translationUrl);
                    $translationData = json_decode($translationResponse, true);
                    
                    $verse = [
                        'text' => $verseData['data']['text'],
                        'surah' => $verseData['data']['surah']['name'],
                        'translation' => isset($translationData['data']['text']) ? $translationData['data']['text'] : null,
                        'reference' => "Surah {$verseData['data']['surah']['englishName']} ({$verseData['data']['surah']['number']}), Verse {$verseData['data']['numberInSurah']}"
                    ];
                    
                    // Cache the verse
                    file_put_contents($cacheFile, json_encode([
                        'timestamp' => time(),
                        'verse' => $verse
                    ]));
                    
                    $response = [
                        'code' => 200,
                        'message' => 'Daily verse retrieved successfully',
                        'data' => $verse
                    ];
                } else {
                    throw new Exception('Failed to retrieve Quran verse from API');
                }
            }
            break;
            
        default:
            $response = [
                'code' => 400,
                'message' => 'Invalid action parameter',
                'data' => null
            ];
    }
} catch (Exception $e) {
    $response = [
        'code' => 500,
        'message' => 'Server error: ' . $e->getMessage(),
        'data' => null
    ];
}

// Send JSON response
echo json_encode($response); 