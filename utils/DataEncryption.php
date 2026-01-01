<?php
class DataEncryption {
    
    private $encryptionKey;
    private $cipher = "AES-256-CBC";
    
    public function __construct($key = null) {
        // Use provided key or generate from environment
        $this->encryptionKey = $key ?: $this->getEncryptionKey();
    }
    
    /**
     * Get encryption key from environment or config
     */
    private function getEncryptionKey() {
        // In production, this should come from environment variables or secure config
        $key = getenv('ENCRYPTION_KEY');
        if (!$key) {
            // Fallback to a default key (should be changed in production)
            $key = 'HMS_SECURE_KEY_2024_CHANGE_IN_PRODUCTION_12345678';
        }
        return hash('sha256', $key, true);
    }
    
    /**
     * Encrypt sensitive data
     */
    public function encrypt($data) {
        if (empty($data)) {
            return $data;
        }
        
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        $encrypted = openssl_encrypt(
            $data,
            $this->cipher,
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv
        );
        
        // Combine IV and encrypted data, then base64 encode
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt sensitive data
     */
    public function decrypt($data) {
        if (empty($data)) {
            return $data;
        }
        
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length($this->cipher);
        
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        
        return openssl_decrypt(
            $encrypted,
            $this->cipher,
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
    
    /**
     * Hash sensitive data (one-way)
     */
    public function hash($data) {
        return hash('sha256', $data);
    }
    
    /**
     * Verify hashed data
     */
    public function verifyHash($data, $hash) {
        return hash_equals($hash, $this->hash($data));
    }
    
    /**
     * Sanitize patient data for HIPAA/GDPR compliance
     */
    public function sanitizePatientData($data, $fieldsToEncrypt = []) {
        $defaultSensitiveFields = [
            'medical_history',
            'ssn',
            'insurance_number',
            'blood_type',
            'allergies',
            'medications'
        ];
        
        $fieldsToEncrypt = array_merge($defaultSensitiveFields, $fieldsToEncrypt);
        
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $fieldsToEncrypt) && !empty($value)) {
                $sanitized[$key] = $this->encrypt($value);
            } else {
                $sanitized[$key] = htmlspecialchars(strip_tags($value));
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Decrypt patient data
     */
    public function decryptPatientData($data, $fieldsToDecrypt = []) {
        $defaultSensitiveFields = [
            'medical_history',
            'ssn',
            'insurance_number',
            'blood_type',
            'allergies',
            'medications'
        ];
        
        $fieldsToDecrypt = array_merge($defaultSensitiveFields, $fieldsToDecrypt);
        
        $decrypted = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $fieldsToDecrypt) && !empty($value)) {
                $decrypted[$key] = $this->decrypt($value);
            } else {
                $decrypted[$key] = $value;
            }
        }
        
        return $decrypted;
    }
}
?>
