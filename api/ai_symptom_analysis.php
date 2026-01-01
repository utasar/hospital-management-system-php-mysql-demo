<?php
include_once '../config/Database.php';
include_once '../class/AISymptomAnalyzer.php';
include_once '../class/User.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Check if user is logged in
if (!$user->loggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$aiAnalyzer = new AISymptomAnalyzer($db);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'analyze':
        if (empty($_POST['symptoms'])) {
            echo json_encode(['status' => 'error', 'message' => 'Symptoms are required']);
            exit();
        }
        
        // Sanitize and validate input
        $symptoms = is_array($_POST['symptoms']) ? $_POST['symptoms'] : $_POST['symptoms'];
        if (is_string($symptoms)) {
            $symptoms = htmlspecialchars(strip_tags($symptoms), ENT_QUOTES, 'UTF-8');
        }
        
        $patientId = $_SESSION['userid'];
        
        $result = $aiAnalyzer->analyzeSymptoms($symptoms, $patientId);
        echo json_encode($result);
        break;
    
    case 'getCommonSymptoms':
        $symptoms = $aiAnalyzer->getCommonSymptoms();
        echo json_encode(['status' => 'success', 'symptoms' => $symptoms]);
        break;
    
    case 'getHistory':
        $patientId = $_SESSION['userid'];
        $history = $aiAnalyzer->getPatientDiagnosisHistory($patientId);
        echo json_encode(['status' => 'success', 'history' => $history]);
        break;
    
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
