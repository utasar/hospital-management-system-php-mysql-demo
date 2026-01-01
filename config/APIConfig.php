<?php
/**
 * API Configuration for AI Dr. Care
 * 
 * This file contains configuration for external API integrations
 * such as medication databases, Google Maps, and AI/ML services
 */

class APIConfig {
    
    // Google Maps API (for location-based doctor recommendations)
    const GOOGLE_MAPS_API_KEY = 'YOUR_GOOGLE_MAPS_API_KEY_HERE';
    const GOOGLE_MAPS_ENABLED = false; // Set to true when API key is configured
    
    // OpenFDA API for medication information
    const OPENFDA_BASE_URL = 'https://api.fda.gov/drug/';
    const OPENFDA_ENABLED = true;
    
    // For future ML/AI integration
    const ML_API_ENDPOINT = 'http://localhost:5000/api/'; // Python ML service
    const ML_API_ENABLED = false;
    
    // Data encryption settings
    const ENCRYPTION_ENABLED = true;
    const ENCRYPTION_ALGORITHM = 'AES-256-CBC';
    
    // HIPAA/GDPR compliance settings
    const AUDIT_LOGGING_ENABLED = true;
    const DATA_RETENTION_DAYS = 2555; // ~7 years for medical records
    
    // AI symptom analysis settings
    const AI_CONFIDENCE_THRESHOLD = 40; // Minimum confidence to show diagnosis
    const AI_MAX_DIAGNOSES = 5; // Maximum number of diagnoses to return
    
    /**
     * Get Google Maps API configuration
     */
    public static function getGoogleMapsConfig() {
        return [
            'api_key' => self::GOOGLE_MAPS_API_KEY,
            'enabled' => self::GOOGLE_MAPS_ENABLED,
            'libraries' => ['places', 'geometry']
        ];
    }
    
    /**
     * Get OpenFDA API configuration
     */
    public static function getOpenFDAConfig() {
        return [
            'base_url' => self::OPENFDA_BASE_URL,
            'enabled' => self::OPENFDA_ENABLED
        ];
    }
    
    /**
     * Get ML service configuration
     */
    public static function getMLConfig() {
        return [
            'endpoint' => self::ML_API_ENDPOINT,
            'enabled' => self::ML_API_ENABLED,
            'timeout' => 30
        ];
    }
    
    /**
     * Check if feature is enabled
     */
    public static function isFeatureEnabled($feature) {
        $features = [
            'google_maps' => self::GOOGLE_MAPS_ENABLED,
            'openfda' => self::OPENFDA_ENABLED,
            'ml_service' => self::ML_API_ENABLED,
            'encryption' => self::ENCRYPTION_ENABLED,
            'audit_logging' => self::AUDIT_LOGGING_ENABLED
        ];
        
        return isset($features[$feature]) ? $features[$feature] : false;
    }
}
?>
