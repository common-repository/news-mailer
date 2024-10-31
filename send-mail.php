<?php
function news_mailer_send_mail() {
	
	if ( isset( $_GET['send'] ) ) {
		
		if ( $_GET['send'] == 1 ) {

		//Swiftmailer
		require_once('swift/lib/swift_required.php');

		global $wpdb;


		//Header
		$from_name = get_option('mail_from_name');
		$from_mail = get_option('mail_from_mail');
		$mail_subject = $_POST['mailer_subject'];
		
		
		//Recievers
		if($_POST['mailer_role'] == 'all') {
			$users_table = $wpdb->prefix.'users';
			$id_col = "ID";
			$users_query = "SELECT $id_col FROM $users_table";
		}
		else {
			$usermeta_table = $wpdb->prefix.'usermeta';
			$capabilities_name = $wpdb->prefix.'capabilities';
			$sender_role = $_POST['mailer_role'];
			$id_col = "user_id";
			$users_query = "SELECT $id_col FROM $usermeta_table WHERE meta_key = '$capabilities_name' AND meta_value LIKE '%$sender_role%'";
		}
		
		$results = $wpdb->get_results($users_query);
		$recievers = array();
			
		foreach ($results as $result) {  	
			$id = $result->$id_col;
			$user_info = get_userdata($id);
			$recievers[$user_info->user_email] = $user_info->first_name." ".$user_info->last_name;

			//Individual user replacement fields - {field}
			$replacements[$user_info->user_email] = array(
				"{ID}" => $user_info->ID,
				"{user_login}" => $user_info->user_login,
				"{user_email}" => $user_info->user_email,
				"{user_url}" => $user_info->user_url,
				"{user_registered}" => $user_info->user_registered,
				"{user_registered_date}" => substr($user_info->user_registered, 0, 11),
				"{display_name}" => $user_info->display_name,
				"{first_name}" => $user_info->first_name,
				"{last_name}" => $user_info->last_name,
				"{nickname}" => $user_info->nickname,
				"{description}" => $user_info->description
			);
		}
		
		
		$mailer_article1_title = stripslashes($_POST['mailer_article1_title']);
		$mailer_article1_preamble = stripslashes($_POST['mailer_article1_preamble']);
		$mailer_article1_text = stripslashes($_POST['mailer_article1_text']);
		
		$mailer_article2_title = stripslashes($_POST['mailer_article2_title']);
		$mailer_article2_preamble = stripslashes($_POST['mailer_article2_preamble']);
		$mailer_article2_text = stripslashes($_POST['mailer_article2_text']);
		
		
		
		//Articles
		$posts = array(0 => array($mailer_article1_title, nl2br2($mailer_article1_preamble), nl2br2($mailer_article1_text)), 1 => array($mailer_article2_title, nl2br2($mailer_article2_preamble), nl2br2($mailer_article2_text)));


		//Build articles
		if(count($posts) == 1) {
			$article_title = $posts[0][0];
			$article_preamble = $posts[0][1];
			$article_text = $posts[0][2];
	
			$articles_html = '<h2 style="font-family:Verdana; font-size:28px; font-weight:bold; color:#000000; margin:0 0 8px 0; padding:0;">'.$article_title.'</h2><p style="font-family:Verdana; font-size:13px; font-weight:bold; color:#000000;">'.$article_preamble.'</p><p style="font-family:Verdana; font-size:13px; font-weight:normal; color:#000000; margin:0 0 14px 0; padding:0;">'.$article_text.'</p>';
			$articles_plain = $article_title.PHP_EOL.PHP_EOL.$article_preamble.PHP_EOL.PHP_EOL.$article_text.PHP_EOL.PHP_EOL.'    -~-'.PHP_EOL.PHP_EOL;
		}
		else {
			$i = 1;
			foreach($posts as $post) {
				$article_title = $post[0];
				$article_preamble = $post[1];
				$article_text = $post[2];
	
				if($i == 1) {
					$articles_html = '<h2 style="font-family:Verdana; font-size:28px; font-weight:bold; color:#000000; margin:0 0 8px 0; padding:0;">'.$article_title.'</h2><p style="font-family:Verdana; font-size:13px; font-weight:bold; color:#000000;">'.$article_preamble.'</p><p style="font-family:Verdana; font-size:13px; font-weight:normal; color:#000000; margin:0 0 14px 0; padding:0;">'.$article_text.'</p><br>';
					$articles_plain = $article_title.PHP_EOL.PHP_EOL.$article_preamble.PHP_EOL.PHP_EOL.$article_text.PHP_EOL.PHP_EOL.'    -~-'.PHP_EOL.PHP_EOL;
				$i++;
				}
				else {
					$articles_html = $articles_html.'<h3 style="font-family:Verdana; font-size:20px; font-weight:bold; color:#000000; margin:0 0 8px 0; padding:0;">'.$article_title.'</h3><p style="font-family:Verdana; font-size:13px; font-weight:bold; color:#000000;">'.$article_preamble.'</p><p style="font-family:Verdana; font-size:13px; font-weight:normal; color:#000000; margin:0 0 14px 0; padding:0;">'.$article_text.'</p>';
					$articles_plain = $articles_plain.$article_title.PHP_EOL.PHP_EOL.$article_preamble.PHP_EOL.PHP_EOL.$article_text.PHP_EOL.PHP_EOL.'    -~-'.PHP_EOL.PHP_EOL;
				}
			}
		}

		//Events
		if(/*$_POST['mailer_events']*/"2" == "1") {

			$dbem_events = $wpdb->prefix.'dbem_events';
			$cur_date = date('Y-m-d');
			$events_query = "SELECT event_id, event_name, event_start_date, event_start_time FROM $dbem_events WHERE event_start_date > '$cur_date' ORDER BY event_start_date ASC";
			$events_results = $wpdb->get_results($events_query);
			$calendar_html = "";

			foreach ($events_results as $event) {
				$event_name = $event->event_name;	
				$event_date = $event->event_start_date." ".$event->event_start_time;	
				$event_id = $event->event_id;
	
				$calendar_html = $calendar_html.'<h4 style="font-family:Verdana; font-size:13px; font-weight:bold; color:#000000; margin:6px 0 0 0; padding:0;">'.$event_name.'</h4><p style="font-family:Verdana; font-size:12px; font-style:italic; font-weight:normal; color:#333333; margin:0; padding:0;">'.$event_date.'</p><p style="font-family:Verdana; font-size:12px; font-weight:normal; color:#333333; margin:0 0 10px 0; padding:0;"><a href="http://www.salongen.org/events/?event_id='.$event_id.'" style="font-family:Verdana; font-size:12px; font-weight:normal; color:#333333;">Mer info >></a></p>';
				$calendar_plain = $calendar_plain.$event_name.PHP_EOL.$event_date.PHP_EOL.'http://www.salongen.org/events/?event_id='.$event_id.PHP_EOL.PHP_EOL.'    ---'.PHP_EOL.PHP_EOL;
			}
		}
		else {
			$calendar_html = "Det finns tyvärr inga evenemang i föreningens kalendarium för tillfället";
			$calendar_plain = "Det finns tyvärr inga evenemang i föreningens kalendarium för tillfället";
		}

		//Mail body
		$blogname = get_option('blogname');
		$date = date("Y-m-d");

		//HTML body
		$html_template_file = NEWSMAILER_DIRPATH.'templates/template.html';
		$html_template_handle = fopen($html_template_file, "r");
		$html_template = fread($html_template_handle, filesize($html_template_file));
		fclose($html_template_handle);
		$html_tags = array("/%TITLE%/", "/%BLOGNAME%/", "/%DATE%/", "/%ARTICLES%/", "/%CALENDAR%/", "/%USERMSG%/");
		$html_replacers = array($mail_subject, $blogname, $date, $articles_html, $calendar_html, "");
		$html_mail = preg_replace($html_tags, $html_replacers, $html_template);

		//Plain text body
		$plain_template_file = NEWSMAILER_DIRPATH.'templates/template.txt';
		$plain_template_handle = fopen($plain_template_file, "r");
		$plain_template = fread($plain_template_handle, filesize($plain_template_file));
		fclose($plain_template_handle);
		$plain_tags = array("/%TITLE%/", "/%BLOGNAME%/", "/%DATE%/", "/%ARTICLES%/", "/%CALENDAR%/", "/%USERMSG%/");
		$plain_replacers = array($mail_subject, $blogname, $date, $articles_plain, $calendar_plain, "");
		$plain_mail = strip_tags(preg_replace($plain_tags, $plain_replacers, $plain_template));




		//Start the mailing
		
		echo '<div id="message" class="updated">';
		echo "<p>".__('Log', 'news-mailer')."</p>";
			
		if ( get_option('mail_transport') == 'wp_mail') {
		
			echo '<p>'.__("Sorry, but the wp_mail transport is currently not supported by News Mailer. Please change your settings to SMTP in the Mail Settings.", 'news-mailer').'</p>';
			
		}
		else {
		

			//Transport type
			if(get_option('mail_transport') == "smtp") {
				$transport = Swift_SmtpTransport::newInstance();
				$transport->setHost(get_option('mail_smtp_server_url'));
				$transport->setPort(get_option('mail_smtp_server_port'));
				$transport->setUsername(get_option('mail_smtp_server_username'));
				$transport->setPassword(get_option('mail_smtp_server_password'));
				
				//Encryption
				if( get_option('mail_smtp_server_encryption') ) {
					$transport->setEncryption( get_option('mail_smtp_server_encryption') );
				}
				
			}
			elseif(get_option('mail_transport') == "sendmail") {
				$transport_mta = get_option('mail_server_mta').' '.get_option('mail_server_mta_flag');
				$transport = Swift_SendmailTransport::newInstance($transport_mta);
			}		


			$mailer = Swift_Mailer::newInstance($transport);

			if ( get_option('mail_anti_flood_enable') == '1' ) {
			
				if ( get_option('mail_anti_flood_type') == 'swiftmailer' ) {
				
					$antiflood_mails = get_option('mail_anti_flood_mails');
					$antiflood_seconds = get_option('mail_anti_flood_seconds');	

					$mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin($antiflood_mails, $antiflood_seconds));
					
				}
				elseif ( get_option('mail_anti_flood_type') == 'wp-cron' ) {
					
					$antiflood_mails = get_option('mail_anti_flood_mails');
					
					$i=1;
					
					$recievers_wp_cron = array();
					
					foreach ( $recievers as $r_mail=>$r_name ) {
					
						if ( $i <= $antiflood_mails ) {
						
							$recievers_first[$r_mail] = $r_name;
							
						}
						else {
						
							$recievers_wp_cron[$r_mail] = $r_name;
							
						}
						
						$i++;
						
					}
					
					unset($recievers);
					
					$recievers = $recievers_first;
					
					$store_mail_to = serialize($recievers_wp_cron);
					$store_mail_subject = serialize($mail_subject);
					$store_mail_html = serialize($html_mail);
					$store_mail_plain = serialize($plain_mail);
					
					$table_name = $wpdb->prefix.NEWSMAILER_MAIL_CRON_TABLE;
					
					$wpdb->insert( $table_name, array( 'mail_to' => $store_mail_to, 'mail_subject' => $store_mail_subject, 'mail_html' => $store_mail_html, 'mail_plain' => $store_mail_plain ) );
					
					update_option( 'mail_current_cronjob', $wpdb->insert_id );

				}
			}
			
			//Logger plugin
			$logger = new Swift_Plugins_Loggers_EchoLogger();
			$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

			$message = Swift_Message::newInstance();
			$message->setSubject($mail_subject);
			$message->setFrom(array($from_mail => $from_name));
			
			$message->setTo($recievers);
			
			//Define body format
			if( get_option('mail_format') == 'both') {
				
				$message->setBody($html_mail,'text/html');
				$message->addPart($plain_mail,'text/plain');
			}
			elseif( get_option('mail_format') == 'plain') {
				
				$message->setBody($plain_mail,'text/plain');
				
			}
			elseif( get_option('mail_format') == 'html') {
				
				$message->setBody($html_mail,'text/html');
			}
			else {
				
				$message->setBody($html_mail,'text/html');
				$message->addPart($plain_mail,'text/plain');
			}

			//Decorator plugin
			$decorator = new Swift_Plugins_DecoratorPlugin($replacements);
			$mailer->registerPlugin($decorator);

			//Skicka mail
			$result = $mailer->batchSend($message);
			
			if ( get_option('mail_anti_flood_enable') == '1' and get_option('mail_anti_flood_type') == 'wp-cron' ) {
				
				$antiflood_seconds = get_option('mail_anti_flood_seconds');
				wp_schedule_single_event(time()+$antiflood_seconds, 'news_mailer_mail_cron');
				
				echo "<p>";
				_e("Mailing started with wp-cron", 'news-mailer');
				echo "</p>";
				//echo "<p>".$logger->dump()."</p>";
				echo "<p>".printf(_n("The wp-cron job has finished sending mails to listed recipients. Next wp-cron job will begin in %d second.", "The wp-cron job has finished sending mails to listed recipients. Next wp-cron job will begin in %d seconds.", $antiflood_seconds), $antiflood_seconds)."</p>";
				
			}
			else {
			
				echo "<p><strong>".__("Mailing started", 'news-mailer')."</strong></p>";
				echo "<p>".$logger->dump()."</p>";
				
			}
		
		}
		
		echo '</div>';

		}
	}
}

function news_mailer_send_mail_wp_cron() {
	
	//Swiftmailer
	require_once('swift/lib/swift_required.php');

	global $wpdb;
	
	
	$from_name = get_option('mail_from_name');
	$from_mail = get_option('mail_from_mail');
	
	$cronjob_table_name = $wpdb->prefix.NEWSMAILER_MAIL_CRON_TABLE;
	$current_cronjob_id = get_option('mail_current_cronjob');
	
	$cronjob = $wpdb->get_row("SELECT * FROM $cronjob_table_name WHERE cron_id = '$current_cronjob_id'");
	
	$recievers = unserialize($cronjob->mail_to);
	$mail_subject = unserialize($cronjob->mail_subject);
	$html_mail = unserialize($cronjob->mail_html);
	$plain_mail = unserialize($cronjob->mail_plain);
	
	$id_col = "ID";
	$users_table = $wpdb->prefix.'users';
		
	foreach ($recievers as $cur_mail=>$cur_name) {
		
		$user_id = "SELECT $id_col FROM $users_table WHERE user_email = '$cur_mail'";
		$id = $wpdb->get_var($user_id);
		
		$user_info = get_userdata($id);

		//Individual user replacement fields - {field}
		$replacements[$user_info->user_email] = array(
			"{ID}" => $user_info->ID,
			"{user_login}" => $user_info->user_login,
			"{user_email}" => $user_info->user_email,
			"{user_url}" => $user_info->user_url,
			"{user_registered}" => $user_info->user_registered,
			"{user_registered_date}" => substr($user_info->user_registered, 0, 11),
			"{display_name}" => $user_info->display_name,
			"{first_name}" => $user_info->first_name,
			"{last_name}" => $user_info->last_name,
			"{nickname}" => $user_info->nickname,
			"{description}" => $user_info->description
		);
	}
	
	
	//Transport type
	if(get_option('mail_transport') == "smtp") {
		$transport = Swift_SmtpTransport::newInstance();
		$transport->setHost(get_option('mail_smtp_server_url'));
		$transport->setPort(get_option('mail_smtp_server_port'));
		$transport->setUsername(get_option('mail_smtp_server_username'));
		$transport->setPassword(get_option('mail_smtp_server_password'));
	}
	elseif(get_option('mail_transport') == "sendmail") {
		$transport_mta = get_option('mail_server_mta').' '.get_option('mail_server_mta_flag');
		$transport = Swift_SendmailTransport::newInstance($transport_mta);
	}
	
	$mailer = Swift_Mailer::newInstance($transport);
				
				
	
	
	$antiflood_mails = get_option('mail_anti_flood_mails');
					
	$i=1;
	
	$recievers_wp_cron = array();
					
	foreach ( $recievers as $r_mail=>$r_name ) {
					
		if ( $i <= $antiflood_mails ) {
						
			$recievers_first[$r_mail] = $r_name;
							
		}			
		else {
						
			$recievers_wp_cron[$r_mail] = $r_name;
							
		}
						
		$i++;
						
	}
					
	unset($recievers);
					
	$recievers = $recievers_first;
	
	if (count($recievers_wp_cron) > 0) {
	
		wp_schedule_single_event(time()+$antiflood_seconds, 'news_mailer_mail_cron');
		
		$store_mail_to = serialize($recievers_wp_cron);
		
		$wpdb->update( $cronjob_table_name, array( 'mail_to' => $store_mail_to ), array( 'cron_id' => $current_cronjob_id ) );
		
	}
	else {
	
		delete_option( 'mail_current_cronjob' );
		
		$wpdb->update( $cronjob_table_name, array( 'mail_to' => '' ), array( 'cron_id' => $current_cronjob_id ) );
		
	}
	
	
		
	//Logger plugin
	//$logger = new Swift_Plugins_Loggers_EchoLogger();
	//$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

	$message = Swift_Message::newInstance();
	$message->setSubject($mail_subject);
	$message->setFrom(array($from_mail => $from_name));
			
	$message->setTo($recievers);

    
    //Define body format
    if( get_option('mail_format') == 'both') {
          
          $message->setBody($html_mail,'text/html');
          $message->addPart($plain_mail,'text/plain');
    }
    elseif( get_option('mail_format') == 'plain') {
          
          $message->setBody($plain_mail,'text/plain');
          
    }
    elseif( get_option('mail_format') == 'html') {
          
          $message->setBody($html_mail,'text/html');
    }
    else {
          
          $message->setBody($html_mail,'text/html');
          $message->addPart($plain_mail,'text/plain');
    }

	//Decorator plugin
	$decorator = new Swift_Plugins_DecoratorPlugin($replacements);
	$mailer->registerPlugin($decorator);
	
	$result = $mailer->batchSend($message);

}
?>