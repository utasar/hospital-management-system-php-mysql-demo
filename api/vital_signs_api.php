<?php
include_once '../config/Database.php';
include_once '../class/User.php';
include_once '../class/VitalSigns.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Check if user is logged in
if (!$user->loggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$vitalSigns = new VitalSigns($db);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'save':
        $patientId = $_SESSION['userid'];
        $data = [
            'temperature' => $_POST['temperature'] ?? null,
            'bp_systolic' => $_POST['bp_systolic'] ?? null,
            'bp_diastolic' => $_POST['bp_diastolic'] ?? null,
            'heart_rate' => $_POST['heart_rate'] ?? null,
            'respiratory_rate' => $_POST['respiratory_rate'] ?? null,
            'oxygen_saturation' => $_POST['oxygen_saturation'] ?? null,
            'weight' => $_POST['weight'] ?? null,
            'height' => $_POST['height'] ?? null,
            'bmi' => $_POST['bmi'] ?? null,
            'notes' => $_POST['notes'] ?? ''
        ];
        
        // Calculate BMI if not provided
        if (!$data['bmi'] && $data['weight'] && $data['height']) {
            $data['bmi'] = $vitalSigns->calculateBMI($data['weight'], $data['height']);
        }
        
        $result = $vitalSigns->addVitalSigns($patientId, $data);
        echo json_encode($result);
        break;
    
    case 'latest':
        $patientId = $_SESSION['userid'];
        $latest = $vitalSigns->getLatestVitalSigns($patientId);
        
        if ($latest) {
            $alerts = $vitalSigns->analyzeVitalSigns($latest);
            echo json_encode([
                'status' => 'success',
                'data' => $latest,
                'alerts' => $alerts
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'data' => null,
                'alerts' => []
            ]);
        }
        break;
    
    case 'history':
        $patientId = $_SESSION['userid'];
        $limit = $_GET['limit'] ?? 10;
        $history = $vitalSigns->getVitalSignsHistory($patientId, $limit);
        
        echo json_encode([
            'status' => 'success',
            'data' => $history
        ]);
        break;
    
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
