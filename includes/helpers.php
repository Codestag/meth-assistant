<?php
/**
 * Contact form Ajax submission
 *
 * @return void
 */
function stag_ajax_contact_form() {
	$message = '';
	$type = 'success';

	if ( wp_verify_nonce( $_POST['_wpnonce'], 'contact-nonce' ) ) {
		if( ( $_POST['contact_name'] || $_POST['contact_email'] || $_POST['contact_description'] ) != '' ) {
			$name    = $_POST['contact_name'];
			$email   = $_POST['contact_email'];
			$message = $_POST['contact_description'];

			if( ! is_email( $email ) ) {
				$message = __( 'Incorrect email address.', 'meth-assistant' );
				$type = 'error';
			} else {
				$emailTo = stag_theme_mod( 'theme_options', 'contact-email' );
				if ( !isset( $emailTo ) || ( $emailTo == '' ) ) {
				  $emailTo = get_option( 'admin_email' );
				}

				$subject = __( '[Contact Form] From ', 'meth-assistant' ) . $name;
				$body    = "Name: {$name} \n\nEmail: {$email} \n\nMessage: {$message} \n\n";
				$body    .= "--\n";
				$body    .= "This mail is sent via contact form on " . get_bloginfo('name') . "\n";
				$body    .= home_url();

				$headers = 'From: ' . $name . ' <' . $email . '>' . "\r\n" . 'Reply-To: ' . $email;

				wp_mail( $emailTo, $subject, $body, $headers );

				$message = __( 'Thanks, your email was sent successfully.', 'meth-assistant' );
			}
		} else {
			$message = __( 'Please fill-in all fields.', 'meth-assistant' );
			$type = 'error';
		}
	} else {
		$message = __( 'Playing Naughty, huh?', 'meth-assistant' );
		$type = 'error';
	}

	echo json_encode(array(
		'message' => $message,
		'type'    => $type
	));
	die;
}
add_action( 'wp_ajax_contact_form', 'stag_ajax_contact_form' );
add_action( 'wp_ajax_nopriv_contact_form', 'stag_ajax_contact_form' );
