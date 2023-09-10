$(document).ready(function(){	

	var taskRecords = $('#taskListing').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,		
		"bFilter": false,
		'serverMethod': 'post',		
		"order":[],
		"ajax":{
			url:"tasks_action.php",
			type:"POST",
			data:{action:'listTasks'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[0],
				"orderable":false,
				"className": 'details-control',
				
			}		
		],
		"pageLength": 10
	});	
	
	
	$('#taskListing tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = taskRecords.row( tr ); 
        if ( row.child.isShown() ) {            
            row.child.hide();
            tr.removeClass('shown');
        } else { 			
			var id = $(this).find('span').attr('id');		
			var action = "getHours";
			$.ajax({
				url:"tasks_action.php",
				method:"POST",
				dataType:"json",
				data:{id:id, action:action},
				success:function(data) {					
					row.child( format(id, data)).show();
					tr.addClass('shown');
				}
			}); 
            
        }
    });
	
	
	$("#taskListing").on('click', '.addHours', function(){
		var id = $(this).attr("data-id");		
		$("#hoursModal").on("shown.bs.modal", function () { 
			$('#id').val(id);
			$('#action').val('addHours');
			$('#hoursForm')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Add Hours");		
			$('#save').val('Save');
		}).modal({
			backdrop: 'static',
			keyboard: false
		});			
	});	

	
	$(document).on('submit','#hoursForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"tasks_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#hoursForm')[0].reset();
				$('#hoursModal').modal('hide');				
				$('#save').attr('disabled', false);	
				clientRecords.ajax.reload();
			}
		})
	});
	
});

function format (taskId, data) {   
	var message = '<table class="table table-striped">';
	message += '<thead><tr><th>Date</th><th>Time</th><th>Work Completed</th></tr></thead><tbody>';
	data.forEach( function( item ) {
		message += '<tr>'+
            '<td>'+item.date+'</td>'+
            '<td>'+item.time+'</td>'+
			'<td>'+item.work_completed+'</td>'+
        '</tr>';
    });
	message += '</tbody></table>';
	message += '<div>';
	message += '&nbsp;&nbsp;<a><span class="glyphicon glyphicon-plus addHours" data-id="'+taskId+'" title="Add Hours Details"></span></a>&nbsp;&nbsp;&nbsp;';		
	message += '</div>';
	return message;    
}