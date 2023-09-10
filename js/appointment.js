$(document).ready(function(){	

	var appointmentRecords = $('#appointmentListing').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,		
		"bFilter": false,
		'serverMethod': 'post',		
		"order":[],
		"ajax":{
			url:"appointment_action.php",
			type:"POST",
			data:{action:'listAppointment'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[0, 8, 9, 10],
				"orderable":false,
			},
		],
		"pageLength": 10
	});	
	
	$('#createAppointment').click(function(){
		$('#appointmentModal').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#appointmentForm')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Create Appointment");
		$('#action').val('createAppointment');
		$('#save').val('Save');
	});		
	
	$("#appointmentListing").on('click', '.update', function(){
		var id = $(this).attr("id");
		var action = 'getAppointment';
		$.ajax({
			url:'appointment_action.php',
			method:"POST",
			data:{id:id, action:action},
			dataType:"json",
			success:function(data){				
				$("#appointmentModal").on("shown.bs.modal", function () { 
					$('#id').val(data.id);
					$('#doctor_name').val(data.d_id);
					$('#specialization').val(data.s_id);
					$('#fee').val(data.consultancy_fee);
					$('#appointment_date').val(data.appointment_date);	
					$('#appointment_time').val(data.appointment_time);					
					$('#status').val(data.status);											
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit Appointment");
					$('#action').val('updateAppointment');
					$('#save').val('Save');
				}).modal({
					backdrop: 'static',
					keyboard: false
				});			
			}
		});
	});
	
	$("#appointmentModal").on('submit','#appointmentForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"appointment_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#appointmentForm')[0].reset();
				$('#appointmentModal').modal('hide');				
				$('#save').attr('disabled', false);
				appointmentRecords.ajax.reload();
			}
		})
	});	
	
	$("#appointmentListing").on('click', '.view', function(){
		var id = $(this).attr("id");
		var action = 'getAppointment';
		$.ajax({
			url:'appointment_action.php',
			method:"POST",
			data:{id:id, action:action},
			dataType:"json",
			success:function(data){				
				$("#appointmentDetails").on("shown.bs.modal", function () { 					
					$('#a_patient').html(data.patient_name);
					$('#a_doctor').html(data.doctor_name);
					$('#a_special').html(data.specialization);
					$('#a_fee').html(data.consultancy_fee);				
					$('#a_time').html(data.appointment_date+" "+data.appointment_time);
					$('#a_status').html(data.status);									
				}).modal();			
			}
		});
	});
	
	$("#appointmentListing").on('click', '.delete', function(){
		var id = $(this).attr("id");		
		var action = "deleteAppointment";
		if(confirm("Are you sure you want to delete this appointment?")) {
			$.ajax({
				url:"appointment_action.php",
				method:"POST",
				data:{id:id, action:action},
				success:function(data) {					
					appointmentRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	
	
});