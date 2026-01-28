<?php
/**
 * AI Chat API Endpoint
 * Handles communication with Gemini API securely on server-side
 * Includes user's certificate descriptions in the AI context
 */

header('Content-Type: application/json');
session_start();

// Check if user is logged in
if (!isset($_SESSION['rollno'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include 'includes/db.php';

// Load API key from secrets
if (file_exists(__DIR__ . '/includes/secrets.php')) {
    require __DIR__ . '/includes/secrets.php';
}

$gemini_api_key = $GLOBALS['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?? '';

if (empty($gemini_api_key)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'AI service not configured']);
    exit;
}

// Get the request data
$data = json_decode(file_get_contents('php://input'), true);
$user_message = trim($data['message'] ?? '');
$conversation_history = $data['history'] ?? [];

if (empty($user_message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Message is required']);
    exit;
}

$rollno = $_SESSION['rollno'];

// Fetch user's certificates with descriptions
$certificates = [];
$stmt = $conn->prepare("SELECT certificate_name, description, upload_date FROM certificates WHERE rollno = ? AND is_deleted = FALSE ORDER BY upload_date DESC");
if ($stmt) {
    $stmt->bind_param("s", $rollno);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $certificates[] = [
            'name' => $row['certificate_name'],
            'description' => $row['description'] ?? '',
            'date' => date('M d, Y', strtotime($row['upload_date']))
        ];
    }
    $stmt->close();
}

// Build certificate context for the AI
$cert_context = "";
if (!empty($certificates)) {
    $cert_context = "\n\n--- USER'S CURRENT CERTIFICATES ---\n";
    foreach ($certificates as $index => $cert) {
        $cert_context .= ($index + 1) . ". " . $cert['name'];
        if (!empty($cert['description'])) {
            $cert_context .= "\n   Description: " . $cert['description'];
        }
        $cert_context .= "\n   Uploaded: " . $cert['date'] . "\n\n";
    }
    $cert_context .= "--- END OF CERTIFICATES ---\n\n";
} else {
    $cert_context = "\n\n[Note: This user has not uploaded any certificates yet.]\n\n";
}

// Build the system prompt with certificate context
$system_prompt = "You are an AI career assistant specialized in AI/ML certifications and career development for the \"Yaswanth's AI Certificate Management Hub\" platform.

Your role is to:
1. Analyze the user's current certifications and their descriptions
2. Provide personalized career guidance based on their existing skills
3. Recommend relevant FREE and PAID certifications they can pursue to enhance their career
4. Suggest learning paths and roadmaps
5. Give insights about industry trends in AI/ML

When recommending certifications, always:
- Categorize them as FREE or PAID
- Include the provider name (Google, Microsoft, AWS, Coursera, etc.)
- Provide estimated completion time if known
- Explain why this certification would benefit them based on their current skills
- Include official links when possible

Format your responses with clear headings, bullet points, and organized sections.
Be encouraging but realistic about career prospects.

" . $cert_context . "

Based on this information, help the user with their query. If they ask about certifications, tailor your recommendations to complement what they already have.";

// Build the conversation for Gemini API
$contents = [
    [
        "role" => "user",
        "parts" => [["text" => $system_prompt]]
    ],
    [
        "role" => "model",
        "parts" => [["text" => "Hello! I'm your AI career assistant. I've reviewed your certificate portfolio and I'm ready to help you with personalized career guidance, certification recommendations, and learning paths. How can I assist you today?"]]
    ]
];

// Add conversation history if provided
if (!empty($conversation_history)) {
    foreach ($conversation_history as $msg) {
        if (isset($msg['role']) && isset($msg['text'])) {
            $role = ($msg['role'] === 'user') ? 'user' : 'model';
            $contents[] = [
                "role" => $role,
                "parts" => [["text" => $msg['text']]]
            ];
        }
    }
}

// Add the current user message
$contents[] = [
    "role" => "user",
    "parts" => [["text" => $user_message]]
];

// Make API request to Gemini
$api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $gemini_api_key;

$request_body = [
    "contents" => $contents,
    "generationConfig" => [
        "temperature" => 0.7,
        "topP" => 0.95,
        "topK" => 64,
        "maxOutputTokens" => 2500,
        "responseMimeType" => "text/plain"
    ]
];

// Check if cURL is available
if (!function_exists('curl_init')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'cURL not available on this server. Using client-side fallback.', 'use_client_fallback' => true]);
    exit;
}

// Use cURL to make the API request
$ch = curl_init($api_url);
if (!$ch) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to initialize connection. Using client-side fallback.', 'use_client_fallback' => true]);
    exit;
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error || $response === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to connect to AI service. Server may block external API calls.', 'use_client_fallback' => true, 'error_detail' => $curl_error]);
    exit;
}

$response_data = json_decode($response, true);

if ($http_code !== 200) {
    http_response_code(500);
    $error_message = $response_data['error']['message'] ?? 'AI service error';
    echo json_encode(['success' => false, 'message' => $error_message]);
    exit;
}

// Extract the AI response
if (isset($response_data['candidates'][0]['content']['parts'][0]['text'])) {
    $ai_reply = $response_data['candidates'][0]['content']['parts'][0]['text'];
    echo json_encode([
        'success' => true,
        'reply' => $ai_reply,
        'certificates_count' => count($certificates)
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Invalid response from AI service']);
}

$conn->close();
?>
