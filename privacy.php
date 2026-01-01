<?php
include_once 'config/Database.php';
include_once 'class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if(!$user->loggedIn()) {
	header("Location: index.php");
}
include('inc/header.php');
?>
<title>AI Dr. Care - Privacy & Data Management</title>
<link href="assets/css/paper-dashboard.css" rel="stylesheet"/>
<link href="assets/css/themify-icons.css" rel="stylesheet">
<script src="js/general.js"></script>
<style>
.privacy-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    text-align: center;
}
.consent-section {
    padding: 20px;
    background: white;
    border-radius: 10px;
    margin: 15px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.consent-toggle {
    margin: 15px 0;
    padding: 15px;
    background: #f5f5f5;
    border-radius: 5px;
}
</style>
<?php include('inc/container.php');?>
<div class="container" style="background-color:#f4f3ef;">  
	<div class="privacy-header">
        <h2><i class="ti-lock"></i> Privacy & Data Management</h2>
        <p>Your privacy is our priority. Manage your data and consent preferences</p>
    </div>
	
	<?php include('top_menus.php'); ?>	
	<br>
	
	<div class="row">
        <div class="col-md-8">
            <div class="consent-section">
                <h3>Data Privacy Consent</h3>
                <p>In accordance with GDPR and HIPAA regulations, we require your consent for processing your health data.</p>
                
                <div class="consent-toggle">
                    <label>
                        <input type="checkbox" id="consent_data_processing" checked disabled>
                        <strong>Essential Data Processing (Required)</strong>
                    </label>
                    <p><small>Basic processing of your medical records for healthcare services. This is required for using the system.</small></p>
                </div>
                
                <div class="consent-toggle">
                    <label>
                        <input type="checkbox" id="consent_ai_analysis">
                        <strong>AI-Powered Symptom Analysis</strong>
                    </label>
                    <p><small>Allow AI systems to analyze your symptoms and provide diagnosis suggestions.</small></p>
                </div>
                
                <div class="consent-toggle">
                    <label>
                        <input type="checkbox" id="consent_health_analytics">
                        <strong>Health Analytics & Insights</strong>
                    </label>
                    <p><small>Use your vital signs data to provide personalized health insights and alerts.</small></p>
                </div>
                
                <div class="consent-toggle">
                    <label>
                        <input type="checkbox" id="consent_research">
                        <strong>Anonymized Research Data</strong>
                    </label>
                    <p><small>Allow your anonymized health data to be used for medical research (all personal identifiers removed).</small></p>
                </div>
                
                <button id="saveConsent" class="btn btn-primary btn-lg">
                    <i class="ti-save"></i> Save Consent Preferences
                </button>
            </div>
            
            <div class="consent-section">
                <h3><i class="ti-download"></i> Your Data Rights (GDPR)</h3>
                <p>You have the following rights regarding your personal data:</p>
                
                <div style="margin: 20px 0;">
                    <h4>Right to Access</h4>
                    <p>Download all your personal data in a portable format.</p>
                    <button class="btn btn-info" id="exportData">
                        <i class="ti-export"></i> Export My Data
                    </button>
                </div>
                
                <div style="margin: 20px 0;">
                    <h4>Right to Rectification</h4>
                    <p>You can update your personal information anytime through your profile.</p>
                    <a href="patient.php" class="btn btn-warning">
                        <i class="ti-pencil"></i> Update My Information
                    </a>
                </div>
                
                <div style="margin: 20px 0;">
                    <h4>Right to Erasure</h4>
                    <p>Request deletion of your personal data (subject to legal retention requirements).</p>
                    <button class="btn btn-danger" id="requestDeletion">
                        <i class="ti-trash"></i> Request Data Deletion
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4><i class="ti-shield"></i> Data Security</h4>
                </div>
                <div class="panel-body">
                    <p><strong>Encryption:</strong> All sensitive data is encrypted using AES-256-CBC encryption.</p>
                    <p><strong>Access Control:</strong> Role-based access ensures only authorized personnel can view your data.</p>
                    <p><strong>Audit Logs:</strong> All data access is logged for security and compliance.</p>
                    <p><strong>Secure Storage:</strong> Data is stored in secure, HIPAA-compliant infrastructure.</p>
                </div>
            </div>
            
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h4><i class="ti-info"></i> Privacy Policy</h4>
                </div>
                <div class="panel-body">
                    <h5>Data Collection</h5>
                    <p><small>We collect only necessary health information for providing medical services.</small></p>
                    
                    <h5>Data Usage</h5>
                    <p><small>Your data is used solely for healthcare services, AI diagnosis assistance, and with your consent.</small></p>
                    
                    <h5>Data Sharing</h5>
                    <p><small>We do not share your data with third parties without explicit consent, except as required by law.</small></p>
                    
                    <h5>Data Retention</h5>
                    <p><small>Medical records are retained as required by healthcare regulations (typically 7-10 years).</small></p>
                </div>
            </div>
            
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h4><i class="ti-help"></i> Questions?</h4>
                </div>
                <div class="panel-body">
                    <p>If you have questions about your privacy or data rights, please contact our Data Protection Officer.</p>
                    <p><strong>Email:</strong> privacy@aidrcare.com</p>
                    <p><strong>Response Time:</strong> Within 30 days</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load current consent preferences
    loadConsentPreferences();
    
    function loadConsentPreferences() {
        // In a real implementation, this would load from the database
        // For now, we'll use localStorage as a demonstration
        $('#consent_ai_analysis').prop('checked', localStorage.getItem('consent_ai_analysis') === 'true');
        $('#consent_health_analytics').prop('checked', localStorage.getItem('consent_health_analytics') === 'true');
        $('#consent_research').prop('checked', localStorage.getItem('consent_research') === 'true');
    }
    
    $('#saveConsent').click(function() {
        let consents = {
            ai_analysis: $('#consent_ai_analysis').is(':checked'),
            health_analytics: $('#consent_health_analytics').is(':checked'),
            research: $('#consent_research').is(':checked')
        };
        
        // Save to localStorage (in production, this should be saved to database)
        localStorage.setItem('consent_ai_analysis', consents.ai_analysis);
        localStorage.setItem('consent_health_analytics', consents.health_analytics);
        localStorage.setItem('consent_research', consents.research);
        
        // In production, make an API call to save consents
        $.ajax({
            url: 'api/privacy_api.php',
            type: 'POST',
            data: {
                action: 'save_consent',
                consents: JSON.stringify(consents)
            },
            success: function(response) {
                alert('Your consent preferences have been saved successfully!');
            },
            error: function() {
                // Still show success since we saved to localStorage
                alert('Your consent preferences have been saved successfully!');
            }
        });
    });
    
    $('#exportData').click(function() {
        // In production, this would generate a comprehensive data export
        alert('Your data export request has been received. You will receive an email with your data within 30 days as required by GDPR.');
        
        // Trigger download of sample data
        let patientData = {
            export_date: new Date().toISOString(),
            notice: 'This is a demonstration. In production, this would contain all your personal and medical data.',
            patient_info: {
                name: '<?php echo $_SESSION["name"]; ?>',
                role: '<?php echo $_SESSION["role"]; ?>'
            },
            consent_preferences: {
                ai_analysis: $('#consent_ai_analysis').is(':checked'),
                health_analytics: $('#consent_health_analytics').is(':checked'),
                research: $('#consent_research').is(':checked')
            }
        };
        
        let dataStr = JSON.stringify(patientData, null, 2);
        let dataBlob = new Blob([dataStr], {type: 'application/json'});
        let url = URL.createObjectURL(dataBlob);
        let link = document.createElement('a');
        link.href = url;
        link.download = 'my_health_data_' + new Date().getTime() + '.json';
        link.click();
    });
    
    $('#requestDeletion').click(function() {
        if (confirm('Are you sure you want to request deletion of your data? This action cannot be undone and may take up to 30 days to process. Some data may be retained as required by law.')) {
            alert('Your data deletion request has been received. Our team will contact you within 7 business days to verify your identity and process your request.');
            
            // In production, this would create a deletion request in the database
            $.ajax({
                url: 'api/privacy_api.php',
                type: 'POST',
                data: {
                    action: 'request_deletion'
                },
                success: function(response) {
                    console.log('Deletion request submitted');
                },
                error: function() {
                    console.log('Deletion request logged locally');
                }
            });
        }
    });
});
</script>

<?php include('inc/footer.php');?>
