<?php
/**
 * Prayerchain Page Template
 */
 // Header
//get_template_part( 'header', 'page' ); // this makes $content_title available
// Get requested info
$authcode = $_GET['auth'];
$pid = $_GET['pid'];
$action = $_GET['action'];

if ($pid > 0 && $authcode != '') {
	$prayer = getPrayerRequest($pid,$authcode);
} else {
	exit("failed to load prayerchain api");
}

if ($action == 4) {
	//submitted form after editing. 
	savePrayerRequest();
	$prayer = getPrayerRequest($pid,$authcode);
	$action = 1;
}

$min = getNextDelivery();

?>
		
<?php while ( have_posts() ) : the_post(); ?>

<div id="content">

	<div id="content-inner">

	<h1>Prayerchain Management</h1>
    
    	<?php
		
		if ($prayer['pcStatus'] > 1) {
			//someone else got to it first. 
			// Approved
		?>
        <h2>Already Approved!</h2>
        <p>Thanks for your submission, but this prayer request is already approved and has been sent off for delivery. </p>
        <h3>Prayer Request:</h3>
        <strong>Name:</strong> <?php echo $prayer['pcName']; ?><br />
        <strong>Request:</strong> <?php echo $prayer['pcText']; ?>
        <p>&nbsp;</p>        
        <?php
		} else if ($prayer['pcStatus'] == -1) {
			// Disapproved
		?>
        <h2>Shot down!</h2>
        <p>Thanks for your submission, but this prayer request is already disapproved. You can still edit it though in the admin control panel.</p>
        <h3>Prayer Request:</h3>
        <strong>Name:</strong> <?php echo $prayer['pcName']; ?><br />
        <strong>Request:</strong> <?php echo $prayer['pcText']; ?>
        <p>&nbsp;</p>    
		<?php	
		} else if ($action == 1) {
			authorizePrayerRequest($pid,$authcode);
			sendPCMailing();			
		?>
        <h2>Authorized</h2>
        <p>The prayer request has been authorized for delivery. The next prayerchain delivery will be <em>automatically</em> sent in <?php echo $min; ?> minutes.</p>
         <h3>Prayer Request:</h3>
        <strong>Name:</strong> <?php echo $prayer['pcName']; ?><br />
        <strong>Request:</strong> <?php echo $prayer['pcText']; ?>
        <p>&nbsp;</p>   
        <?php 
		} else if ($action == 2) {
		?>
        <h2>Edit Prayer Request</h2>
        <p>Please edit the request and click submit. Once submitted, the prayer request will be authorized for delivery. The next prayerchain delivery will be <em>automatically</em> sent in <?php echo $min; ?> minutes.</p>
        <div id="pc_form_container">
        <div class="pc_form_box">
            <form name='pc_form' action="/prayerchain-api" method="get">
            <input type="hidden" name="auth" value="<?php echo $authcode; ?>" />
            <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
            <input type="hidden" name="action" value="4" />
            <label>Name</label>
            <input type="text" name="pc_name" value="<?php echo $prayer['pcName']; ?>" />
            <label>Email</label>
            <input type="text" name="pc_email" value="<?php echo $prayer['pcEmail']; ?>" />
            <label>Prayer Request:</label>
            <textarea rows="10" name="pc_request"><?php echo $prayer['pcText']; ?></textarea><br />
            <input type="submit" name="submitform" value="Submit Prayer Request" /><br />
		</form>
        </div>
        </div>
		<?php
		} else if ($action == 3) {
			// disapprove
			disapprovePrayerRequest($pid,$authcode);
			
		?>
        <h2>Disapproved</h2>
        <p>The prayer request has been disapproved. It can still be edited in the admin control panel and then approved, but at this point, it is not going anywhere.</p>
        <h3>Prayer Request:</h3>
        <strong>Name:</strong> <?php echo $prayer['pcName']; ?><br />
        <strong>Request:</strong> <?php echo $prayer['pcText']; ?>
        <p>&nbsp;</p>   
        <?php 
		}
		?>	
        <p>&nbsp;</p><p>&nbsp;</p>
        <p><a href="/wp-admin">Login to the control panel</a> to access the control panel for all prayer requests.</p>
        
	</div>

</div>

<?php endwhile; ?>

<?php
get_footer();
?>
