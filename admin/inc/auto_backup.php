<?php
/* TOP_COMMENT_START
 * Copyright (C) 2022, Champion Consulting, LLC  dba ChampionCMS - All Rights Reserved
 *
 * This file is part of Champion Core. It may be used by individuals or organizations generating less than $400,000 USD per year in revenue, free-of-charge. Individuals or organizations generating over $400,000 in annual revenue who continue to use Champion Core after 90 days for non-evaluation and non-development use must purchase a paid license. 
 *
 * Proprietary
 * You may modify this source code for internal use. Resale or redistribution is prohibited.
 *
 * You can get the latest version at: https://cms.championconsulting.com/
 *
 * Dated June 2023
 *
TOP_COMMENT_END */


require_once (CHAMPION_ADMIN_DIR . '/inc/login.php');

/**
 * tag - auto backup
 */
function tag_auto_backup() {
	
	$days = 1; // if no backups within x days, create one
	$keep = 5; // keep only x number of most recent backups
	
	$backup_files = \glob(\championcore\get_configs()->dir_content . '/backups/*');
	
	# safety - skip if there is no backup directory
	if (!\is_dir(\championcore\get_configs()->dir_content . '/backups')) {
		\error_log( 'No contents/backups directory present' );
		return;
	}
	
	$backup_collection = array();
	
	foreach ($backup_files as $value) {
		if (\stripos($value, '.html') === false) {
			$backup_collection[ \filemtime($value) ] = $value;
		}
	}
	
	\krsort($backup_collection);
	
	# remove old backup files
	if (\count($backup_collection) > $keep) {
		$i = 0;
		foreach ($backup_collection as $backup) {
			$i++;
			if ($i > $keep) {
				if (\file_exists($backup)) {
					\unlink($backup);
				}
			}
		}
	}
	
	# create new backup
	$last_backup = 0;
	if (\sizeof($backup_collection) > 0) {
		$last_backup = \filemtime( reset($backup_collection) ) + ($days * 24 * 60 * 60);
	}
	
	if ($last_backup <= \time()) {
		
		$today = \date("m.d.y-His");
		
		$zip   = new \ZipArchive();
		
		$filename_zip = \championcore\get_configs()->dir_content . '/backups/' . $today . '.zip';
		
		\championcore\invariant( $filename_zip !== false, 'backup file cannot be created' );
		
		$zip->open( $filename_zip, \ZipArchive::CREATE);
		
		$dirNames = array(
			\championcore\get_configs()->dir_content . '/pages',
			\championcore\get_configs()->dir_content . '/blog',
			\championcore\get_configs()->dir_content . '/media',
			\championcore\get_configs()->dir_content . '/blocks',
			
			(CHAMPION_BASE_DIR . '/inc')
		); 
		
		foreach ($dirNames as $dirName) {
			
			if (!\is_dir($dirName)) {
				echo $GLOBALS['lang_backup_err_destination']; 
			} 
			
			$dirName  = \realpath($dirName);
			$dirName  = \rtrim($dirName, \DIRECTORY_SEPARATOR);
			$dirName .= \DIRECTORY_SEPARATOR;
			
			$dirStack = array($dirName);
			
			while (!empty($dirStack)) {
				$currentDir = \array_pop($dirStack);
				$filesToAdd = array(); 
				
				$dir = \dir($currentDir);
				while (false !== ($node = $dir->read())) {
					
					if (($node == '..') or ($node == '.')) {
						continue; 
					}
					
					$filename = $currentDir . $node;
					
					if (\is_dir($filename)) {
						\array_push($dirStack, ($filename . \DIRECTORY_SEPARATOR) );
					} 
					
					if (\is_file($filename)) {
						$filesToAdd[] = $node; 
					} 
				}
				
				$cutFrom = \strrpos(\substr($dirName, 0, -1), \DIRECTORY_SEPARATOR) + 1;
				
				$local_dir = \substr($currentDir, $cutFrom);
				$local_dir = \rtrim($local_dir, \DIRECTORY_SEPARATOR);
				
				$status = $zip->addEmptyDir( $local_dir );
				
				\championcore\invariant( $status === true, "cannot add directory to the backup ({$local_dir})" );
				
				foreach ($filesToAdd as $file) {
					
					$filename = $currentDir . $file;
					
					if (\is_file($filename)) {
						$status = $zip->addFile( $filename, ($local_dir . \DIRECTORY_SEPARATOR . $file) );
						
						\championcore\invariant( $status === true, 'cannot add file to the backup' );
					}
				}
			}
		}
		
		# close zip file
		$status = $zip->close();
		
		\championcore\invariant( $status === true, 'cannot close backup archive file' );
		
		# email the backup
		tag_auto_backup_email( $filename_zip );
	}
}

/**
 * email the backup zip file
 * \param $zipfile string the path to the zip file
 * \return void
 */
function tag_auto_backup_email( $zipfile ) {
	
	\championcore\pre_condition(      isset($zipfile) );
	\championcore\pre_condition( \is_string($zipfile) );
	\championcore\pre_condition(    \strlen($zipfile) > 0);
	
	if (\strlen(\championcore\wedge\config\get_json_configs()->json->autobackup_email) > 0) {
		
		$mailer = new \PHPMailer\PHPMailer\PHPMailer();
		
		$mailer->CharSet = 'UTF-8';
		
		# smtp
		if (    (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_host    ) > 0)
				and (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_username) > 0)
				and (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_password) > 0)
				and (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_port    ) > 0)
			) {
				// If your host requires smtp authentication, uncomment and fill out the lines below. 
				$mailer->isSMTP();                                                                      // Do nothing here
				$mailer->Host       = \championcore\wedge\config\get_json_configs()->json->smtp_host;      // Specify main server
				$mailer->SMTPAuth   = true;                                                             // Do nothing here
				$mailer->Username   = \championcore\wedge\config\get_json_configs()->json->smtp_username;  // SMTP username
				$mailer->Password   = \championcore\wedge\config\get_json_configs()->json->smtp_password;  // SMTP password
				$mailer->Port       = \championcore\wedge\config\get_json_configs()->json->smtp_port;      // SMTP port 	
				$mailer->SMTPSecure = 'tls';                                                            // Enable encryption, 'ssl' also accepted
		}
		
		# general mail setup
		$mailer->From     = reset($GLOBALS['email_contact']);
		#$mailer->FromName = reset($GLOBALS['email_contact']);
		
		$mailer->addAddress( \championcore\wedge\config\get_json_configs()->json->autobackup_email );
		
		$mailer->Subject  = $GLOBALS['lang_autobackup_email_subject_line'];
		$mailer->Body     = $GLOBALS['lang_autobackup_email_text'];
		
		$mailer->addAttachment( $zipfile ); 
		
		$status = $mailer->send();
		
		\championcore\invariant( $status === true, 'cannot send backup archive file via email: ' . $mailer->ErrorInfo);
	}
	
}

# call
tag_auto_backup();
