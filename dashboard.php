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
<title>webdamn.com : Demo Hospital Management System with PHP and MySQL</title>
<link href="assets/css/paper-dashboard.css" rel="stylesheet"/>
<link href="assets/css/themify-icons.css" rel="stylesheet">
<script src="js/general.js"></script>
<?php include('inc/container.php');?>
<div class="container" style="background-color:#f4f3ef;">  
	<h2>Hospital Management System</h2>	
	<br>
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
</div>
 <?php include('inc/footer.php');?>
