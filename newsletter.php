<?php
function news_mailer_mail_page() {
?>

<div class="wrap">
<?php //screen_icon(); ?>
<h2><?php _e('Compose Newsletter', 'news-mailer'); ?></h2>

<?php
if ( get_option('mail_beginner_message') == '1' ) {
?>
<div id="message" class="updated">
<p><?php _e("Welcome to News Mailer!", 'news-mailer'); ?></p>
<p><?php _e("In order to use the plugin you'll have to go to the Mail settings page found under the settings section in the menu and change the transport type used by News Mailer. SMTP is recommended.", 'news-mailer'); ?></p>
<p><?php _e("The current template is a project that I'm working on. It's two files, one for the HTML part of the mail and one for the plain text part. Feel free to modify them to suit your needs. Remember to back them up since they're at the moment overwritten when you update the plugin.", 'news-mailer'); ?></p>
<p><?php _e("Thank you for installing News Mailer, this message will now self destruct. *poff*", 'news-mailer'); ?></p>
<p><i><?php _e("/Joel Bergroth, creator", 'news-mailer'); ?></i></p>
</div>
<?php

	delete_option( 'mail_beginner_message' );
}
elseif ( get_option('mail_transport') == 'wp_mail' ) {
?>

<div id="message" class="error">
<p><?php _e("Warning: The default WordPress mail transport is currently not supported by News Mailer. Go to the <a href=\"options-general.php?page=news_mailer_options\">Mail settings</a> and change the <i>Transport type</i> to either SMTP (recommended) or MTA.", 'news-mailer'); ?></p>
</div>

<?php
}
?>


<p><?php _e('Send a newsletter to the users of this blog.', 'news-mailer'); ?></p>

<form action="admin.php?page=news-mailer&send=1" method="post" name="nm_sender" id="nm_sender">
<h3><?php _e('Head', 'news-mailer'); ?></h3>
<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><?php _e('From:', 'news-mailer'); ?></th>
		<td><?php echo " ".get_option('mail_from_name')." &lt;".get_option('mail_from_mail')."&gt;"; ?></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="mailer_role"><?php _e('Send to', 'news-mailer'); ?></label></th>
		<td>
			<select name="mailer_role" id="mailer_role">
			<option value='all' selected="selected"><?php _e('All', 'news-mailer') ?></option>
			<?php wp_dropdown_roles(); ?>
			</select>
		</td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="mailer_subject"><?php _e('Subject', 'news-mailer'); ?> <span class="description"><?php _e('(required)', 'news-mailer'); ?></span></label></th>
		<td><input name="mailer_subject" type="text" id="mailer_subject" value="" /></td>
	</tr>
</table>
<h3><?php _e('Articles', 'news-mailer'); ?></h3>

<h4><?php _e('First article', 'news-mailer'); ?></h4>
<table class="form-table">
	<tr class="form-field form-required">
		<th scope="row"><label for="mailer_article1_title"><?php _e('Title', 'news-mailer'); ?> <span class="description"><?php _e('(required)', 'news-mailer'); ?></span></label></th>
		<td><input name="mailer_article1_title" type="text" id="mailer_article1_title" value="" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="mailer_article1_preamble"><?php _e('Preamble', 'news-mailer'); ?></label></th>
		<td><textarea name="mailer_article1_preamble" type="text" id="mailer_article1_preamble"></textarea></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="mailer_article1_text"><?php _e('Body', 'news-mailer'); ?></label></th>
		<td><textarea name="mailer_article1_text" type="text" id="mailer_article1_text"></textarea></td>
	</tr>
</table>

<h4><?php _e('Second article (optional)', 'news-mailer'); ?></h4>
<table class="form-table">
	<tr class="form-field">
		<th scope="row"><label for="mailer_article2_title"><?php _e('Title', 'news-mailer'); ?></label></th>
		<td><input name="mailer_article2_title" type="text" id="mailer_article2_title" value="" /></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="mailer_article2_preamble"><?php _e('Preamble', 'news-mailer'); ?></label></th>
		<td><textarea name="mailer_article2_preamble" type="text" id="mailer_article2_preamble"></textarea></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="mailer_article2_text"><?php _e('Body', 'news-mailer'); ?></label></th>
		<td><textarea name="mailer_article2_text" type="text" id="mailer_article2_text"></textarea></td>
	</tr>
	
</table>
<h3><?php _e('Other mail settings', 'news-mailer'); ?></h3>
<table class="form-table">	
	<!--<tr>
		<th scope="row"><label for="mailer_events"><?php _e('Events', 'news-mailer') ?></label></th>
		<td><label for="mailer_events"><input type="checkbox" name="mailer_events" id="mailer_events" value="1" disabled="disabled" /> <?php _e( 'Include upcoming events. (Not yet available)' , 'news-mailer'); ?></label></td>
	</tr>-->
	<tr>
		<th scope="row"><label for="mailer_userinfo"><?php _e('User information', 'news-mailer') ?></label></th>
		<td><label for="mailer_userinfo"><input type="checkbox" name="mailer_userinfo" id="mailer_userinfo" value="1" checked="checked" disabled="disabled" /> <?php _e( 'Include user information' , 'news-mailer'); ?></label></td>
	</tr>
</table>
<p class="submit">
	<input name="mailer" type="submit" id="mailersub" class="button-primary" value="<?php esc_attr_e('Send', 'news-mailer') ?>" />
</p>
</form>

</div>

<?php
}
?>