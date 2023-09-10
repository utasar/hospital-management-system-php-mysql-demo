$(document).ready(function(){
	var urlPath = window.location.pathname,
    urlPathArray = urlPath.split('.'),
    tabId = urlPathArray[0].split('/').pop();
	$('#dashboard, #doctor, #patient, #nurse, #appointment, #payments, #reports').removeClass('active');	
	$('#'+tabId).addClass('active');
});