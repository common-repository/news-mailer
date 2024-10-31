<?php
function register_news_mailer_settings() {

	register_setting('mail', 'mail_from_name');
	register_setting('mail', 'mail_from_mail');
	register_setting('mail', 'mail_format');
	register_setting('mail', 'mail_transport');
	register_setting('mail', 'mail_smtp_server_url');
	register_setting('mail', 'mail_smtp_server_port', 'intval');
	register_setting('mail', 'mail_smtp_server_username');
	
	if( isset($_POST['mail_smtp_server_password']) ) {
		if( $_POST['mail_smtp_server_password'] != '############') {
			register_setting('mail', 'mail_smtp_server_password');
		}
	}
	
	register_setting('mail', 'mail_smtp_server_encryption');
	register_setting('mail', 'mail_anti_flood_enable');
	register_setting('mail', 'mail_anti_flood_mails');
	register_setting('mail', 'mail_anti_flood_seconds');
	register_setting('mail', 'mail_anti_flood_type');
	register_setting('mail', 'mail_server_mta');
	register_setting('mail', 'mail_server_mta_flag');

}

function news_mailer_options_page() {

?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('Mail Settings', 'news-mailer'); ?></h2>

<form method="post" action="options.php">
<?php settings_fields('mail'); ?>

<table class="form-table">

<tr valign="top">
<th scope="row"><label for="mail_from_name"><?php _e('From name', 'news-mailer'); ?></label></th>
<td><input name="mail_from_name" type="text" id="mail_from_name" value="<?php echo get_option('mail_from_name'); ?>" class="regular-text" /> <span class="description"><?php _e('Default sender name in outgoing mail.', 'news-mailer'); ?></span></td>
</tr>

<tr valign="top">
<th scope="row"><label for="mail_from_mail"><?php _e('From mail', 'news-mailer'); ?></label></th>
<td><input name="mail_from_mail" type="text" id="mail_from_mail" value="<?php echo get_option('mail_from_mail'); ?>" class="regular-text" /> <span class="description"><?php _e('Default sender mail adress in outgoing mail.', 'news-mailer'); ?></span></td>
</tr>

<tr valign="top">
<th scope="row"><label for="mail_format"><?php _e('Mail format', 'news-mailer') ?></label></th>
<td>
<select name="mail_format" id="mail_format">
<?php
$current_mail_format = get_option('mail_format');
$mail_format = array('both' => __('Both (Default)', 'news-mailer'), 'plain' => __('Plain text only', 'news-mailer'), 'html' => __('HTML only', 'news-mailer'));
foreach($mail_format as $type => $type_name) {
	$selected_type = '';
	if($current_mail_format == $type ) {
		$selected_type = ' selected="selected"';
	}
	echo '<option value="'.esc_attr($type).'"'.$selected_type.'>'.$type_name.'</option>';
}
?>
</select>
<p><span class="description"></span></p>
</td>
</tr>

</table>
<h2><?php _e('News Mailer Settings', 'news-mailer'); ?></h2>

<?php

$potential_issues = array();
$count_users = count_users();

if ( ini_get('max_execution_time') <= 30 ) {
	$potential_issues[] = __("The <code>max_execution_time</code> in your php.ini is set to 30 seconds or less. This can affect the stability of the mailing process, especially when mass mailing. If you are experiencing problems with recipents not receiving mails, increasing this value may solve the problem.", 'news-mailer');
}

/*
if ( $count_users['total_users'] < 50 ) {
	$potential_issues[] = __("There are more than 50 registered users on your blog. If you're going to send a newsletter to all of them it's recommended that you configure the AntiFlood feature.", 'news-mailer');
}
*/

if ( count( $potential_issues ) > 0 ) {
?>
<div id="message" class="updated">
<p><?php _e("Potential issues:", 'news-mailer'); ?></p>
<ul>
<?php
foreach ( $potential_issues as $potential_issue ) {
	echo '	<li>'.$potential_issue.'</li>' . PHP_EOL;
}
?>
</ul>
</div>
<?php
}

?>

<h3 class="title"><?php _e('Transport', 'news-mailer') ?></h3>
<p><?php _e("You can specify what transport type you would like Wordpress to use when sending newsletters. The default wp_mail() function is of course working well but isn't implemented to News Mailer yet. If you want to use encryption or send mails to a large number of recipents at the same time, another transport such as SMTP is preferred.", 'news-mailer') ?></p>
<table class="form-table">

<tr valign="top">
<th scope="row"><label for="mail_transport"><?php _e('Transport type', 'news-mailer') ?></label></th>
<td>
<select name="mail_transport" id="mail_transport">
<?php
$current_transport_type = get_option('mail_transport');
$transport_type = array('smtp' => __('SMTP', 'news-mailer'), 'mta' => __('MTA', 'news-mailer'), 'wp_mail' => __('Default', 'news-mailer'));
foreach($transport_type as $type => $type_name) {
	$selected_type = '';
	if($current_transport_type == $type ) {
		$selected_type = ' selected="selected"';
	}
	echo '<option value="'.esc_attr($type).'"'.$selected_type.'>'.$type_name.'</option>';
}
?>
</select>
<p><span class="description">
<?php
echo "<strong>".__('SMTP', 'news-mailer')."</strong>: ";
/* translators: Description of SMTP setting */
_e("The Swiftmailer SMTP transport. Supports logging and many other features. <strong>Recommended setting</strong>.", 'news-mailer');
?>
</span></p>
<p><span class="description">
<?php
echo "<strong>".__('MTA', 'news-mailer')."</strong>: ";
/* translators: Description of MTA setting */
_e("Use a locally installed Mail Transfer Agent. This feature is not recommended, it hasn't been properly tested yet", 'news-mailer');
?>
</span></p>
<p><span class="description">
<?php
echo "<strong>".__('Default', 'news-mailer')."</strong>: ";
/* translators: Description of the default setting */
_e('Wordpress default wp_mail() function.', 'news-mailer');
?>
</span></p>
</td>
</tr>

</table>

<h3 class="title"><?php _e('SMTP', 'news-mailer') ?></h3>
<table class="form-table">

<tr valign="top">
<th scope="row"><label for="mail_smtp_server_url"><?php _e('URL', 'news-mailer'); ?></label></th>
<td><input name="mail_smtp_server_url" type="text" id="mail_smtp_server_url" value="<?php echo get_option('mail_smtp_server_url'); ?>" class="regular-text" /> 
<label for="mail_smtp_server_port"><?php _e('Port', 'news-mailer'); ?></label>
<input name="mail_smtp_server_port" type="text" id="mail_smtp_server_port" value="<?php echo get_option('mail_smtp_server_port'); ?>" class="small-text" />
<span class="description"><?php _e('Default settings in php.ini:', 'news-mailer'); echo ' <code>'.ini_get('SMTP').'</code>: <code>'.ini_get('smtp_port').'</code>'; ?></span>
</td>
</tr>

<tr valign="top">
<th scope="row"><label for="mail_smtp_server_username"><?php _e('Username', 'news-mailer'); ?></label></th>
<td><input name="mail_smtp_server_username" type="text" id="mail_smtp_server_username" value="<?php echo get_option('mail_smtp_server_username'); ?>" class="regular-text" /></td>
</tr>

<tr valign="top">
<th scope="row"><label for="mail_smtp_server_password"><?php _e('Password', 'news-mailer'); ?></label></th>
<td><input name="mail_smtp_server_password" type="password" id="mail_smtp_server_password" value="<?php if( get_option('mail_smtp_server_password') ) { echo '############'; } ?>" class="regular-text" /></td>
</tr>

<tr valign="top">
<th scope="row"><label for="mail_smtp_server_encryption"><?php _e('Encryption', 'news-mailer') ?></label></th>
<td>
<select name="mail_smtp_server_encryption" id="mail_smtp_server_encryption">
<?php
$current_smtp_encryption = get_option('mail_smtp_server_encryption');
$smtp_encryption = array('' => __( '&mdash; None &mdash;' , 'news-mailer'), 'ssl' => __('SSL', 'news-mailer'), 'tls' => __('TLS', 'news-mailer'));
foreach($smtp_encryption as $encryption => $encryption_name) {
	$selected_encryption = '';
	if($current_smtp_encryption == $encryption ) {
		$selected_encryption = ' selected="selected"';
	}
	echo '<option value="'.esc_attr($encryption).'"'.$selected_encryption.'>'.$encryption_name.'</option>';
}
?>
</select>
<?php
// Check if PHP installation has the appropriate PHP transport wrappers
if(function_exists('stream_get_transports')) {
	if(!in_array('ssl', stream_get_transports()) and !in_array('tls', stream_get_transports())) {
		echo ' <span class="description">';
	
		if(in_array('ssl', stream_get_transports())) {
			echo "<strong>Attention:</strong> This PHP installation support only <code>SSL</code> encryption";
		}
		elseif(in_array('tls', stream_get_transports())) {
			echo "<strong>Attention:</strong> This PHP installation support only <code>TLS</code> encryption";
		}
		else {
			_e('<strong>Varning:</strong> This PHP installation does not support encryption over SSL or TLS.', 'news-mailer');
		}
	
		echo '</span>';
	}
}

?>
</td>
</tr>

</table>


<h3 class="title"><?php _e('Mail Transfer Agent (MTA)', 'news-mailer') ?></h3>
<table class="form-table">

<tr valign="top">
<th scope="row"><label for="mail_server_mta"><?php _e('Path', 'news-mailer'); ?></label></th>
<td><input name="mail_server_mta" type="text" id="mail_server_mta" value="<?php echo get_option('mail_server_mta'); ?>" class="regular-text" /> <span class="description"><?php _e('Location of sendmail or another MTA. Defaults to the <code>sendmail_path</code> in php.ini', 'news-mailer'); ?></span></td>
</tr>

<tr valign="top">
<th scope="row"><label for="mail_server_mta_flag"><?php _e('Sendmail command line flag', 'news-mailer') ?></label></th>
<td>
<select name="mail_server_mta_flag" id="mail_server_mta_flag">
<?php
$current_mta_flag = get_option('mail_server_mta_flag');
$mta_flags = array('-bs' => __('-bs', 'news-mailer'), '-t' => '-t');
foreach($mta_flags as $mta_flag => $mta_flag_name) {
	$selected_mta_flag = '';
	if($current_mta_flag == $mta_flag ) {
		$selected_mta_flag = ' selected="selected"';
	}
	echo '<option value="'.esc_attr($mta_flag).'"'.$selected_mta_flag.'>'.$mta_flag_name.'</option>';
}
?>
</select> <span class="description"><?php _e('If you run sendmail in "-t" mode you will get no feedback as to whether or not sending has succeeded. Use "-bs" unless you have a reason not to.', 'news-mailer'); ?></span>
</td>
</tr>

</table>



<h3 class="title"><?php _e('AntiFlood', 'news-mailer') ?></h3>
<p><?php _e("Many SMTP servers have limits on the number of messages that may be sent during any single SMTP connection. The AntiFlood feature provides a way to stay within this limit while still managing a large number of emails.", 'news-mailer'); ?></p>
<?php
if ( get_option('mail_anti_flood_enable') == '1' ) {
	$mail_anti_flood_enable_checked = ' checked="checked"';
}
else {
	$mail_anti_flood_enable_checked = '';
}
?>
<p><input name="mail_anti_flood_enable" type="checkbox" id="mail_anti_flood_enable" value="1"<?php echo $mail_anti_flood_enable_checked; ?> /> <label for="mail_anti_flood_enable"><?php _e('Enable', 'news-mailer'); ?></label></p>
<p><span class="description"><?php _e('Highly recommended. Though only available when SMTP or MTA is used as transport.', 'news-mailer'); ?></span></p>
<table class="form-table">

<tr valign="top">
<th scope="row"></th>
<td></td>
</tr>

<tr valign="top">
<th scope="row"><label for="mail_anti_flood_mails"><?php _e('Mails', 'news-mailer'); ?></label></th>
<td><input name="mail_anti_flood_mails" type="text" id="mail_anti_flood_mails" value="<?php echo get_option('mail_anti_flood_mails'); ?>" class="small-text" /> <span class="description"><?php _e('Disconnect and then re-connect to the server when this number of mails is sent.', 'news-mailer'); ?></span></td>
</tr>

<tr valign="top">
<th scope="row"><label for="mail_anti_flood_seconds"><?php _e('Seconds', 'news-mailer'); ?></label></th>
<td><input name="mail_anti_flood_seconds" type="text" id="mail_anti_flood_seconds" value="<?php echo get_option('mail_anti_flood_seconds'); ?>" class="small-text" /> <span class="description"><?php _e('Seconds of pause between connections.', 'news-mailer'); ?></span><p><span class="description"></span></p></td>
</tr>

<tr valign="top">
<th scope="row"><label for="mail_anti_flood_type"><?php _e('Type', 'news-mailer'); ?></label></th>
<td>
<p>
<?php
if ( get_option('mail_anti_flood_type') == 'swiftmailer' ) {
	$mail_anti_flood_type_swiftmailer = ' checked="checked"';
	$mail_anti_flood_type_wpcron = '';
}
elseif ( get_option('mail_anti_flood_type') == 'wp-cron' ) {
	$mail_anti_flood_type_wpcron = ' checked="checked"';
	$mail_anti_flood_type_swiftmailer = '';
}
?>
<input type="radio" name="mail_anti_flood_type" value="swiftmailer"<?php echo $mail_anti_flood_type_swiftmailer; ?> /> <?php _e('Swiftmailer', 'news-mailer'); ?> <br />
<input type="radio" name="mail_anti_flood_type" value="wp-cron"<?php echo $mail_anti_flood_type_wpcron; ?> /> <?php _e('wp-cron', 'news-mailer'); ?>
</p>
<p><span class="description"><?php _e("If the pause between the connections is a matter of seconds, Swiftmailer is recommended. If minutes or even hours wp-cron will probably work better.", 'news-mailer'); ?></span></p>
</td>
</tr>




</table>


<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'news-mailer') ?>" />
</p>

</form>
</div>
<?php } ?>