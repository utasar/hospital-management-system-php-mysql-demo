<?php
include_once 'config/Database.php';
include_once 'class/User.php';
include_once 'class/VitalSigns.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if(!$user->loggedIn()) {
	header("Location: index.php");
}
include('inc/header.php');
?>
<title>AI Dr. Care - Vital Signs Tracker</title>
<link href="assets/css/paper-dashboard.css" rel="stylesheet"/>
<link href="assets/css/themify-icons.css" rel="stylesheet">
<script src="js/general.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.vital-card {
    padding: 20px;
    margin: 10px 0;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.vital-value {
    font-size: 36px;
    font-weight: bold;
    color: #00796B;
}
.vital-label {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}
.alert-indicator {
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border-left: 4px solid;
}
.alert-danger {
    background: #ffebee;
    border-color: #f44336;
}
.alert-warning {
    background: #fff3e0;
    border-color: #ff9800;
}
.alert-info {
    background: #e3f2fd;
    border-color: #2196f3;
}
.vital-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    text-align: center;
}
</style>
<?php include('inc/container.php');?>
<div class="container" style="background-color:#f4f3ef;">  
	<div class="vital-header">
        <h2><i class="ti-heart"></i> Vital Signs Tracker</h2>
        <p>Monitor and track your health vitals with AI-powered insights</p>
    </div>
	
	<?php include('top_menus.php'); ?>	
	<br>
	
	<div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading" style="background:#667eea;color:white;">
                    <h4><i class="ti-plus"></i> Record Vital Signs</h4>
                </div>
                <div class="panel-body">
                    <form id="vitalSignsForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Temperature (°C)</label>
                                    <input type="number" step="0.1" class="form-control" id="temperature" name="temperature" placeholder="e.g., 37.0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Heart Rate (bpm)</label>
                                    <input type="number" class="form-control" id="heart_rate" name="heart_rate" placeholder="e.g., 72">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Blood Pressure - Systolic (mmHg)</label>
                                    <input type="number" class="form-control" id="bp_systolic" name="bp_systolic" placeholder="e.g., 120">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Blood Pressure - Diastolic (mmHg)</label>
                                    <input type="number" class="form-control" id="bp_diastolic" name="bp_diastolic" placeholder="e.g., 80">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Oxygen Saturation (%)</label>
                                    <input type="number" step="0.1" class="form-control" id="oxygen_saturation" name="oxygen_saturation" placeholder="e.g., 98">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Respiratory Rate (per min)</label>
                                    <input type="number" class="form-control" id="respiratory_rate" name="respiratory_rate" placeholder="e.g., 16">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Weight (kg)</label>
                                    <input type="number" step="0.1" class="form-control" id="weight" name="weight" placeholder="e.g., 70.5">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Height (meters)</label>
                                    <input type="number" step="0.01" class="form-control" id="height" name="height" placeholder="e.g., 1.75">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional notes or observations..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg btn-block">
                            <i class="ti-save"></i> Save Vital Signs
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="ti-bar-chart"></i> Vital Signs History</h4>
                </div>
                <div class="panel-body">
                    <canvas id="vitalSignsChart" height="100"></canvas>
                    <div id="vitalSignsHistory" style="margin-top: 20px;"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h4><i class="ti-info"></i> Latest Readings</h4>
                </div>
                <div class="panel-body" id="latestReadings">
                    <p class="text-center"><em>No readings yet. Record your first vital signs!</em></p>
                </div>
            </div>
            
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h4><i class="ti-alert"></i> Health Alerts</h4>
                </div>
                <div class="panel-body" id="healthAlerts">
                    <p class="text-center"><em>No alerts</em></p>
                </div>
            </div>
            
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4><i class="ti-help"></i> Normal Ranges</h4>
                </div>
                <div class="panel-body">
                    <small>
                    <strong>Temperature:</strong> 36.5-37.5°C<br>
                    <strong>Heart Rate:</strong> 60-100 bpm<br>
                    <strong>Blood Pressure:</strong> 90/60 - 120/80 mmHg<br>
                    <strong>Oxygen Sat:</strong> 95-100%<br>
                    <strong>Respiratory Rate:</strong> 12-20 per min<br>
                    <strong>BMI:</strong> 18.5-24.9
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let vitalChart = null;
    
    // Load latest vital signs
    loadLatestVitalSigns();
    loadVitalSignsHistory();
    
    $('#vitalSignsForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = {
            action: 'save',
            temperature: $('#temperature').val(),
            bp_systolic: $('#bp_systolic').val(),
            bp_diastolic: $('#bp_diastolic').val(),
            heart_rate: $('#heart_rate').val(),
            respiratory_rate: $('#respiratory_rate').val(),
            oxygen_saturation: $('#oxygen_saturation').val(),
            weight: $('#weight').val(),
            height: $('#height').val(),
            notes: $('#notes').val()
        };
        
        // Calculate BMI if weight and height are provided
        if (formData.weight && formData.height) {
            formData.bmi = (formData.weight / (formData.height * formData.height)).toFixed(2);
        }
        
        $.ajax({
            url: 'api/vital_signs_api.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert('Vital signs recorded successfully!');
                    $('#vitalSignsForm')[0].reset();
                    loadLatestVitalSigns();
                    loadVitalSignsHistory();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error saving vital signs. Please try again.');
            }
        });
    });
    
    function loadLatestVitalSigns() {
        $.ajax({
            url: 'api/vital_signs_api.php',
            type: 'GET',
            data: { action: 'latest' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    displayLatestReadings(response.data);
                    displayHealthAlerts(response.alerts);
                }
            }
        });
    }
    
    function loadVitalSignsHistory() {
        $.ajax({
            url: 'api/vital_signs_api.php',
            type: 'GET',
            data: { action: 'history', limit: 10 },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    displayVitalSignsHistory(response.data);
                    updateChart(response.data);
                }
            }
        });
    }
    
    function displayLatestReadings(data) {
        let html = '';
        
        if (data.temperature) {
            html += '<div class="vital-card">';
            html += '<div class="vital-value">' + data.temperature + '°C</div>';
            html += '<div class="vital-label">Temperature</div>';
            html += '</div>';
        }
        
        if (data.blood_pressure_systolic && data.blood_pressure_diastolic) {
            html += '<div class="vital-card">';
            html += '<div class="vital-value">' + data.blood_pressure_systolic + '/' + data.blood_pressure_diastolic + '</div>';
            html += '<div class="vital-label">Blood Pressure (mmHg)</div>';
            html += '</div>';
        }
        
        if (data.heart_rate) {
            html += '<div class="vital-card">';
            html += '<div class="vital-value">' + data.heart_rate + '</div>';
            html += '<div class="vital-label">Heart Rate (bpm)</div>';
            html += '</div>';
        }
        
        if (data.oxygen_saturation) {
            html += '<div class="vital-card">';
            html += '<div class="vital-value">' + data.oxygen_saturation + '%</div>';
            html += '<div class="vital-label">Oxygen Saturation</div>';
            html += '</div>';
        }
        
        if (data.bmi) {
            html += '<div class="vital-card">';
            html += '<div class="vital-value">' + data.bmi + '</div>';
            html += '<div class="vital-label">BMI</div>';
            html += '</div>';
        }
        
        $('#latestReadings').html(html);
    }
    
    function displayHealthAlerts(alerts) {
        if (!alerts || alerts.length === 0) {
            $('#healthAlerts').html('<p class="text-center"><em>All vitals within normal range!</em></p>');
            return;
        }
        
        let html = '';
        alerts.forEach(function(alert) {
            html += '<div class="alert-indicator alert-' + alert.type + '">';
            html += '<strong>' + alert.parameter + ':</strong> ' + alert.value + '<br>';
            html += '<small>' + alert.message + '</small>';
            html += '</div>';
        });
        
        $('#healthAlerts').html(html);
    }
    
    function displayVitalSignsHistory(history) {
        let html = '<table class="table table-striped"><thead><tr><th>Date</th><th>Temp</th><th>BP</th><th>HR</th><th>SpO2</th></tr></thead><tbody>';
        
        history.forEach(function(record) {
            html += '<tr>';
            html += '<td>' + new Date(record.recorded_at).toLocaleString() + '</td>';
            html += '<td>' + (record.temperature || '-') + '</td>';
            html += '<td>' + (record.blood_pressure_systolic && record.blood_pressure_diastolic ? record.blood_pressure_systolic + '/' + record.blood_pressure_diastolic : '-') + '</td>';
            html += '<td>' + (record.heart_rate || '-') + '</td>';
            html += '<td>' + (record.oxygen_saturation || '-') + '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table>';
        $('#vitalSignsHistory').html(html);
    }
    
    function updateChart(history) {
        let labels = history.map(h => new Date(h.recorded_at).toLocaleDateString()).reverse();
        let tempData = history.map(h => h.temperature || null).reverse();
        let bpSystolicData = history.map(h => h.blood_pressure_systolic || null).reverse();
        let heartRateData = history.map(h => h.heart_rate || null).reverse();
        
        if (vitalChart) {
            vitalChart.destroy();
        }
        
        let ctx = document.getElementById('vitalSignsChart').getContext('2d');
        vitalChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Temperature (°C)',
                        data: tempData,
                        borderColor: '#FF6384',
                        fill: false
                    },
                    {
                        label: 'BP Systolic (mmHg)',
                        data: bpSystolicData,
                        borderColor: '#36A2EB',
                        fill: false
                    },
                    {
                        label: 'Heart Rate (bpm)',
                        data: heartRateData,
                        borderColor: '#4BC0C0',
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    }
});
</script>

<?php include('inc/footer.php');?>
