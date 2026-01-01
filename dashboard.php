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
<title>AI Dr. Care - Dashboard</title>
<link href="assets/css/paper-dashboard.css" rel="stylesheet"/>
<link href="assets/css/themify-icons.css" rel="stylesheet">
<script src="js/general.js"></script>
<style>
.ai-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
}
.ai-card {
    background: linear-gradient(135deg, #00796B 0%, #004D40 100%);
    color: white;
}
.ai-card .footer {
    border-top-color: rgba(255,255,255,0.2) !important;
}
.ai-card .stats {
    color: white !important;
}
</style>
<?php include('inc/container.php');?>
<div class="container" style="background-color:#f4f3ef;">  
	<div class="ai-banner">
        <h2>🤖 AI Dr. Care - Intelligent Hospital Management System</h2>
        <p>Experience AI-powered healthcare with advanced symptom analysis, vital signs monitoring, and smart doctor recommendations</p>
    </div>
	
	<?php include('top_menus.php'); ?>	
	<br>	
	<div class="row">
		<div class="col-lg-3 col-sm-6">
			<div class="card">
				<div class="content">
					<div class="row">
						<div class="col-xs-5">
							<div class="icon-big icon-warning text-center">
								<i class="ti-user"></i>
							</div>
						</div>
						<div class="col-xs-7">
							<div class="numbers">10
								<p><strong>Doctors</strong></p>										
							</div>
						</div>
					</div>
					<a href="doctor.php">
						<div class="footer">
						<hr />
						<div class="stats">
							<i class="ti-arrow-right"></i>View
						</div>
					</div>
				</a>
				</div>
			</div>
		</div>				
		<div class="col-lg-3 col-sm-6">
			<div class="card">
				<div class="content">
					<div class="row">
						<div class="col-xs-5">
							<div class="icon-big icon-info text-center">
								<i class="ti-user"></i>
							</div>
						</div>
						<div class="col-xs-7">
							<div class="numbers">45
								<p><strong>Patients</strong></p>									   
							</div>
						</div>
					</div>
					<a href="patient.php">
						<div class="footer">
						<hr />
						<div class="stats">
							<i class="ti-arrow-right"></i>View
						</div>
					</div>
				</a>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-sm-6">
			<div class="card">
				<div class="content">
					<div class="row">
						<div class="col-xs-5">
							<div class="icon-big icon-success text-center">
								<i class="ti-user"></i>
							</div>
						</div>
						<div class="col-xs-7">
							<div class="numbers">12
								<p><strong>Nurses</strong></p>										
							</div>
						</div>
					</div>
					<a href="#">
						<div class="footer">
						<hr />
						<div class="stats">
							<i class="ti-arrow-right"></i>View
						</div>
					</div>
				</a>
				</div>
			</div>
		</div>
	</div>
		<div class="row">
		<div class="col-lg-3 col-sm-6">
			<div class="card">
				<div class="content">
					<div class="row">
						<div class="col-xs-5">
							<div class="icon-big icon-warning text-center">
								<i class="ti-agenda"></i>
							</div>
						</div>
						<div class="col-xs-7">
							<div class="numbers">123
								<p><strong>Appointment</strong></p>									   
							</div>
						</div>
					</div>
					<a href="appointment.php">
						<div class="footer">
						<hr />
						<div class="stats">
							<i class="ti-arrow-right"></i>View
						</div>
					</div>
				</a>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-sm-6">
			<div class="card">
				<div class="content">
					<div class="row">
						<div class="col-xs-5">
							<div class="icon-big icon-success text-center">
								<i class="ti-money"></i>
							</div>
						</div>
						<div class="col-xs-7">
							<div class="numbers">56
								<p><strong>Payments</strong></p>										
							</div>
						</div>
					</div>
					<a href="#">
						<div class="footer">
						<hr />
						<div class="stats">
							<i class="ti-arrow-right"></i>View
						</div>
					</div>
				</a>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-sm-6">
			<div class="card">
				<div class="content">
					<div class="row">
						<div class="col-xs-5">
							<div class="icon-big icon-danger text-center">
								<i class="ti-archive"></i>
							</div>
						</div>
						<div class="col-xs-7">
							<div class="numbers">112
								<p><strong>Reports</strong></p>										
							</div>
						</div>
					</div>
					<a href="#">
						<div class="footer">
						<hr />
						<div class="stats">
							<i class="ti-arrow-right"></i>View
						</div>
					</div>
				</a>
				</div>
			</div>
		</div>
	</div>
	
	<!-- AI Features Row -->
	<div class="row" style="margin-top: 20px;">
		<div class="col-lg-4 col-sm-6">
			<div class="card ai-card">
				<div class="content">
					<div class="row">
						<div class="col-xs-5">
							<div class="icon-big text-center">
								<span style="font-size: 48px;">🤖</span>
							</div>
						</div>
						<div class="col-xs-7">
							<div class="numbers">AI
								<p><strong>Symptom Checker</strong></p>									   
							</div>
						</div>
					</div>
					<a href="ai_symptom_checker.php">
						<div class="footer">
						<hr />
						<div class="stats">
							<i class="ti-arrow-right"></i>Analyze Symptoms
						</div>
					</div>
				</a>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-sm-6">
			<div class="card ai-card">
				<div class="content">
					<div class="row">
						<div class="col-xs-5">
							<div class="icon-big text-center">
								<span style="font-size: 48px;">📊</span>
							</div>
						</div>
						<div class="col-xs-7">
							<div class="numbers">Track
								<p><strong>Vital Signs</strong></p>										
							</div>
						</div>
					</div>
					<a href="vital_signs.php">
						<div class="footer">
						<hr />
						<div class="stats">
							<i class="ti-arrow-right"></i>Monitor Health
						</div>
					</div>
				</a>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-sm-6">
			<div class="card ai-card">
				<div class="content">
					<div class="row">
						<div class="col-xs-5">
							<div class="icon-big text-center">
								<span style="font-size: 48px;">🔒</span>
							</div>
						</div>
						<div class="col-xs-7">
							<div class="numbers">GDPR
								<p><strong>Privacy Center</strong></p>										
							</div>
						</div>
					</div>
					<a href="privacy.php">
						<div class="footer">
						<hr />
						<div class="stats">
							<i class="ti-arrow-right"></i>Manage Data
						</div>
					</div>
				</a>
				</div>
			</div>
		</div>				
	</div>		
</div>
 <?php include('inc/footer.php');?>
