<?php
include_once '../config/Database.php';
include_once '../class/DoctorRecommendation.php';
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

$doctorRec = new DoctorRecommendation($db);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'recommend':
        $specialization = $_POST['specialization'] ?? $_GET['specialization'] ?? null;
        $location = $_POST['location'] ?? $_GET['location'] ?? null;
        $maxDistance = $_POST['max_distance'] ?? $_GET['max_distance'] ?? 50;
        
        $result = $doctorRec->recommendDoctors($specialization, $location, $maxDistance);
        echo json_encode($result);
        break;
    
    case 'searchBySpecialization':
        if (empty($_POST['specialization']) && empty($_GET['specialization'])) {
            echo json_encode(['status' => 'error', 'message' => 'Specialization is required']);
            exit();
        }
        
        $specialization = $_POST['specialization'] ?? $_GET['specialization'];
        $doctors = $doctorRec->getDoctorsBySpecialization($specialization);
        echo json_encode(['status' => 'success', 'doctors' => $doctors]);
        break;
    
    case 'search':
        $criteria = [
            'name' => $_POST['name'] ?? $_GET['name'] ?? '',
            'specialization' => $_POST['specialization'] ?? $_GET['specialization'] ?? '',
            'max_fee' => $_POST['max_fee'] ?? $_GET['max_fee'] ?? '',
            'location' => $_POST['location'] ?? $_GET['location'] ?? ''
        ];
        
        $doctors = $doctorRec->searchDoctors($criteria);
        echo json_encode(['status' => 'success', 'doctors' => $doctors]);
        break;
    
    case 'getSpecializations':
        $specializations = $doctorRec->getSpecializations();
        echo json_encode(['status' => 'success', 'specializations' => $specializations]);
        break;
    
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
