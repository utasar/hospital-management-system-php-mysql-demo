$(document).ready(function(){	

	var clientRecords = $('#projectListing').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,		
		"bFilter": false,
		'serverMethod': 'post',		
		"order":[],
		"ajax":{
			url:"project_action.php",
			type:"POST",
			data:{action:'listProjects'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[0, 7, 8, 9],
				"orderable":false,
			},
		],
		"pageLength": 10
	});	
	
	$('#addProject').click(function(){
		$('#projectModal').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#projetForm')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add Project");
		$('#action').val('addProject');
		$('#save').val('Save');
	});		
	
	$("#projectListing").on('click', '.update', function(){
		var id = $(this).attr("id");
		var action = 'getProject';
		$.ajax({
			url:'project_action.php',
			method:"POST",
			data:{id:id, action:action},
			dataType:"json",
			success:function(data){				
				$("#projectModal").on("shown.bs.modal", function () { 
					$('#id').val(data.id);
					$('#project').val(data.project_name);
					$('#client').val(data.client_id);
					$('#project_manager').val(data.project_manager_id);
					$('#active').val(data.active);				
					$('#hourly_rate').val(data.hourly_rate);
					$('#budget').val(data.budget);
					$('#project_status').val(data.status_id);						
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit Project");
					$('#action').val('updateProject');
					$('#save').val('Save');
				}).modal({
					backdrop: 'static',
					keyboard: false
				});			
			}
		});
	});
	
	$("#projectModal").on('submit','#projetForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"project_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#projetForm')[0].reset();
				$('#projectModal').modal('hide');				
				$('#save').attr('disabled', false);
				clientRecords.ajax.reload();
			}
		})
	});	
	
	$("#projectListing").on('click', '.delete', function(){
		var id = $(this).attr("id");		
		var action = "deleteProject";
		if(confirm("Are you sure you want to delete this project?")) {
			$.ajax({
				url:"project_action.php",
				method:"POST",
				data:{id:id, action:action},
				success:function(data) {					
					clientRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	
	
	$("#projectListing").on('click', '.view', function(){
		var id = $(this).attr("id");
		var action = 'getProjectDetails';
		$.ajax({
			url:'project_action.php',
			method:"POST",
			data:{id:id, action:action},
			dataType:"json",
			success:function(data){				
				$("#projectDetails").on("shown.bs.modal", function () {
					$('#project_id').html(data.id);
					$('#dproject').html(data.project_name);
					$('#dclient').html(data.client);
					$('#dmanager').html(data.first_name+" "+data.last_name);
					$('#dactive').html(data.active);				
					$('#dhourly_rate').html(data.hourly_rate);
					$('#dbudget').html(data.budget);
					$('#dstatus').html(data.status);				
				}).modal();			
			}
		});
	});
	
});