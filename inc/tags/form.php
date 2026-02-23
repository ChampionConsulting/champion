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


# recapcha
if (       isset(\championcore\wedge\config\get_json_configs()->json->recapcha_site_key)
	  and (\strlen(\championcore\wedge\config\get_json_configs()->json->recapcha_site_key) > 0)
	  and    isset(\championcore\wedge\config\get_json_configs()->json->recapcha_secret_key)
	  and (\strlen(\championcore\wedge\config\get_json_configs()->json->recapcha_secret_key) > 0)) {
	
	 \championcore\get_context()->theme->js->add('https://www.google.com/recaptcha/api.js');
}

#create if needed
$errors = (isset($errors) and \is_array($errors)) ? $errors : array();

#initialisation
$success = false;
$text    = '';

if (     isset($_POST['submit']) and empty($_POST['human'])
	  and (isset($_SESSION['form_email_allowed_token']) and ($_SESSION['form_email_allowed_token'] == true))) {
	
	$flag_recapcha_ok = true;
	
	# recapcha verification step
	if (     isset(\championcore\wedge\config\get_json_configs()->json->recapcha_site_key)
	  and (\strlen(\championcore\wedge\config\get_json_configs()->json->recapcha_site_key) > 0)
	  and    isset(\championcore\wedge\config\get_json_configs()->json->recapcha_secret_key)
	  and (\strlen(\championcore\wedge\config\get_json_configs()->json->recapcha_secret_key) > 0)) {
		
		$curl_post_data = array(
			'secret'   => \championcore\wedge\config\get_json_configs()->json->recapcha_secret_key,
			'response' => $_POST['g-recaptcha-response'],
			'remoteip' => $_SERVER['REMOTE_ADDR']
		);
		
		$curl_handle = \curl_init( 'https://www.google.com/recaptcha/api/siteverify' );
		
		\curl_setopt($curl_handle, CURLOPT_POST, 1);
		\curl_setopt($curl_handle, CURLOPT_SAFE_UPLOAD, false); # required as of PHP 5.6.0
		\curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $curl_post_data);
		\curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true );
		
		# dangerous - makes request insecure
		# workaround for "Curl error: SSL certificate problem: unable to get local issuer certificate"
		#\curl_setopt($curl_handle, \CURLOPT_SSL_VERIFYPEER, false); 
		
		$status = \curl_exec($curl_handle);
		
		$curl_error = \curl_error($curl_handle);
		
		\curl_close($curl_handle);
		
		\championcore\invariant(($status !== false), "Curl error detected: {$curl_error}" );
		
		$unpacked = \json_decode( $status );
		
		if ($unpacked->success !== true) {
			
			$flag_recapcha_ok = false;
			
			$errors[] = $GLOBALS['lang_form_error_recapcha'];
		}
	}
	
	if ($flag_recapcha_ok === true) {
		# clear email token
		$_SESSION['form_email_allowed_token'] = false;
		
		$merge_tester = \array_merge(
			((array)\championcore\wedge\config\get_json_configs()->json->mail_inputs),
			((array)\championcore\wedge\config\get_json_configs()->json->mail_textarea)
		);
		
		foreach ($merge_tester as $field => $type) {
			$raw_field = $field;
			$field = \str_replace(" ", "_", $field);
			
			if (!isset($_POST[$field]) or empty($_POST[$field]) or (\strlen(\trim($_POST[$field])) < 1) ) {
				$field = \str_replace("_", " ", $field);
				$errors[] = $GLOBALS['lang_form_error1'] . $field;
			}	
			if (\strlen($_POST[$field]) > 1000) {
				$field = \str_replace("_", " ", $field);
				$errors[] = $GLOBALS['lang_form_error2a']. $field . $GLOBALS['lang_form_error2b'];
			}
			if (($raw_field == \championcore\wedge\config\get_json_configs()->json->lang_form_email) and (filter_var($_POST[$field], \FILTER_VALIDATE_EMAIL) == false)) {
				$errors[] = $GLOBALS['lang_form_error1'] . $raw_field;
			}
		}
		
		#deduplicate
		$errors = \array_unique($errors);
		
		if (empty($errors)) {
			
			$sender_name  = '';
			$sender_email = '';
			
			foreach ($merge_tester as $field => $type) {
				$field1    = \str_replace(" ", "_", $field);
				$submitted = \trim($_POST[$field1]);
				
				if ($field == \championcore\wedge\config\get_json_configs()->json->lang_form_name) {
					$sender_name = $submitted;
	
				} else if ($field == \championcore\wedge\config\get_json_configs()->json->lang_form_email) {
					$sender_email = $submitted;
	
				}
				
				# ensure ALL fields in the mail body
				$text .= ($field . ': ' . $submitted . "\n\n");
			}
			
			$mail = new \PHPMailer\PHPMailer\PHPMailer();
		
			if (    (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_host    ) > 0)
					and (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_username) > 0)
					and (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_password) > 0)
					and (\strlen(\championcore\wedge\config\get_json_configs()->json->smtp_port    ) > 0)
				) {
					// If your host requires smtp authentication, uncomment and fill out the lines below. 
					$mail->isSMTP();                                                                      // Do nothing here
					$mail->Host       = \championcore\wedge\config\get_json_configs()->json->smtp_host;      // Specify main server
					$mail->SMTPAuth   = true;                                                             // Do nothing here
					$mail->Username   = \championcore\wedge\config\get_json_configs()->json->smtp_username;  // SMTP username
					$mail->Password   = \championcore\wedge\config\get_json_configs()->json->smtp_password;  // SMTP password
					$mail->Port       = \championcore\wedge\config\get_json_configs()->json->smtp_port;      // SMTP port 	
					$mail->SMTPSecure = 'tls';                                                            // Enable encryption, 'ssl' also accepted
			}
			
			$mail->From     = $sender_email;
			$mail->FromName = $sender_name;

			/* @Docs From field for forms: 
				To avoid abuse and spam, we currently do not provide a default
				"from" address. However, we are leaving the code here in case a
				customer modifies the form code to make one without a from address.
				If code is enabled and the form submitted doesnt have a "From" email, we need
				to provide one. We use the one saved under configurations from 
				the admin settings. Failing that, we use a hardcoded email address as a failsafe.

				@todo For branding, adjust the failsafe email domain
				@branding Change the email below.
			* /
			if (!isset($sender_email) || $sender_email=='') {
				
				$admin_email = reset(\championcore\wedge\config\get_json_configs()->json->email_contact); # first contact email in list
				
				if (!isset($admin_email) || $admin_email =='') {
					$admin_email = "no-reply-forms@mycompany.com";
				}
				
				$mail->From = $admin_email;
				// @Docs Most SMTP servers will work with a blank name, so there is no failsafe if 
				// sender name is missing.
				$mail->FromName = \championcore\wedge\config\get_json_configs()->json->administrator_name;
			}
			*/

			foreach (\championcore\wedge\config\get_json_configs()->json->email_contact as $email_address) {
				$mail->addAddress($email_address);
			}
			$mail->Subject  = (isset(\championcore\wedge\config\get_json_configs()->json->config_contact_form_subject_line) ? \championcore\wedge\config\get_json_configs()->json->config_contact_form_subject_line : $GLOBALS['lang_form_subject_line']);
			$mail->Body     = $text;
							
			if ($mail->send()) {
				echo "\n<p class='green'>{$GLOBALS['lang_form_email_sent']}</p>\n";
				
				if (\championcore\wedge\config\get_json_configs()->json->config_contact_form_auto_thank) {
					echo "\n<p class='green'>{$GLOBALS['lang_auto_thank_contact']}</p>\n";
				}
				
				$success = true;
				unset($mail,$merge_tester);
				
				# handle optional redirect
				if (($success === true) and (\strlen(\championcore\wedge\config\get_json_configs()->json->contact_form_redirect) > 0)) {
					\header( 'Location: ' . \championcore\wedge\config\get_json_configs()->json->contact_form_redirect );
					exit;
				}
			}
		}
	}
}

if (!empty($errors) or (isset($success) and ($success == false))) {
	if (!empty($errors)) {
		echo "\n";
		foreach($errors as $error){
			echo "<p>{$error}</p>\n";
		}
	}
	
	# set email token to allow email to be sent
	$_SESSION['form_email_allowed_token'] = true;
	
	$form_inputs = [];
	
	$default_configs = \championcore\wedge\config\default_json_configs();
	
	$site_configs = \championcore\wedge\config\get_json_configs()->json;
	
	$form_inputs[] = (object)[
		'key'   => 'Name',
		'label' => (($site_configs->lang_form_name == $default_configs->lang_form_name) ? $GLOBALS['lang_form_name'] : $site_configs->lang_form_name),
		'id'    => \str_replace(' ', '_', $site_configs->lang_form_name),
		'type'  => $site_configs->mail_inputs->Name,
	];
	
	$form_inputs[] = (object)[
		'key'   => 'Email',
		'label' => (($site_configs->lang_form_email == $default_configs->lang_form_email) ? $GLOBALS['lang_form_email'] : $site_configs->lang_form_email),
		'id'    => \str_replace(' ', '_', $site_configs->lang_form_email),
		'type'  => $site_configs->mail_inputs->Email
	];
	
	$form_inputs[] = (object)[
		'key'   => 'Phone',
		'label' => (($site_configs->lang_form_phone == $default_configs->lang_form_phone) ? $GLOBALS['lang_form_phone'] : $site_configs->lang_form_phone),
		'id'    => \str_replace(' ', '_', $site_configs->lang_form_phone),
		'type'  => $site_configs->mail_inputs->Phone
	];
	
	$form_inputs[] = (object)[
		'key'   => 'Comment',
		'label' => (($site_configs->lang_form_comment == $default_configs->lang_form_comment) ? $GLOBALS['lang_form_comment'] : $site_configs->lang_form_comment),
		'id'    => \str_replace(' ', '_', $site_configs->lang_form_comment),
		'type'  => 'textarea'
	];
	
	if ($site_configs->gdpr->enable_in_form) {
		$form_inputs[] = (object)[
			'key'      => 'GDPR',
			'label' => (($site_configs->lang_form_gdpr == $default_configs->lang_form_gdpr) ? $GLOBALS['lang_form_gdpr'] : $site_configs->lang_form_gdpr),
			'id'       => \str_replace(' ', '_', $site_configs->lang_form_gdpr),
			'type'     => 'checkbox',
			'required' => true
		];
	}
	
?>

<!-- //
<form id="contact" method="post" action="">
	
	<?php foreach ($form_inputs as $value) { ?>
		<?php if ($value->key !== 'Comment') { ?>
			<label><?php echo \htmlentities($value->label); ?></label>
			<input id="<?php echo \htmlentities($value->key); ?>" name="<?php echo \htmlentities($value->key); ?>" type="<?php echo \htmlentities($value->type); ?>" value="<?php echo \htmlentities(isset($_POST[$value->key]) ? $_POST[$value->key] : ''); ?>" <?php echo ((isset($value->required) and $value->required) ? 'required' : '' ); ?> />
			<br />
		<?php } else { ?>
			
			<label><?php echo \htmlentities($value->label); ?></label>
			<textarea name="<?php echo \htmlentities($value->key); ?>" id="<?php echo \htmlentities($value->key); ?>" rows="<?php echo \htmlentities(\championcore\wedge\config\get_json_configs()->json->mail_textarea->{$value->key}); ?>" ><?php echo \htmlentities(\trim(isset($_POST[$value->key]) ? $_POST[$value->key] : '')); ?></textarea>
			<br />
		<?php } ?>
	<?php } ?>
	
	<input id="human" name="human" type="text"  value="<?php echo (isset($_POST['human']) ? $_POST['human'] : null);?>" /> 
	<button name="submit" type="submit"><?php echo $GLOBALS['lang_form_sent_button']; ?></button>
	
	<?php if (       isset(\championcore\wedge\config\get_json_configs()->json->recapcha_site_key)
	  and (\strlen(\championcore\wedge\config\get_json_configs()->json->recapcha_site_key) > 0)
	  and    isset(\championcore\wedge\config\get_json_configs()->json->recapcha_secret_key)
	  and (\strlen(\championcore\wedge\config\get_json_configs()->json->recapcha_secret_key) > 0)) { ?>
	
		<div class="g-recaptcha" data-sitekey="<?php echo \championcore\wedge\config\get_json_configs()->json->recapcha_site_key; ?>"></div>
	<?php } ?>
	
</form>

<?php } ?>
<link rel="stylesheet" href="<?php echo \championcore\wedge\config\get_json_configs()->json->path; ?>/inc/tags/css/form.css" />

//-->

<?php
/**
 * Created by PhpStorm.
 * User: panayiotisgeorgiou
 * Date: 16/12/16
 */

$str_num1 = rand(1,20);
$str_num2 = rand(1,20);
$_SESSION['expect_answer'] = $str_num1 + $str_num2;
?>
	
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>

<script type="text/javascript">
        $(document).ready(function() {
            $("#submit_contact").click(function() {

                var is_validation = true;
                //simple validation at client's end
                //loop through each field and we simply change border color to red for invalid fields
                $("#contact_form input[required=true], #contact_form textarea[required=true]").each(function(){
                    $(this).css('border-color','');
                    if(!$.trim($(this).val())){ //if this field is empty
                        $(this).css('border-color','red'); //change border color to red
                        is_validation = false; //set do not is_validation flag
                    }
                    //check invalid email
                    var email_reg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                    if($(this).attr("type")=="email" && !email_reg.test($.trim($(this).val()))){
                        $(this).css('border-color','red'); //change border color to red
                        is_validation = false; //set do not is_validation flag
                    }
                });

                if(is_validation) //everything looks good! proceed...
                {
                    //get input field values data to be sent to server
                    post_data = {
                        'user_name'		: $('input[name=name]').val(),
                        'user_email'	: $('input[name=email]').val(),
                        'phone_number'	: $('input[name=phone2]').val(),
                        'subject'		: $('select[name=subject]').val(),
                        'message'		: $('textarea[name=message]').val(),
                        'captcha_answer': $('input[name=captcha_answer]').val()
                    };

                    //Ajax post data to server
                    $.post('<?php echo \championcore\wedge\config\get_json_configs()->json->path; ?>/inc/plugins/contactform/submit.php', post_data, function(response){
                        if(response.type == 'error'){ //load json data from server and output message
                            output = '<div class="error">'+response.text+'</div>';
                        }else{
                            output = '<div class="success">'+response.text+'</div>';
                            $("#contact_form  input[required=true], #contact_form textarea[required=true]").val('');
                            $("#contact_form #contact_body").slideUp(); //hide form after success
                        }
                        $("#contact_form #contact_results").hide().html(output).slideDown();
                    }, 'json');
                }
            });

            //reset previously set border colors and hide all message on .keyup()
            $("#contact_form  input[required=true], #contact_form textarea[required=true]").keyup(function() {
                $(this).css('border-color','');
                $("#result").slideUp();
            });
        });
    </script>
<link href="<?php echo \championcore\wedge\config\get_json_configs()->json->path; ?>/inc/plugins/contactform/style.css" rel="stylesheet" type="text/css" />

<div class="form-style" id="contact_form">
    <div class="form-style-heading">Please fill in the following form to contact our </div>
    <div id="contact_results"></div>
    <form id="contact" method="post" action="">
	<div id="contact_body">
        <label><span>Your Name <span class="required">*</span></span>
            <input type="text" name="name" id="name" required="true" class="input-field"/>
        </label>
        <label><span>Email <span class="required">*</span></span>
            <input type="email" name="email" required="true" class="input-field"/>
        </label>
        <label><span>Phone <span class="required">*</span></span>
            <input type="text" name="phone2" maxlength="15"  required="true" class="input-field" />
        </label>
        <label for="subject"><span>Regarding</span>
            <select name="subject" class="select-field">
                <option value="General Question">General Question</option>
                <option value="Technical Support">Technical Support</option>
				<option value="Billing">Billing Department</option>
                <option value="Sales">Sales Department</option>
				<option value="Live-Chat">Live Chat</option>
            </select>
        </label>
        <label for="field5"><span>Message <span class="required">*</span></span>
            <textarea name="message" id="message" class="textarea-field" required="true"></textarea>
        </label>
        <label><span>Are you human?</span>
            <?php echo $str_num1 .' + '. $str_num2 ; ?> = <input type="text" name="captcha_answer" required="true" class="tel-number-field long" />
        </label>
        <label>
            <span>&nbsp;</span><input type="submit" id="submit_contact" value="Submit" />
        </label>
    </div>
</div>
</form>