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
<title>AI Dr. Care - Symptom Checker</title>
<link href="assets/css/paper-dashboard.css" rel="stylesheet"/>
<link href="assets/css/themify-icons.css" rel="stylesheet">
<script src="js/general.js"></script>
<style>
.symptom-category {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}
.symptom-badge {
    display: inline-block;
    margin: 5px;
    padding: 8px 15px;
    background: #00796B;
    color: white;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s;
}
.symptom-badge:hover {
    background: #004D40;
    transform: scale(1.05);
}
.symptom-badge.selected {
    background: #FF5722;
}
.diagnosis-card {
    margin: 10px 0;
    padding: 15px;
    border-left: 4px solid #00796B;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.confidence-bar {
    height: 20px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 10px;
}
.confidence-fill {
    height: 100%;
    background: linear-gradient(90deg, #4CAF50, #8BC34A);
    transition: width 0.5s ease;
}
.ai-assistant-header {
    background: linear-gradient(135deg, #00796B 0%, #004D40 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    text-align: center;
}
.ai-icon {
    font-size: 48px;
    margin-bottom: 10px;
}
</style>
<?php include('inc/container.php');?>
<div class="container" style="background-color:#f4f3ef;">  
	<div class="ai-assistant-header">
        <div class="ai-icon">🤖</div>
        <h2>AI Dr. Care - Symptom Checker</h2>
        <p>AI-powered symptom analysis and diagnosis assistance</p>
    </div>
	
	<?php include('top_menus.php'); ?>	
	<br>
	
	<div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading" style="background:#00796B;color:white;">
                    <h4>Select Your Symptoms</h4>
                </div>
                <div class="panel-body">
                    <div id="symptomCategories"></div>
                    
                    <div style="margin-top: 20px;">
                        <h5>Selected Symptoms:</h5>
                        <div id="selectedSymptoms" style="min-height: 50px; padding: 10px; background: #f5f5f5; border-radius: 5px;">
                            <em>Click symptoms above to select them</em>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <h5>Or enter custom symptoms:</h5>
                        <textarea id="customSymptoms" class="form-control" rows="3" placeholder="E.g., headache, fever, cough..."></textarea>
                    </div>
                    
                    <button id="analyzeBtn" class="btn btn-lg btn-success" style="margin-top: 20px; width: 100%;">
                        <i class="ti-pulse"></i> Analyze Symptoms
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4>How It Works</h4>
                </div>
                <div class="panel-body">
                    <ol style="padding-left: 20px;">
                        <li>Select symptoms you're experiencing</li>
                        <li>Click "Analyze Symptoms"</li>
                        <li>Review AI-powered diagnosis suggestions</li>
                        <li>Book appointment with recommended specialist</li>
                    </ol>
                    <div class="alert alert-warning" style="margin-top: 15px;">
                        <strong>Note:</strong> This is an AI assistant and not a replacement for professional medical advice. Always consult with a healthcare provider.
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="diagnosisResults" style="display: none; margin-top: 30px;">
        <div class="panel panel-success">
            <div class="panel-heading" style="background:#4CAF50;color:white;">
                <h4><i class="ti-check"></i> AI Diagnosis Results</h4>
            </div>
            <div class="panel-body">
                <div id="diagnosisContent"></div>
                
                <div style="margin-top: 20px;">
                    <h5>Recommended Actions:</h5>
                    <div id="recommendations"></div>
                </div>
                
                <div style="margin-top: 20px; text-align: center;">
                    <button id="findDoctorBtn" class="btn btn-primary btn-lg">
                        <i class="ti-search"></i> Find Specialist Doctor
                    </button>
                    <button id="bookAppointmentBtn" class="btn btn-info btn-lg">
                        <i class="ti-calendar"></i> Book Appointment
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div id="doctorRecommendations" style="display: none; margin-top: 30px;">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="ti-user"></i> Recommended Doctors</h4>
            </div>
            <div class="panel-body">
                <div id="doctorList"></div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let selectedSymptoms = [];
    
    // Load common symptoms
    loadCommonSymptoms();
    
    function loadCommonSymptoms() {
        $.ajax({
            url: 'api/ai_symptom_analysis.php',
            type: 'GET',
            data: { action: 'getCommonSymptoms' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displaySymptomCategories(response.symptoms);
                }
            }
        });
    }
    
    function displaySymptomCategories(symptoms) {
        let html = '';
        for (let category in symptoms) {
            html += '<div class="symptom-category">';
            html += '<h5><strong>' + category + '</strong></h5>';
            symptoms[category].forEach(function(symptom) {
                html += '<span class="symptom-badge" data-symptom="' + symptom + '">' + symptom + '</span>';
            });
            html += '</div>';
        }
        $('#symptomCategories').html(html);
        
        // Add click handlers
        $('.symptom-badge').click(function() {
            let symptom = $(this).data('symptom');
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
                selectedSymptoms = selectedSymptoms.filter(s => s !== symptom);
            } else {
                $(this).addClass('selected');
                selectedSymptoms.push(symptom);
            }
            updateSelectedSymptoms();
        });
    }
    
    function updateSelectedSymptoms() {
        if (selectedSymptoms.length === 0) {
            $('#selectedSymptoms').html('<em>Click symptoms above to select them</em>');
        } else {
            let html = selectedSymptoms.map(s => '<span class="symptom-badge selected">' + s + '</span>').join('');
            $('#selectedSymptoms').html(html);
        }
    }
    
    $('#analyzeBtn').click(function() {
        let symptoms = selectedSymptoms.slice();
        let customSymptoms = $('#customSymptoms').val().trim();
        
        if (customSymptoms) {
            let custom = customSymptoms.split(',').map(s => s.trim()).filter(s => s);
            symptoms = symptoms.concat(custom);
        }
        
        if (symptoms.length === 0) {
            alert('Please select or enter at least one symptom');
            return;
        }
        
        $(this).prop('disabled', true).html('<i class="ti-reload"></i> Analyzing...');
        
        $.ajax({
            url: 'api/ai_symptom_analysis.php',
            type: 'POST',
            data: {
                action: 'analyze',
                symptoms: symptoms.join(', ')
            },
            dataType: 'json',
            success: function(response) {
                $('#analyzeBtn').prop('disabled', false).html('<i class="ti-pulse"></i> Analyze Symptoms');
                
                if (response.status === 'success') {
                    displayDiagnosisResults(response);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                $('#analyzeBtn').prop('disabled', false).html('<i class="ti-pulse"></i> Analyze Symptoms');
                alert('Error analyzing symptoms. Please try again.');
            }
        });
    });
    
    function displayDiagnosisResults(response) {
        let html = '';
        
        if (response.diagnoses && response.diagnoses.length > 0) {
            response.diagnoses.forEach(function(diagnosis, index) {
                html += '<div class="diagnosis-card">';
                html += '<h4>' + (index + 1) + '. ' + diagnosis.disease + '</h4>';
                html += '<p>Confidence: ' + diagnosis.confidence + '%</p>';
                html += '<div class="confidence-bar">';
                html += '<div class="confidence-fill" style="width: ' + diagnosis.confidence + '%"></div>';
                html += '</div>';
                html += '<p style="margin-top: 10px;"><small>Matched ' + diagnosis.matched_symptoms + ' out of ' + diagnosis.total_symptoms + ' typical symptoms</small></p>';
                html += '</div>';
            });
        } else {
            html = '<p>No clear diagnosis found based on the provided symptoms.</p>';
        }
        
        $('#diagnosisContent').html(html);
        $('#recommendations').html('<div class="alert alert-info">' + response.recommendation + '</div>');
        $('#diagnosisResults').slideDown();
        
        // Scroll to results
        $('html, body').animate({
            scrollTop: $('#diagnosisResults').offset().top - 20
        }, 500);
    }
    
    $('#findDoctorBtn').click(function() {
        // Get specialization from top diagnosis
        loadDoctorRecommendations();
    });
    
    function loadDoctorRecommendations() {
        $.ajax({
            url: 'api/doctor_recommendation.php',
            type: 'GET',
            data: { action: 'recommend' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayDoctorRecommendations(response.doctors);
                }
            }
        });
    }
    
    function displayDoctorRecommendations(doctors) {
        let html = '';
        
        if (doctors && doctors.length > 0) {
            doctors.forEach(function(doctor) {
                html += '<div class="panel panel-default" style="margin-bottom: 15px;">';
                html += '<div class="panel-body">';
                html += '<h4>' + doctor.name + '</h4>';
                html += '<p><strong>Specialization:</strong> ' + doctor.specialization + '</p>';
                html += '<p><strong>Fee:</strong> $' + doctor.fee + '</p>';
                html += '<p><strong>Address:</strong> ' + doctor.address + '</p>';
                html += '<p><strong>Availability:</strong> <span class="label label-success">' + doctor.availability_status + '</span></p>';
                html += '<button class="btn btn-primary book-appointment" data-doctor-id="' + doctor.id + '">Book Appointment</button>';
                html += '</div>';
                html += '</div>';
            });
        } else {
            html = '<p>No doctors found. Please try different criteria.</p>';
        }
        
        $('#doctorList').html(html);
        $('#doctorRecommendations').slideDown();
        
        $('.book-appointment').click(function() {
            window.location.href = 'appointment.php?doctor_id=' + $(this).data('doctor-id');
        });
    }
    
    $('#bookAppointmentBtn').click(function() {
        window.location.href = 'appointment.php';
    });
});
</script>

<?php include('inc/footer.php');?>
