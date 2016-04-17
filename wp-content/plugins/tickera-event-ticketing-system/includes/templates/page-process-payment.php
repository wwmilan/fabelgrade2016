<?php

global $tc, $tc_gateway_plugins;

$cart_total		 = 0;
$cart_contents	 = $tc->get_cart_cookie();

if ( !isset( $_REQUEST[ 'tc_choose_gateway' ] ) ) {
	//_e( 'Something went wrong: Gateway is not selected.', 'tc' );
	$payment_class_name = $tc_gateway_plugins[ apply_filters( 'tc_not_selected_default_gateway', 'free_orders' ) ][ 0 ];
} else {
	$payment_class_name = $tc_gateway_plugins[ $_REQUEST[ 'tc_choose_gateway' ] ][ 0 ];
}

$payment_gateway = new $payment_class_name;

if ( !session_id() ) {
	session_start();
}

$cart_total = $_SESSION[ 'tc_cart_total' ];

if ( !empty( $cart_contents ) && count( $cart_contents ) > 0 ) {

	if ( $tc->checkout_error == false ) {
		$payment_gateway->process_payment( $cart_contents );
		exit;
	} else {
		wp_redirect( $this->get_payment_slug( true ) );
		tc_js_redirect( $this->get_payment_slug( true ) );
		exit;
	}
} else {//The cart is empty and this page shouldn't be reached
	wp_redirect( $this->get_payment_slug( true ) );
	tc_js_redirect( $this->get_payment_slug( true ) );
	exit;
}
?>