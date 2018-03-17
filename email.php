<?php
function emailNewPrayerRequest($pid) {
	
	global $wpdb;
	
	$email = get_option("scpc_admin_email");
	
	$res = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."sc_prayerchain WHERE pcID = '".$pid."'", OBJECT);
	
	$template = plugins_url("sd-prayerchain/email/authorize.html");
	
	$html = file_get_contents($template);
	
	$html = str_ireplace('[[name]]',stripslashes($res->pcName),$html);
	$html = str_ireplace('[[date]]',$res->pcDate,$html);
	$html = str_ireplace('[[contact]]',$res->pcContact,$html);
	$html = str_ireplace('[[request]]',stripslashes($res->pcText),$html);
	$html = str_ireplace('[[phone]]',$res->pcPhone,$html);
	$html = str_ireplace('[[email]]',$res->pcEmail,$html);
	$html = str_ireplace('[[authcode]]',$res->pcAuth,$html);
	$html = str_ireplace('[[pid]]',$pid,$html);
	
	$html = str_ireplace('[[year]',date("Y"),$html);
	
	if ($email == "") { $email = "lsenner@crossroadsde.com"; }
	
	$to = $email;
	$from = "prayer@crossroadsde.com";
	$subject = "New Prayer Request: Please Moderate.";
	
	$headers = array("Content-Type: text/html");
	wp_mail( $to,$subject, $html, $headers);
	
	return;
		
} // EOF 

function scpc_send_mailing() {
	
	$auth = $_GET['auth'];
	if ($auth != "32d98yhilb4fp97t2p93fdbiw") exit("FAIL");
	
	sendPCMailing();
	
	exit("SEND");
	
} // EOF scpc_send_mailing

function sendPCMailing() {
	
	global $wpdb;
	
	$r = get_users(array("meta_key" => "sc_prayerchain_sub", "meta_value" => 1));
	
	$to = array('prayer@crossroadsde.com');	
	foreach($r as $u) {	
		$bcc[] = "BCC: ". $u->user_email;
	}
	
	$r = $wpdb->get_results("SELECT * FROM wp_sc_prayerchain WHERE pcStatus = 2");
	
	$html = $hor = $hort = $text = '';
	$num = 0;
	foreach($r as $pc) {
		
		$html .=$hor."<strong><b>Request from ".stripslashes($pc->pcName).":</b></strong><br /><p>".stripslashes($pc->pcText)."<p><br />";
		
		$text .=$hort."Request from ".stripslashes($pc->pcName).": \r\n \r\n ".stripslashes($pc->pcText)."\r\n";
		$hor = "----------------------------<br />";
		$hort = "---------------------------- \r\n";
		
		$num++;
	} // EOFE
	
	if (count($to) == 0 || $html == "") {
		return true;
	}
	
	if ($num > 1) $sub = "New Prayer Requests"; else $sub = "New Prayer Request";
	
	$header = array('From: Crossroads Prayerchain <prayer@crossroadsde.com>', 'Content-Type: text/html; charset=UTF-8');
	$headers = array_merge($header, $bcc);
		 
	wp_mail( $to, $sub, $html, $headers );
	
	$wpdb->update("wp_sc_prayerchain",array("pcStatus" => 3),array("pcStatus" => 2));
	
	return true;
} // EOF
