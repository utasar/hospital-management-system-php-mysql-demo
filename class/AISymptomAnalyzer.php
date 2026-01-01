<?php
class AISymptomAnalyzer {
    
    private $conn;
    private $symptomsTable = 'hms_symptoms';
    private $diagnosisTable = 'hms_ai_diagnosis';
    
    public function __construct($db){
        $this->conn = $db;
    }
    
    /**
     * Analyze symptoms and suggest possible diagnoses
     * This is a simple rule-based system that can be enhanced with ML models
     */
    public function analyzeSymptoms($symptoms, $patientId) {
        $symptomList = is_array($symptoms) ? $symptoms : explode(',', $symptoms);
        $symptomList = array_map('trim', $symptomList);
        
        // Simple symptom-disease mapping (can be replaced with ML model)
        $diseasePatterns = [
            'Common Cold' => ['cough', 'runny nose', 'sore throat', 'sneezing'],
            'Flu' => ['fever', 'cough', 'body ache', 'fatigue', 'headache'],
            'COVID-19' => ['fever', 'dry cough', 'loss of taste', 'loss of smell', 'fatigue'],
            'Allergies' => ['sneezing', 'runny nose', 'itchy eyes', 'congestion'],
            'Migraine' => ['severe headache', 'nausea', 'sensitivity to light', 'visual disturbances'],
            'Hypertension' => ['headache', 'dizziness', 'chest pain', 'shortness of breath'],
            'Diabetes' => ['frequent urination', 'excessive thirst', 'fatigue', 'blurred vision'],
            'Asthma' => ['wheezing', 'shortness of breath', 'chest tightness', 'coughing'],
        ];
        
        $possibleDiagnoses = [];
        
        foreach ($diseasePatterns as $disease => $patterns) {
            $matchCount = 0;
            foreach ($symptomList as $symptom) {
                foreach ($patterns as $pattern) {
                    if (stripos($symptom, $pattern) !== false || stripos($pattern, $symptom) !== false) {
                        $matchCount++;
                        break;
                    }
                }
            }
            
            if ($matchCount > 0) {
                $confidence = round(($matchCount / count($patterns)) * 100, 2);
                $possibleDiagnoses[] = [
                    'disease' => $disease,
                    'confidence' => $confidence,
                    'matched_symptoms' => $matchCount,
                    'total_symptoms' => count($patterns)
                ];
            }
        }
        
        // Sort by confidence
        usort($possibleDiagnoses, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });
        
        // Save diagnosis to database
        $this->saveDiagnosis($patientId, $symptomList, $possibleDiagnoses);
        
        return [
            'status' => 'success',
            'symptoms' => $symptomList,
            'diagnoses' => array_slice($possibleDiagnoses, 0, 5), // Top 5 diagnoses
            'recommendation' => $this->getRecommendation($possibleDiagnoses)
        ];
    }
    
    /**
     * Save diagnosis to database for record keeping
     */
    private function saveDiagnosis($patientId, $symptoms, $diagnoses) {
        $symptomsJson = json_encode($symptoms);
        $diagnosesJson = json_encode($diagnoses);
        $timestamp = date('Y-m-d H:i:s');
        
        $stmt = $this->conn->prepare("
            INSERT INTO ".$this->diagnosisTable."
            (patient_id, symptoms, ai_diagnosis, created_at)
            VALUES (?, ?, ?, ?)
        ");
        
        if ($stmt) {
            $stmt->bind_param("isss", $patientId, $symptomsJson, $diagnosesJson, $timestamp);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    /**
     * Get recommendation based on diagnosis confidence
     */
    private function getRecommendation($diagnoses) {
        if (empty($diagnoses)) {
            return "No clear diagnosis found. Please consult a healthcare professional for accurate diagnosis.";
        }
        
        $topDiagnosis = $diagnoses[0];
        
        if ($topDiagnosis['confidence'] >= 70) {
            return "High confidence diagnosis: " . $topDiagnosis['disease'] . ". Please book an appointment with a specialist for confirmation and treatment.";
        } elseif ($topDiagnosis['confidence'] >= 40) {
            return "Possible diagnosis: " . $topDiagnosis['disease'] . ". Recommend consulting a doctor for proper examination.";
        } else {
            return "Symptoms are not specific enough. Please consult a healthcare professional for accurate diagnosis.";
        }
    }
    
    /**
     * Get common symptoms list
     */
    public function getCommonSymptoms() {
        return [
            'General' => ['fever', 'fatigue', 'weakness', 'loss of appetite', 'weight loss'],
            'Respiratory' => ['cough', 'shortness of breath', 'wheezing', 'chest pain', 'runny nose', 'sore throat', 'sneezing'],
            'Digestive' => ['nausea', 'vomiting', 'diarrhea', 'constipation', 'abdominal pain', 'bloating'],
            'Neurological' => ['headache', 'dizziness', 'confusion', 'seizures', 'numbness', 'tingling'],
            'Musculoskeletal' => ['joint pain', 'muscle pain', 'back pain', 'stiffness'],
            'Skin' => ['rash', 'itching', 'redness', 'swelling', 'hives'],
            'Other' => ['frequent urination', 'excessive thirst', 'blurred vision', 'loss of taste', 'loss of smell']
        ];
    }
    
    /**
     * Get patient's diagnosis history
     */
    public function getPatientDiagnosisHistory($patientId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM ".$this->diagnosisTable."
            WHERE patient_id = ?
            ORDER BY created_at DESC
            LIMIT 10
        ");
        
        if ($stmt) {
            $stmt->bind_param("i", $patientId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $history = [];
            while ($row = $result->fetch_assoc()) {
                $row['symptoms'] = json_decode($row['symptoms'], true);
                $row['ai_diagnosis'] = json_decode($row['ai_diagnosis'], true);
                $history[] = $row;
            }
            
            $stmt->close();
            return $history;
        }
        
        return [];
    }
}
?>
