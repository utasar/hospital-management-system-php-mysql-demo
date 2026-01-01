<?php
class VitalSigns {
    
    private $vitalSignsTable = 'hms_vital_signs';
    private $conn;
    
    public function __construct($db){
        $this->conn = $db;
    }
    
    /**
     * Add vital signs record for a patient
     */
    public function addVitalSigns($patientId, $data) {
        $stmt = $this->conn->prepare("
            INSERT INTO ".$this->vitalSignsTable."
            (patient_id, temperature, blood_pressure_systolic, blood_pressure_diastolic, 
             heart_rate, respiratory_rate, oxygen_saturation, weight, height, bmi, notes, recorded_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $temperature = $data['temperature'] ?? null;
        $bp_systolic = $data['bp_systolic'] ?? null;
        $bp_diastolic = $data['bp_diastolic'] ?? null;
        $heart_rate = $data['heart_rate'] ?? null;
        $respiratory_rate = $data['respiratory_rate'] ?? null;
        $oxygen_saturation = $data['oxygen_saturation'] ?? null;
        $weight = $data['weight'] ?? null;
        $height = $data['height'] ?? null;
        $bmi = $data['bmi'] ?? null;
        $notes = $data['notes'] ?? '';
        $recorded_at = date('Y-m-d H:i:s');
        
        if ($stmt) {
            $stmt->bind_param("iddiidiiddss", 
                $patientId, $temperature, $bp_systolic, $bp_diastolic,
                $heart_rate, $respiratory_rate, $oxygen_saturation,
                $weight, $height, $bmi, $notes, $recorded_at
            );
            
            if ($stmt->execute()) {
                return ['status' => 'success', 'message' => 'Vital signs recorded successfully'];
            }
        }
        
        return ['status' => 'error', 'message' => 'Failed to record vital signs'];
    }
    
    /**
     * Get vital signs history for a patient
     */
    public function getVitalSignsHistory($patientId, $limit = 10) {
        $stmt = $this->conn->prepare("
            SELECT * FROM ".$this->vitalSignsTable."
            WHERE patient_id = ?
            ORDER BY recorded_at DESC
            LIMIT ?
        ");
        
        if ($stmt) {
            $stmt->bind_param("ii", $patientId, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $history = [];
            while ($row = $result->fetch_assoc()) {
                $history[] = $row;
            }
            
            $stmt->close();
            return $history;
        }
        
        return [];
    }
    
    /**
     * Get latest vital signs for a patient
     */
    public function getLatestVitalSigns($patientId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM ".$this->vitalSignsTable."
            WHERE patient_id = ?
            ORDER BY recorded_at DESC
            LIMIT 1
        ");
        
        if ($stmt) {
            $stmt->bind_param("i", $patientId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return $row;
        }
        
        return null;
    }
    
    /**
     * Analyze vital signs and provide health alerts
     */
    public function analyzeVitalSigns($vitalSigns) {
        $alerts = [];
        
        // Temperature analysis
        if (!empty($vitalSigns['temperature'])) {
            $temp = $vitalSigns['temperature'];
            if ($temp > 38.0) {
                $alerts[] = [
                    'type' => 'warning',
                    'parameter' => 'Temperature',
                    'value' => $temp . '°C',
                    'message' => 'Elevated temperature detected. May indicate fever.'
                ];
            } elseif ($temp < 36.0) {
                $alerts[] = [
                    'type' => 'warning',
                    'parameter' => 'Temperature',
                    'value' => $temp . '°C',
                    'message' => 'Low body temperature detected.'
                ];
            }
        }
        
        // Blood pressure analysis
        if (!empty($vitalSigns['blood_pressure_systolic']) && !empty($vitalSigns['blood_pressure_diastolic'])) {
            $systolic = $vitalSigns['blood_pressure_systolic'];
            $diastolic = $vitalSigns['blood_pressure_diastolic'];
            
            if ($systolic > 140 || $diastolic > 90) {
                $alerts[] = [
                    'type' => 'danger',
                    'parameter' => 'Blood Pressure',
                    'value' => $systolic . '/' . $diastolic . ' mmHg',
                    'message' => 'High blood pressure detected. Please consult a doctor.'
                ];
            } elseif ($systolic < 90 || $diastolic < 60) {
                $alerts[] = [
                    'type' => 'warning',
                    'parameter' => 'Blood Pressure',
                    'value' => $systolic . '/' . $diastolic . ' mmHg',
                    'message' => 'Low blood pressure detected.'
                ];
            }
        }
        
        // Heart rate analysis
        if (!empty($vitalSigns['heart_rate'])) {
            $hr = $vitalSigns['heart_rate'];
            if ($hr > 100) {
                $alerts[] = [
                    'type' => 'warning',
                    'parameter' => 'Heart Rate',
                    'value' => $hr . ' bpm',
                    'message' => 'Elevated heart rate detected (Tachycardia).'
                ];
            } elseif ($hr < 60) {
                $alerts[] = [
                    'type' => 'info',
                    'parameter' => 'Heart Rate',
                    'value' => $hr . ' bpm',
                    'message' => 'Low heart rate detected (Bradycardia). This may be normal for athletes.'
                ];
            }
        }
        
        // Oxygen saturation analysis
        if (!empty($vitalSigns['oxygen_saturation'])) {
            $o2 = $vitalSigns['oxygen_saturation'];
            if ($o2 < 95) {
                $alerts[] = [
                    'type' => 'danger',
                    'parameter' => 'Oxygen Saturation',
                    'value' => $o2 . '%',
                    'message' => 'Low oxygen saturation. Seek medical attention immediately if below 90%.'
                ];
            }
        }
        
        // BMI analysis
        if (!empty($vitalSigns['bmi'])) {
            $bmi = $vitalSigns['bmi'];
            if ($bmi < 18.5) {
                $alerts[] = [
                    'type' => 'info',
                    'parameter' => 'BMI',
                    'value' => $bmi,
                    'message' => 'Underweight. Consider consulting a nutritionist.'
                ];
            } elseif ($bmi >= 25 && $bmi < 30) {
                $alerts[] = [
                    'type' => 'info',
                    'parameter' => 'BMI',
                    'value' => $bmi,
                    'message' => 'Overweight. Consider a healthy diet and exercise.'
                ];
            } elseif ($bmi >= 30) {
                $alerts[] = [
                    'type' => 'warning',
                    'parameter' => 'BMI',
                    'value' => $bmi,
                    'message' => 'Obese. Please consult a healthcare provider.'
                ];
            }
        }
        
        return $alerts;
    }
    
    /**
     * Calculate BMI
     */
    public function calculateBMI($weight, $height) {
        if ($weight > 0 && $height > 0) {
            // Height should be in meters, weight in kg
            $bmi = $weight / ($height * $height);
            return round($bmi, 2);
        }
        return null;
    }
}
?>
