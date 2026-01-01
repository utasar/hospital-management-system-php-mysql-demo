-- AI Dr. Care Database Schema Extensions
-- This script adds tables for AI-powered healthcare features

-- Table for storing AI symptom analysis and diagnosis
CREATE TABLE IF NOT EXISTS `hms_ai_diagnosis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `symptoms` text NOT NULL,
  `ai_diagnosis` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for storing patient vital signs
CREATE TABLE IF NOT EXISTS `hms_vital_signs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `temperature` decimal(4,2) DEFAULT NULL COMMENT 'Temperature in Celsius',
  `blood_pressure_systolic` int(11) DEFAULT NULL COMMENT 'Systolic BP in mmHg',
  `blood_pressure_diastolic` int(11) DEFAULT NULL COMMENT 'Diastolic BP in mmHg',
  `heart_rate` int(11) DEFAULT NULL COMMENT 'Heart rate in bpm',
  `respiratory_rate` int(11) DEFAULT NULL COMMENT 'Respiratory rate per minute',
  `oxygen_saturation` decimal(5,2) DEFAULT NULL COMMENT 'SpO2 percentage',
  `weight` decimal(5,2) DEFAULT NULL COMMENT 'Weight in kg',
  `height` decimal(5,2) DEFAULT NULL COMMENT 'Height in meters',
  `bmi` decimal(5,2) DEFAULT NULL COMMENT 'Body Mass Index',
  `notes` text DEFAULT NULL,
  `recorded_at` datetime NOT NULL,
  `recorded_by` int(11) DEFAULT NULL COMMENT 'User ID who recorded',
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `recorded_at` (`recorded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add location field to doctor table for location-based recommendations
ALTER TABLE `hms_doctor` 
ADD COLUMN `location` varchar(255) DEFAULT NULL COMMENT 'GPS coordinates or address for map integration' AFTER `address`,
ADD COLUMN `latitude` decimal(10,8) DEFAULT NULL AFTER `location`,
ADD COLUMN `longitude` decimal(11,8) DEFAULT NULL AFTER `latitude`;

-- Table for medication suggestions and interactions
CREATE TABLE IF NOT EXISTS `hms_medications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `generic_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `common_uses` text DEFAULT NULL,
  `side_effects` text DEFAULT NULL,
  `interactions` text DEFAULT NULL COMMENT 'Known drug interactions',
  `contraindications` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for patient medication prescriptions
CREATE TABLE IF NOT EXISTS `hms_patient_medications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `medication_id` int(11) NOT NULL,
  `prescribed_by` int(11) DEFAULT NULL COMMENT 'Doctor ID',
  `dosage` varchar(100) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('Active','Completed','Discontinued') DEFAULT 'Active',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `medication_id` (`medication_id`),
  KEY `prescribed_by` (`prescribed_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for audit logs (HIPAA/GDPR compliance)
CREATE TABLE IF NOT EXISTS `hms_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) NOT NULL COMMENT 'patient, doctor, appointment, etc.',
  `entity_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `entity_type` (`entity_type`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for patient consent management (GDPR compliance)
CREATE TABLE IF NOT EXISTS `hms_patient_consent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `consent_type` varchar(100) NOT NULL COMMENT 'data_processing, ai_analysis, third_party_sharing, etc.',
  `consent_given` tinyint(1) NOT NULL DEFAULT 0,
  `consent_text` text DEFAULT NULL,
  `given_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `consent_type` (`consent_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for medical reports and documents
CREATE TABLE IF NOT EXISTS `hms_medical_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `report_type` varchar(100) DEFAULT NULL COMMENT 'lab_test, xray, mri, ct_scan, etc.',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` datetime NOT NULL,
  `is_encrypted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `report_type` (`report_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample medications data
INSERT INTO `hms_medications` (`name`, `generic_name`, `category`, `description`, `common_uses`, `side_effects`) VALUES
('Paracetamol', 'Acetaminophen', 'Pain Relief', 'Common pain reliever and fever reducer', 'Headache, fever, minor pain', 'Rare: liver damage with overdose'),
('Ibuprofen', 'Ibuprofen', 'NSAID', 'Anti-inflammatory pain reliever', 'Pain, inflammation, fever', 'Stomach upset, increased bleeding risk'),
('Amoxicillin', 'Amoxicillin', 'Antibiotic', 'Penicillin-type antibiotic', 'Bacterial infections', 'Nausea, diarrhea, allergic reactions'),
('Lisinopril', 'Lisinopril', 'ACE Inhibitor', 'Blood pressure medication', 'Hypertension, heart failure', 'Dizziness, dry cough'),
('Metformin', 'Metformin', 'Antidiabetic', 'Blood sugar control medication', 'Type 2 diabetes', 'Stomach upset, diarrhea'),
('Omeprazole', 'Omeprazole', 'PPI', 'Stomach acid reducer', 'Heartburn, acid reflux, ulcers', 'Headache, stomach pain'),
('Aspirin', 'Acetylsalicylic Acid', 'NSAID', 'Pain reliever and blood thinner', 'Pain, fever, heart attack prevention', 'Stomach bleeding, allergic reactions');

-- Add indices for better performance
CREATE INDEX idx_patient_consent_status ON hms_patient_consent(patient_id, consent_type, consent_given);
CREATE INDEX idx_vital_signs_date ON hms_vital_signs(patient_id, recorded_at);
CREATE INDEX idx_diagnosis_date ON hms_ai_diagnosis(patient_id, created_at);
