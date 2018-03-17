// JavaScript Document
function ckpcform() {
	
	jQuery("#pc_form_error").hide();
	fd = jQuery("form[name='pc_form']").serializeArray();
	fail = false;
	
	if (fd[0]['value'].length < 4) {
		er = "Please type your full name";
		fail = true;
	}
	
	if (fd[5]['value'].length > 0) {
		er = "You just might not be human. Sorry, for some reason you cannot fill out this form. ";
		fail = true;
	}
	
	if (!validateEmail(fd[1]['value'])) {
		er = "Please enter a valid email address.";
		fail = true;
	}
	
	if (fail) {
		jQuery("#pc_form_error").html(er).fadeIn();
		jQuery('html, body').animate({
	        scrollTop: (jQuery("#pc_form_container").offset().top - 20)
    	}, 500);
		return;
	}
	
	submitpcform();
	
} // EOF ckpcform()

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 

function submitpcform() {
	
	jQuery("input[name='submitform']").attr("disabled","disabled").val("Please Wait...");
	
	jQuery(".pc_form_box").slideUp();
	jQuery(".pc_waiting").slideDown();
	jQuery('html, body').animate({
		scrollTop: (jQuery("#pc_form_container").offset().top - 20)
	});
	
	jQuery.ajax({
		cache: false, 
		dataType: 'json',
		url: "/wp-admin/admin-ajax.php",
		data: jQuery("form[name='pc_form']").serialize()+"&action=pc_submit_request&auth=ihu17dfg1",
		success: function(data) {
			jQuery(".pc_waiting").slideUp();
			jQuery(".pc_submitted").slideDown();
		},
		error: function(data) {
			jQuery(".pc_waiting").slideUp();
			jQuery(".pc_error").slideDown(function() {jQuery(".pc_error").delay(3000).slideUp(function() {jQuery(".pc_form_box").slideDown();})});
		}
	});
	
	jQuery(".pc_form_box input[name='submitform']").removeAttr("disabled").val("Send Prayer Request");
	
} // EOF submitpcform()
