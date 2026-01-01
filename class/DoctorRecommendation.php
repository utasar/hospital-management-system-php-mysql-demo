<?php
class DoctorRecommendation {
    
    private $doctorTable = 'hms_doctor';
    private $conn;
    
    public function __construct($db){
        $this->conn = $db;
    }
    
    /**
     * Recommend doctors based on location, specialization, and availability
     */
    public function recommendDoctors($specialization = null, $patientLocation = null, $maxDistance = 50) {
        $sqlQuery = "SELECT *, 
                     (CASE WHEN location IS NOT NULL AND location != '' THEN 0 ELSE 1 END) as location_priority
                     FROM ".$this->doctorTable."
                     WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($specialization) {
            $sqlQuery .= " AND specialization LIKE ?";
            $params[] = "%".$specialization."%";
            $types .= 's';
        }
        
        $sqlQuery .= " ORDER BY location_priority, fee ASC";
        
        $stmt = $this->conn->prepare($sqlQuery);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $doctors = [];
        while ($doctor = $result->fetch_assoc()) {
            $doctor['availability_status'] = $this->checkAvailability($doctor['id']);
            $doctors[] = $doctor;
        }
        
        $stmt->close();
        
        return [
            'status' => 'success',
            'doctors' => $doctors,
            'count' => count($doctors)
        ];
    }
    
    /**
     * Check doctor availability (simplified - can be enhanced with real scheduling)
     */
    private function checkAvailability($doctorId) {
        // Simple availability check - can be enhanced with real appointment scheduling
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as appointment_count 
            FROM hms_appointments 
            WHERE doctor_id = ? 
            AND appointment_date >= CURDATE()
            AND status != 'Cancelled'
        ");
        
        if ($stmt) {
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            if ($row['appointment_count'] < 5) {
                return 'Available';
            } elseif ($row['appointment_count'] < 10) {
                return 'Limited Availability';
            } else {
                return 'Busy';
            }
        }
        
        return 'Unknown';
    }
    
    /**
     * Get doctors by specialization with detailed info
     */
    public function getDoctorsBySpecialization($specialization) {
        $stmt = $this->conn->prepare("
            SELECT d.*, 
                   COUNT(a.id) as total_appointments,
                   AVG(CASE WHEN a.status = 'Completed' THEN 1 ELSE 0 END) as completion_rate
            FROM ".$this->doctorTable." d
            LEFT JOIN hms_appointments a ON d.id = a.doctor_id
            WHERE d.specialization LIKE ?
            GROUP BY d.id
            ORDER BY completion_rate DESC, d.fee ASC
        ");
        
        $searchTerm = "%".$specialization."%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $doctors = [];
        while ($doctor = $result->fetch_assoc()) {
            $doctor['rating'] = $this->calculateDoctorRating($doctor);
            $doctors[] = $doctor;
        }
        
        $stmt->close();
        
        return $doctors;
    }
    
    /**
     * Calculate doctor rating based on various factors
     */
    private function calculateDoctorRating($doctor) {
        $rating = 3.0; // Base rating
        
        // Adjust based on completion rate
        if ($doctor['completion_rate'] > 0.8) {
            $rating += 1.5;
        } elseif ($doctor['completion_rate'] > 0.6) {
            $rating += 1.0;
        } elseif ($doctor['completion_rate'] > 0.4) {
            $rating += 0.5;
        }
        
        // Adjust based on experience (approximated by total appointments)
        if ($doctor['total_appointments'] > 100) {
            $rating += 0.5;
        }
        
        return min(5.0, $rating); // Cap at 5.0
    }
    
    /**
     * Search doctors by multiple criteria
     */
    public function searchDoctors($criteria) {
        $sqlQuery = "SELECT * FROM ".$this->doctorTable." WHERE 1=1";
        $params = [];
        $types = '';
        
        if (!empty($criteria['name'])) {
            $sqlQuery .= " AND name LIKE ?";
            $params[] = "%".$criteria['name']."%";
            $types .= 's';
        }
        
        if (!empty($criteria['specialization'])) {
            $sqlQuery .= " AND specialization LIKE ?";
            $params[] = "%".$criteria['specialization']."%";
            $types .= 's';
        }
        
        if (!empty($criteria['max_fee'])) {
            $sqlQuery .= " AND fee <= ?";
            $params[] = $criteria['max_fee'];
            $types .= 'i';
        }
        
        if (!empty($criteria['location'])) {
            $sqlQuery .= " AND (address LIKE ? OR location LIKE ?)";
            $locationSearch = "%".$criteria['location']."%";
            $params[] = $locationSearch;
            $params[] = $locationSearch;
            $types .= 'ss';
        }
        
        $sqlQuery .= " ORDER BY fee ASC";
        
        $stmt = $this->conn->prepare($sqlQuery);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $doctors = [];
        while ($doctor = $result->fetch_assoc()) {
            $doctors[] = $doctor;
        }
        
        $stmt->close();
        
        return $doctors;
    }
    
    /**
     * Get available specializations
     */
    public function getSpecializations() {
        $stmt = $this->conn->prepare("
            SELECT DISTINCT specialization 
            FROM ".$this->doctorTable." 
            WHERE specialization IS NOT NULL AND specialization != ''
            ORDER BY specialization
        ");
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $specializations = [];
        while ($row = $result->fetch_assoc()) {
            $specializations[] = $row['specialization'];
        }
        
        $stmt->close();
        
        return $specializations;
    }
}
?>
