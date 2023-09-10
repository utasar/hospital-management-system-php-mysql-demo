<h3><?php if($_SESSION["userid"]) { echo "Logged in : ".ucfirst($_SESSION["name"]); } ?> | <a href="logout.php">Logout</a> </h3><br>
<p><strong>Welcome <?php echo ucfirst($_SESSION["role"]); ?></strong></p>	
<ul class="nav nav-tabs">
	
	<?php if($_SESSION["role"] == 'admin') { ?>
		<li id="dashboard" class="active"><a href="dashboard.php">Dashboard</a></li>
		<li id="doctor"><a href="doctor.php">Doctors</a></li>
		<li id="patient"><a href="patient.php">Patients</a></li> 
		<li id="appointment"><a href="appointment.php">Appointments</a></li>		
	<?php } ?>
	
	<?php if($_SESSION["role"] == 'doctor') { ?>
		<li id="doctor" class="active"><a href="doctor.php">Doctor</a></li>	
		<li id="patient" class="active"><a href="patient.php">Patient</a></li>
		<li id="appointment"><a href="appointment.php">Appointments</a></li>		
	<?php } ?>
	
	<?php if($_SESSION["role"] == 'patient') { ?>
		<li id="patient" class="active"><a href="patient.php">Patient</a></li>
		<li id="appointment"><a href="appointment.php">Appointments</a></li>		
	<?php } ?>

</ul>