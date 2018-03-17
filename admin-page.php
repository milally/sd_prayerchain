<?php
if (isset($_GET['msg'])) {
	echo '<div id="message" class="updated below-h2 pcmessage">';
	switch($_GET['msg']) {
		case 1:
			echo "Prayer Request Approved";
			break;
		case 2:
			echo "Prayer Request Disapproved";
			break;
		case 3:
			echo "Prayer Request Saved";
			break;
		case 4:
			echo "Prayer Requests are being sent.";
			break;
		case 5:
			echo "Settings saved.";
			break;
	}// EOS
	echo "</div>";
}

switch($_REQUEST['action']) {
	case "save":
		savePrayerRequest();	
		wp_redirect("/wp-admin/admin.php?page=prayer-manage&msg=3");
		break;
	case "approve":
		authorizePrayerRequest($_GET['pid'],$_GET['auth']);
		//wp_redirect("/wp-admin/admin.php?page=prayer-manage&msg=1");
		//sendPCMailing();
		//wp_redirect("/wp-admin/admin.php?page=prayer-manage&msg=4");
		wp_redirect("/wp-admin/admin.php?page=prayer-manage&action=sm");
		break;
	case "disapprove":
		disapprovePrayerRequest($_GET['pid'],$_GET['auth']);
		wp_redirect("/wp-admin/admin.php?page=prayer-manage&msg=2");
		break;
	case "edit":
		editPrayerRequest();
		break;
	case "sm":
		forceMailingDelivery();
		break;
	case "save-settings":
		saveAdminSettings();
		break;
	default:
		listPrayerRequests();
		break;		
} // EOS

function listPrayerRequests() {
	global $wpdb;
	
	$past = strtotime("-30 days");
	
	$res = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."sc_prayerchain WHERE pcDate > '".$past."'");
	
	$output = '';
	if ($res) { 
		foreach($res as $pr) {
			
			$actions = "<a href='/wp-admin/admin.php?page=prayer-manage&action=approve&pid=".$pr->pcID."&auth=".$pr->pcAuth."'>Approve & Send</a>&nbsp;|&nbsp;<a href='/wp-admin/admin.php?page=prayer-manage&action=disapprove&pid=".$pr->pcID."&auth=".$pr->pcAuth."'>Disapprove</a>&nbsp;|&nbsp;<a href='/wp-admin/admin.php?page=prayer-manage&action=edit&pid=".$pr->pcID."&auth=".$pr->pcAuth."'>Edit</a>";
			$rc = "";
			switch($pr->pcStatus) {
				case -1:
					$st = "<span style='color:red'>Disapproved</span>";
					break;
				case 1:
					$st = "<span style='color:#ffd409; font-style:italic;'>Awaiting Moderation</span>";
					$rc = "b2b2b2";
					break;
				case 2:
					$st = "<span style='color:#458cc9;'>Approved<br /> (waiting for sending)</span>";
					break;
				case 3:
					$st = "<span style='color:#50a442;'>Sent</span>";
					break;
				
			}
					
			$output .="<tr><td><strong>".date("m/d @ g:ia",$pr->pcDate)."</strong><div class='row-actions'>".$actions."</div></td><td><strong>".$pr->pcName."</strong></td><td>".$pr->pcEmail."</td><td>".$pr->pcPhone."</td><td>".($pr->pcContact ? "Yes":"No")."</td><td>".substr(stripslashes($pr->pcText),0,25)."...</td><td>".$st."</td></tr>";
		}
	} else {
		$output = "<tr><td colspan='7'><em>No Prayer Requests in the system at this time.</em></td></tr>";
	}
	
	//Get current subscribers
	
	$users = get_users(array("meta_key" => "sc_prayerchain_sub", "meta_value" => 1));
	
	$cursubs = "";
	foreach($users as $us) {
		$edit = "<a href='http://www.crossroadsde.com/wp-admin/user-edit.php?user_id=".$us->ID."'>Edit Profile</a>";
		$cursubs .="<tr><td>".$us->display_name."</td><td>".$us->user_email."</td><td>".$edit."</td></tr>";
	} // EOFE	
?>
<div class="wrap">
<h2>Prayerchain Management</h2>
<?php /* 
<p>The next delivery of <span style='color:#458cc9;'>approved</span> prayer requests will be automatically sent in <?php echo getNextDelivery(); ?> minutes. If all of the prayer requests are ready to be sent, just <a href="/wp-admin/admin.php?page=prayer-manage&action=sm">send them now.</a></p>
*/ ?>
<h3>Recent Prayer Requests</h3>
<table cellpadding="6" cellspacing="1" class="wp-list-table widefat fixed posts" style="width:1070px;">
<thead><tr>
	<th width="190">Date</th><th width="170">Name</th><th width="170">Email</th><th width="100">Phone</th><th width="60">Contact?</th><th>Request</th><th width="140">Status</th>
</tr></thead>
<tbody>
<?php echo $output; ?>
</tbody>
</table>

<p>&nbsp;</p>
<p><button onclick="jQuery('#scpc-settings').slideDown();">Edit Settings</button></p>
<div id="scpc-settings" style="margin:10px; padding:10px; background-color:white; border:1px solid #aaaaaa; width:500px; display:none;">
<h3>Prayerchain Settings</h3>
<form action="http://www.crossroadsde.com/wp-admin/admin.php?page=prayer-manage" method="post">
<input type="hidden" name="action" value="save-settings" />
<p><label>Status: <select name="scpc_active">
	<option value="0">Disabled</option>
    <option value="1" <?php if (get_option('scpc_active')) echo 'selected="selected"'; ?> >Enabled</option></select></label><br />
    <small>Keep in mind that even while it is disabled, the menu item remains and people can still subscribe/unsubscribe</small></p>
<p><label>Require user login for submissions: <select name="scpc_require_login">
	<option value="0">Do not require login</option>
    <option value="1" <?php if (get_option('scpc_require_login')) echo 'selected="selected"'; ?> >Require login</option></select></label></p>
<p><label>Email (or mailing list) for prayer request approvals: <input type="text" name="scpc_admin_email" value="<?php echo get_option('scpc_admin_email'); ?>" /></label></p>
<p><input type="submit" value="Save Settings" /></p>
</form>

</div>

<h3>Current Subscribers</h3>
<table cellpadding="6" cellspacing="1" class="wp-list-table widefat fixed posts" style="width:580px;">
<thead><tr>
	<th width="200">Name</th><th width="210">Email</th><th>Edit</th>
</tr></thead>
<tbody>
<?php echo $cursubs; ?>
</tbody>
</table>

</div>	
<?php
} // EOF listPrayerRequests

function editPrayerRequest() {
	
	$prayer = getPrayerRequest($_GET['pid'],$_GET['auth']);
?>
<div class="wrap">
<h2>Edit Prayer Request</h2>
<form name='pc_form' action="/wp-admin/admin.php" method="get">
    <input type="hidden" name="auth" value="<?php echo $_GET['auth']; ?>" />
    <input type="hidden" name="pid" value="<?php echo $_GET['pid']; ?>" />
    <input type="hidden" name="action" value="save" />
    <input type="hidden" name="page" value="prayer-manage" />
    <p><label>Name</label><br />
    <input type="text" name="pc_name" value="<?php echo $prayer['pcName']; ?>" /></p>
    <p><label>Email</label><br />
    <input type="text" name="pc_email" value="<?php echo $prayer['pcEmail']; ?>" /></p>
    <p><label>Prayer Request:</label><br />
    <textarea rows="6" cols="80" name="pc_request"><?php echo stripslashes($prayer['pcText']); ?></textarea></p>
    <p><input type="submit" name="submitform" value="Save Prayer Request" /></p>
    <small>Saving this automatically approves the prayer request. You may save it and then disapprove if for some reason you do not want to send it quite yet.</small>
</form>

<?php	
} // EOF editPrayerRequest

function saveAdminSettings() {
	
	update_option( 'scpc_active', $_POST['scpc_active']);
	update_option( 'scpc_admin_email', $_POST['scpc_admin_email']);
	update_option( 'scpc_require_login', $_POST['scpc_require_login']);
	
	wp_redirect("/wp-admin/admin.php?page=prayer-manage&msg=5");
	
} // EOF saveAdminSettings()

function forceMailingDelivery() {
	
	sendPCMailing();
	
	wp_redirect("/wp-admin/admin.php?page=prayer-manage&msg=4");
	
} // EOF forceMailingDelivery
?>
