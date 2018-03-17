<?php
// Install scripts for prayerchain plugin
function scpc_install() {
	
	global $wpdb;
		
	// prayerchain table
	$table = $wpdb->prefix."sc_prayerchain";
	$structure = "CREATE TABLE ".$table." (
		`pcID` INT( 11 ) NOT NULL AUTO_INCREMENT,
		`userID` INT(11) NOT NULL,
		`pcDate` INT(11) NOT NULL,
		`pcStatus` TINYINT(1) NOT NULL,
		`pcContact` TINYINT(1) NOT NULL,
		`pcText` TEXT,
		`pcName` VARCHAR(64) NOT NULL DEFAULT '0',
		`pcPhone` VARCHAR(64) NOT NULL DEFAULT '0',
		`pcEmail` VARCHAR(64) NOT NULL DEFAULT '0',
		`pcAuth` VARCHAR(256) NOT NULL DEFAULT '0',
		INDEX(`userID`), INDEX(`pcAuth`),
		UNIQUE (  `pcID` )
		) ENGINE = MYISAM";
	
	$wpdb->query($structure);
	
	update_option( 'scpc_active', '1');
	update_option( 'scpc_admin_email', '');
	update_option( 'scpc_require_login', '0');
	update_option( 'scpc_template', 'default.html');
		
	return true;

}
