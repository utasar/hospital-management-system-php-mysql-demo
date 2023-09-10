<?php
include_once 'config/Database.php';
include_once 'class/User.php';
include_once 'class/Appointment.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$appointment = new Appointment($db);
if(!$user->loggedIn()) {
	header("Location: index.php");
}
include('inc/header.php');
?>
<title>webdamn.com : Demo Project Management Sytem with PHP and MySQL</title>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/appointment.js"></script>	
<script src="js/general.js"></script>
<?php include('inc/container.php');?>
<div class="container" style="background-color:#f4f3ef;">  
	<h2>Hospital Management System</h2>	
	<br>
	<?php include('top_menus.php'); ?>	
	<div> 	
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-10">
					<h3 class="panel-title"></h3>
				</div>
				<?php if($_SESSION["role"] == 'patient') { ?>
				<div class="col-md-2" align="right">
					<button type="button" id="createAppointment" class="btn btn-info" title="Create Appointment"><span class="glyphicon glyphicon-plus"></span></button>
				</div>
				<?php } ?>
			</div>
		</div>
		<table id="appointmentListing" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Patient</th>
					<th>Doctor</th>					
					<th>Specialization</th>	
					<th>Fee</th>	
					<th>Apointment Time</th>
					<th>Apointment Date</th>
					<th>Status</th>
					<th></th>
					<th></th>	
					<th></th>					
				</tr>
			</thead>
		</table>
	</div>
	
	<div id="appointmentModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" id="appointmentForm">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Edit Record</h4>
    				</div>
    				<div class="modal-body">
						<div class="form-group"
							<label for="doctor" class="control-label">Doctor</label>
							<select class="form-control" id="doctor_name" name="doctor_name"/>
							<?php 
							$result = $appointment->doctorList();
							while ($doctor = $result->fetch_assoc()) { 	
							?>
								<option value="<?php echo $doctor['id']; ?>"><?php echo $doctor['name']; ?></option>							
							<?php } ?>
							</select>			
						</div>
						<div class="form-group">
							<label for="specialization" class="control-label">Specialization</label>							
							<select class="form-control" id="specialization" name="specialization"/>
							<?php 
							$result = $appointment->specializationList();
							while ($specialization = $result->fetch_assoc()) { 	
							?>
								<option value="<?php echo $specialization['id']; ?>"><?php echo ucfirst($specialization['specialization']); ?></option>							
							<?php } ?>
							</select>								
						</div>	  
						<div class="form-group">
							<label for="fee" class="control-label">Fee</label>							
							<input type="text" class="form-control" id="fee" name="fee" placeholder="fee">							
						</div>	   	
						<div class="form-group">
							<label for="appointment_date" class="control-label">Appointment Date</label>							
							<input type="text" class="form-control"  id="appointment_date" name="appointment_date" placeholder="dd-mm-yyyy">				
						</div>		
						<div class="form-group">
							<label for="appointment_time" class="control-label">Appointment Time</label>							
							<input type="text" class="form-control" id="appointment_time" name="appointment_time" placeholder="hh:mm am/pm">			
						</div>
						<div class="form-group">
							<label for="description" class="control-label">Active</label>							
							<select class="form-control" id="status" name="status"/>
								<option value="Active">Active</option>
								<option value="Completed">Completed</option>
								<option value="Cancelled">Cancelled</option>
							</select>
						</div>						
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="id" id="id" />
    					<input type="hidden" name="action" id="action" value="" />
    					<input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
    					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    				</div>
    			</div>
    		</form>
    	</div>
    </div>
	
	<div id="appointmentDetails" class="modal fade">
    	<div class="modal-dialog">    		
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="fa fa-plus"></i> Appointment Details</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="name" class="control-label">Patient Name:</label>
						<span id="a_patient"></span>	
					</div>
					<div class="form-group">
						<label for="p_gender" class="control-label">Doctor:</label>				
						<span id="a_doctor"></span>							
					</div>	   	
					<div class="form-group">
						<label for="p_age" class="control-label">Specialization:</label>							
						<span id="a_special"></span>								
					</div>	
					<div class="form-group">
						<label for="a_fee" class="control-label">Fee:</label>							
						<span id="a_fee"></span>								
					</div>	
					<div class="form-group">
						<label for="phone" class="control-label">Appoint Date Time:</label>							
						<span id="a_time"></span>					
					</div>			
					<div class="form-group">
						<label for="a_status" class="control-label">Status:</label>							
						<span id="a_status"></span>							
					</div>
					
				</div>    				
			</div>    		
    	</div>
    </div>
	
</div>
 <?php include('inc/footer.php');?>
