<?php
include_once '../config/Database.php';
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

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'search':
        $searchTerm = $_GET['query'] ?? '';
        $medications = searchMedications($db, $searchTerm);
        echo json_encode(['status' => 'success', 'medications' => $medications]);
        break;
    
    case 'getById':
        $id = $_GET['id'] ?? 0;
        $medication = getMedicationById($db, $id);
        if ($medication) {
            echo json_encode(['status' => 'success', 'medication' => $medication]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Medication not found']);
        }
        break;
    
    case 'suggestBySymptoms':
        $symptoms = $_GET['symptoms'] ?? '';
        $suggestions = suggestMedicationsBySymptoms($db, $symptoms);
        echo json_encode(['status' => 'success', 'suggestions' => $suggestions]);
        break;
    
    case 'checkInteractions':
        $medicationIds = $_POST['medication_ids'] ?? [];
        $interactions = checkDrugInteractions($db, $medicationIds);
        echo json_encode(['status' => 'success', 'interactions' => $interactions]);
        break;
    
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

function searchMedications($db, $searchTerm) {
    $stmt = $db->prepare("
        SELECT * FROM hms_medications 
        WHERE name LIKE ? OR generic_name LIKE ? OR category LIKE ?
        ORDER BY name
        LIMIT 20
    ");
    
    $search = "%".$searchTerm."%";
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $medications = [];
    while ($row = $result->fetch_assoc()) {
        $medications[] = $row;
    }
    
    $stmt->close();
    return $medications;
}

function getMedicationById($db, $id) {
    $stmt = $db->prepare("SELECT * FROM hms_medications WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $medication = $result->fetch_assoc();
    $stmt->close();
    
    return $medication;
}

function suggestMedicationsBySymptoms($db, $symptoms) {
    // Simple symptom-to-medication mapping
    $symptomMedicationMap = [
        'pain' => ['Paracetamol', 'Ibuprofen'],
        'headache' => ['Paracetamol', 'Ibuprofen', 'Aspirin'],
        'fever' => ['Paracetamol', 'Ibuprofen'],
        'heartburn' => ['Omeprazole'],
        'acid reflux' => ['Omeprazole'],
        'high blood pressure' => ['Lisinopril'],
        'diabetes' => ['Metformin'],
    ];
    
    $suggestions = [];
    $symptomsLower = strtolower($symptoms);
    
    foreach ($symptomMedicationMap as $symptom => $meds) {
        if (stripos($symptomsLower, $symptom) !== false) {
            foreach ($meds as $medName) {
                $stmt = $db->prepare("
                    SELECT * FROM hms_medications 
                    WHERE name = ? OR generic_name = ?
                    LIMIT 1
                ");
                $stmt->bind_param("ss", $medName, $medName);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($med = $result->fetch_assoc()) {
                    $suggestions[] = [
                        'medication' => $med,
                        'reason' => 'Commonly used for ' . $symptom,
                        'warning' => '⚠️ This is a suggestion only. Consult a doctor before taking any medication.'
                    ];
                }
                $stmt->close();
            }
        }
    }
    
    // Remove duplicates
    $unique = [];
    $seen = [];
    foreach ($suggestions as $suggestion) {
        $id = $suggestion['medication']['id'];
        if (!in_array($id, $seen)) {
            $seen[] = $id;
            $unique[] = $suggestion;
        }
    }
    
    return $unique;
}

function checkDrugInteractions($db, $medicationIds) {
    // This is a simplified version. In production, you would use a comprehensive drug interaction database
    $interactions = [];
    
    // Example: Check for common interactions
    $knownInteractions = [
        ['Aspirin', 'Ibuprofen'] => 'May increase risk of bleeding',
        ['Lisinopril', 'Aspirin'] => 'May reduce effectiveness of blood pressure medication'
    ];
    
    // Get medication names
    $medications = [];
    foreach ($medicationIds as $id) {
        $med = getMedicationById($db, $id);
        if ($med) {
            $medications[] = $med['name'];
        }
    }
    
    // Check for interactions
    if (count($medications) >= 2) {
        foreach ($knownInteractions as $pair => $warning) {
            $pairArray = explode(', ', str_replace(['[', ']', "'"], '', $pair));
            if (count(array_intersect($pairArray, $medications)) === 2) {
                $interactions[] = [
                    'medications' => $pairArray,
                    'warning' => $warning,
                    'severity' => 'moderate'
                ];
            }
        }
    }
    
    if (empty($interactions)) {
        return [
            'found' => false,
            'message' => 'No known interactions found. However, always consult your healthcare provider.'
        ];
    }
    
    return [
        'found' => true,
        'interactions' => $interactions,
        'message' => 'Potential drug interactions detected. Consult your doctor.'
    ];
}
?>
