<?php
// We still give PHP memory just in case!
ini_set('memory_limit', '2048M');

$user_message = $_POST['message'] ?? '';
if (empty($user_message)) {
    echo "Please ask a question.";
    exit;
}

// 🔴 1. PASTE YOUR API KEY HERE
$api_key = "dito lalagay yung apis key ng google";

$pdf_path = __DIR__ . '/uploads/ksayLabo.pdf';
$uri_file = __DIR__ . '/uploads/book_uri.txt';

if (!file_exists($pdf_path)) {
    echo "System Error: I cannot find the book file!";
    exit;
}

$file_uri = "";

// =========================================================
// 🚀 PHASE 1: THE UPLOAD MANAGER (Only runs once a day!)
// =========================================================
// Check if we already uploaded the book today (less than 24 hours ago)
if (file_exists($uri_file) && (time() - filemtime($uri_file) < 86400)) {
    // Just grab the secret link we saved earlier!
    $file_uri = trim(file_get_contents($uri_file));
} else {
    // We need to upload the giant book to Google's File Server
    $upload_url = "https://generativelanguage.googleapis.com/upload/v1beta/files?uploadType=media&key=" . $api_key;
    
    $ch_up = curl_init($upload_url);
    curl_setopt($ch_up, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_up, CURLOPT_POST, true);
    curl_setopt($ch_up, CURLOPT_POSTFIELDS, file_get_contents($pdf_path));
    curl_setopt($ch_up, CURLOPT_HTTPHEADER, ['Content-Type: application/pdf']);
    
    $upload_response = curl_exec($ch_up);
    curl_close($ch_up);
    
    $upload_data = json_decode($upload_response, true);
    
    if (isset($upload_data['file']['uri'])) {
        $file_uri = $upload_data['file']['uri'];
        // Save the secret link so we don't have to upload again today!
        file_put_contents($uri_file, $file_uri);
    } else {
        echo "ERROR: Failed to upload the massive book to Google. " . $upload_response;
        exit;
    }
}

// =========================================================
// 🧠 PHASE 2: THE CHAT BRAIN (Runs lightning fast)
// =========================================================
$chat_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=" . $api_key;

$system_instruction = "You are a helpful virtual tour guide for the Museo de Labo. You have been provided with a PDF book about the history of Labo. STRICTLY base your answers on the information found inside this attached document. Keep answers brief, engaging, and 2-3 sentences max. Question: ";

// We use "fileData" now instead of "inlineData", pointing to the secret link!
$data = [
    "contents" => [
        [
            "parts" => [
                [ "text" => $system_instruction . $user_message ],
                [
                    "fileData" => [
                        "mimeType" => "application/pdf",
                        "fileUri" => $file_uri
                    ]
                ]
            ]
        ]
    ]
];

$ch = curl_init($chat_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    echo $result['candidates'][0]['content']['parts'][0]['text'];
} else {
    echo "GOOGLE ERROR: " . $response;
}
?>