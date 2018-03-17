<?php
/**
 * Default Page Template
 */
// Header
//get_template_part( 'header', 'page' ); // this makes $content_title available
?>
		
<?php while ( have_posts() ) : the_post(); ?>

<div id="content">

	<div id="content-inner">

		<h1>Prayerchain</h1>
		<?php
		if ($_GET['ac'] == "unsubscribe") {
			// User came to this page to unsubscribe
		?>
        <p>To unsubscribe from the prayer chain list, please enter the email address registered with our system.</p>
        <form action="/prayerchain-manage" method="post">
        	<input type="hidden" name="ac" value="removeuser" />
            <div style="margin:8px; background-color:#dddddd; border:1px solid #b8b8b8; border-radius:6px; padding:10px; width:500px;">
            <label>Email: <input type="text" size="30" name="em" /></label> <input type="submit" name="unsub" value="Unsubscribe" /><br /><span style="font-size:11px; color:#7e7e7e;">Note: Your unsubscription takes immediate effect.</span></div></form>
         <?php
		 
		} else if($_REQUEST['ac'] == "removeuser") {
			// User entered email to be removed from list
			$user = get_user_by('email',$_REQUEST['em']);
			
			if ($user) {
				update_user_meta( $user->ID, 'sc_prayerchain_sub', 0);
				?>
                <div style="margin:8px; background-color:#faf8ca; border:2px solid #58af41; border-radius:6px; padding:10px; width:500px;"><h3>Success. Your email has been unsubscribed.</h3></div>
                
                <?php
			} else { // Email not found.
				?>
                <div style="margin:8px; background-color:#faf8ca; border:2px solid #f93f31; border-radius:6px; padding:10px; width:500px;"><h3>Oops. The email address is incorrect.</h3></div>
                <p>To unsubscribe from the prayer chain list, please enter the email address registered with our system.</p>
        <form action="/prayerchain-manage" method="post">
        	<input type="hidden" name="ac" value="removeuser" />
            <div style="margin:8px; background-color:#dddddd; border:1px solid #b8b8b8; border-radius:6px; padding:10px; width:500px;">
            <label>Email: <input type="text" size="30" name="em" /></label> <input type="submit" name="unsub" value="Unsubscribe" /><br /><span style="font-size:11px; color:#7e7e7e;">Note: Your unsubscription takes immediate effect.</span></div></form>
            <?php
			} // EF doesn't exist
                			
		} else if($_REQUEST['ac'] == "adduser") {
				$user = get_user_by('email',$_REQUEST['em']);
				update_user_meta( $user->ID, 'sc_prayerchain_sub', 1);
				?>
                <div style="margin:8px; background-color:#faf8ca; border:2px solid #58af41; border-radius:6px; padding:10px; width:500px;"><h3>Success. Your email has been subscribed.</h3></div>
                
                <?php
								
		} else {
			
			if (is_user_logged_in() == false) {
				?>
                <p>You <strong>must</strong> be logged in to sign up to receive the prayer chain. If you wish to register for the Crossroads Prayerchain but do not have an account, <a href="http://www.crossroadsde.com/wp-login.php?action=register">register for one here.</a> </p>
                <?php wp_login_form();
				
			} else {
				// User logged in.
				global $current_user;
				get_currentuserinfo();
				$pc = get_user_meta($current_user->ID, 'sc_prayerchain_sub',true);
                
				if ($pc == 0) {
					// They are NOT subscribed.
					?>
                    <div style="margin:8px; background-color:#dddddd; border:1px solid #b8b8b8; border-radius:6px; padding:10px; width:500px;">
            <form action="/prayerchain-manage" method="post">
        	<input type="hidden" name="ac" value="adduser" />
            <input type="hidden" name="em" value="<?php echo $current_user->user_email; ?>" />
            <p>You will receive the prayer chain at the following address: <em><?php echo $current_user->user_email; ?></em></p>
            <p><input type="submit" name="sub" value="Subscribe to Prayerchain" /></p>
            </form>
			
            </div>	
			<?php	} else { // User is subscribed ?>
            <div style="margin:8px; background-color:#dddddd; border:1px solid #b8b8b8; border-radius:6px; padding:10px; width:500px;">
            <form action="/prayerchain-manage" method="post">
        	<input type="hidden" name="ac" value="removeuser" />
            <input type="hidden" name="em" value="<?php echo $current_user->user_email; ?>" />
            <p>Want to stop receiving the prayer chain emails? </p>
            <p><input type="submit" name="unsub" value="Unsubscribe from Prayerchain" /></p>
            </form>
			
            </div>	
            
            
            <?php
			
			} // END if user is subscribed. 
		
		} // End if all
		}
		
		?>     
	</div>
</div>

<?php endwhile; ?>

<?php
get_footer();
?>
