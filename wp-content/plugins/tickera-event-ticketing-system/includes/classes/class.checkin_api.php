<?php

if ( !defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly
//if ( isset( $_REQUEST[ 'ct_json' ] ) ) {
//}

if ( !class_exists( 'TC_Checkin_API' ) ) {

	class TC_Checkin_API {

		var $api_key			 = '';
		var $ticket_code		 = '';
		var $page_number		 = 1;
		var $results_per_page = 10;
		var $keyword			 = '';

		function __construct( $api_key, $request, $return_method = 'echo', $ticket_code = '', $execute_request = true ) {
			global $wp;

			$this->api_key = $api_key;

			$checksum			 = isset( $wp->query_vars[ 'checksum' ] ) ? $wp->query_vars[ 'checksum' ] : (isset( $_REQUEST[ 'checksum' ] ) ? $_REQUEST[ 'checksum' ] : '');
			$page_number		 = isset( $wp->query_vars[ 'page_number' ] ) ? $wp->query_vars[ 'page_number' ] : (isset( $_REQUEST[ 'page_number' ] ) ? $_REQUEST[ 'page_number' ] : apply_filters( 'tc_ticket_info_default_page_number', 1 ));
			$results_per_page	 = isset( $wp->query_vars[ 'results_per_page' ] ) ? $wp->query_vars[ 'results_per_page' ] : (isset( $_REQUEST[ 'results_per_page' ] ) ? $_REQUEST[ 'results_per_page' ] : apply_filters( 'tc_ticket_info_default_results_per_page', 50 ));
			$keyword			 = isset( $wp->query_vars[ 'keyword' ] ) ? $wp->query_vars[ 'keyword' ] : (isset( $_REQUEST[ 'keyword' ] ) ? $_REQUEST[ 'keyword' ] : '');

			if ( $checksum !== '' ) {
				$findme	 = 'checksum'; //old QR code character
				$pos	 = strpos( $checksum, $findme );
				if ( $pos === false ) {//new code
					//$checksum
				} else {//old code
					$ticket_strings_array	 = explode( '%7C', $checksum ); //%7C = |
					$checksum				 = end( $ticket_strings_array );
				}
			}

			$this->ticket_code		 = apply_filters( 'tc_ticket_code_var_name', isset( $ticket_code ) && $ticket_code != '' ? $this->extract_checksum_from_code( $ticket_code ) : $checksum  );
			$this->page_number		 = apply_filters( 'tc_tickets_info_page_number_var_name', $page_number );
			$this->results_per_page	 = apply_filters( 'tc_tickets_info_results_per_page_var_name', $results_per_page );
			$this->keyword			 = apply_filters( 'tc_tickets_info_keyword_var_name', $keyword );

			if ( $execute_request ) {
				header( 'Content-Type: application/json' );

				if ( $request == apply_filters( 'tc_translation_request_name', 'tickera_translation' ) ) {
					$this->translation();
				}

				if ( $request == apply_filters( 'tc_check_credentials_request_name', 'tickera_check_credentials' ) ) {
					$this->check_credentials();
				}

				if ( $request == apply_filters( 'tc_event_essentials_request_name', 'tickera_event_essentials' ) ) {
					$this->get_event_essentials();
				}

				if ( $request == apply_filters( 'tc_checkins_request_name', 'tickera_checkins' ) ) {
					$this->ticket_checkins();
				}

				if ( $request == apply_filters( 'tc_scan_request_name', 'tickera_scan' ) ) {
					$this->ticket_checkin( $return_method );
				}

				if ( $request == apply_filters( 'tc_tickets_info_request_name', 'tickera_tickets_info' ) ) {
					$this->tickets_info();
				}
			}
		}

		function get_license_key() {
			$tc_general_settings = get_option( 'tc_general_setting', false );
			$license_key		 = (defined( 'TC_LCK' ) && TC_LCK !== '') ? TC_LCK : (isset( $tc_general_settings[ 'license_key' ] ) && $tc_general_settings[ 'license_key' ] !== '' ? $tc_general_settings[ 'license_key' ] : '');
			return $license_key;
		}

		function extract_checksum_from_code( $code ) {
			if ( $code !== '' ) {
				$findme	 = 'checksum'; //old or QR code characters
				$pos	 = strpos( $code, $findme );

				if ( $pos === false ) {//new code
					//$checksum
				} else {//old code
					$ticket_strings_array	 = explode( '|', $code ); //received from barcode reader addon
					$new_code				 = end( $ticket_strings_array );

					if ( $code == $new_code ) {
						$ticket_strings_array	 = explode( '%7C', $code ); //received from mobile apps
						$code					 = end( $ticket_strings_array );
					} else {
						$code = $new_code;
					}
				}
			}

			return $code;
		}

		function get_api_event() {
			return get_post_meta( $this->get_api_key_id(), 'event_name', true );
		}

		function get_api_key_id() {
			$args = array(
				'post_type'		 => 'tc_api_keys',
				'post_status'	 => 'any',
				'posts_per_page' => 1,
				'meta_key'		 => 'api_key',
				'meta_value'	 => $this->api_key
			);

			$post = get_posts( $args );

			if ( $post ) {
				return $post[ 0 ]->ID;
			} else {
				return false;
			}
		}

		function translation( $echo = true ) {

			if ( $this->get_api_key_id() ) {
				$data = array(
					'WORDPRESS_INSTALLATION_URL' => 'WORDPRESS INSTALLATION URL',
					'API_KEY'					 => 'API KEY',
					'AUTO_LOGIN'				 => 'AUTO LOGIN',
					'SIGN_IN'					 => 'SIGN IN',
					'SOLD_TICKETS'				 => 'TICKETS SOLD',
					'CHECKED_IN_TICKETS'		 => 'CHECKED-IN TICKETS',
					'HOME_STATS'				 => 'Home - Stats',
					'LIST'						 => 'LIST',
					'SIGN_OUT'					 => 'SIGN OUT',
					'CHECK_IN'					 => 'CHECK IN',
					'CANCEL'					 => 'CANCEL',
					'SEARCH'					 => 'Search',
					'ID'						 => 'ID',
					'PURCHASED'					 => 'PURCHASED',
					'CHECKINS'					 => 'CHECK-INS',
					'CHECK_IN'					 => 'CHECK IN',
					'SUCCESS'					 => 'SUCCESS',
					'SUCCESS_MESSAGE'			 => 'Ticket has been check-in',
					'OK'						 => 'OK',
					'ERROR'						 => 'ERROR',
					'ERROR_MESSAGE'				 => 'Wrong ticket code',
					'PASS'						 => 'Pass',
					'FAIL'						 => 'Fail',
					'ERROR_LOADING_DATA'		 => 'Error loading data. Please check the URL and API KEY provided',
					'API_KEY_LOGIN_ERROR'		 => 'Error. Please check the URL and API KEY provided',
					'APP_TITLE'					 => 'Ticket Check-in',
					'PLEASE_WAIT'				 => 'Please wait...',
					'EMPTY_LIST'				 => 'The list is empty'
				);
			} else {
				$data = array(
					'pass' => false //api key is NOT valid
				);
			}

			$json = json_encode( apply_filters( 'tc_translation_data_output', $data ) );

			if ( $echo ) {
				echo $json;
				exit;
			} else {
				return $json;
			}
		}

		function check_credentials( $echo = true ) {

			if ( $this->get_api_key_id() ) {
				$data = array(
					'pass'			 => true, //api key is valid
					'license_key'	 => $this->get_license_key(),
					'admin_email'	 => get_option( 'admin_email' )
				);
			} else {
				$data = array(
					'pass' => false //api key is NOT valid
				);
			}

			$json = json_encode( apply_filters( 'tc_check_credentials_data_output', $data ) );

			if ( $echo ) {
				echo $json;
				exit;
			} else {
				return $json;
			}
		}

		function get_event_essentials( $echo = true ) {

			if ( $this->get_api_key_id() ) {

				$event_id = $this->get_api_event();

				$event_ticket_types = array();

				if ( $event_id == 'all' ) {
					$wp_events_search = new TC_Events_Search( '', '', '', 'publish' );
					foreach ( $wp_events_search->get_results() as $event ) {
						$event_obj			 = new TC_Event( $event->ID );
						$event_ticket_types	 = array_merge( $event_ticket_types, $event_obj->get_event_ticket_types() );
					}
				} else {
					$event				 = new TC_Event( $event_id );
					$event_ticket_types	 = $event->get_event_ticket_types();
				}

				$event_tickets_total	 = 0;
				$event_checkedin_tickets = 0;

				$meta_query = array( 'relation' => 'OR' );

				foreach ( $event_ticket_types as $event_ticket_type ) {
					$meta_query[] = array(
						'key'	 => 'ticket_type_id',
						'value'	 => (string) $event_ticket_type,
					);
				}

				$args = array(
					'post_type'		 => 'tc_tickets_instances',
					'post_status'	 => 'any',
					'posts_per_page' => -1,
					'meta_query'	 => $meta_query
				);

				$ticket_instances = get_posts( $args );

				$tickets_sold = 0;

				foreach ( $ticket_instances as $ticket_instance ) {
					$order = new TC_Order( $ticket_instance->post_parent );

					if ( isset( $order->details->post_status ) && $order->details->post_status == 'order_paid' ) {
						$order_is_paid = true;
					} else {
						$order_is_paid = false;
					}

					$order_is_paid = apply_filters( 'tc_order_is_paid', $order_is_paid, $order->details->ID );

					if ( $order_is_paid ) {
						$tickets_sold++;
					}

					$checkins = get_post_meta( $ticket_instance->ID, 'tc_checkins', true );

					if ( isset( $checkins ) && is_array( $checkins ) && count( $checkins ) > 0 ) {
						$event_checkedin_tickets++;
					}
				}

				$event_tickets_total = $tickets_sold;

				$data = array(
					'event_name'			 => $event_id == 'all' ? __( 'Multiple Events', 'tc' ) : stripslashes( $event->details->post_title ),
					'event_date_time'		 => $event_id == 'all' ? __( 'N/A', 'tc' ) : date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $event->details->event_date_time ), false ),
					'event_location'		 => $event_id == 'all' ? __( 'N/A', 'tc' ) : stripslashes( $event->details->event_location ),
					'event_logo'			 => $event_id == 'all' ? __( 'N/A', 'tc' ) : stripslashes( $event->details->event_logo_file_url ),
					'event_sponsors_logos'	 => $event_id == 'all' ? __( 'N/A', 'tc' ) : stripslashes( $event->details->sponsors_logo_file_url ),
					'sold_tickets'			 => $event_tickets_total,
					'checked_tickets'		 => $event_checkedin_tickets,
					'pass'					 => true
				);

				$json = json_encode( apply_filters( 'tc_get_event_essentials_data_output', $data, $event_id ) );

				if ( $echo ) {
					echo $json;
					exit;
				} else {
					return $json;
				}
			}
		}

		function ticket_checkins( $echo = true ) {
			if ( $this->get_api_key_id() ) {
				$ticket_id		 = ticket_code_to_id( $this->ticket_code );
				$ticket_instance = new TC_Ticket_Instance( $ticket_id );
				$check_ins		 = get_post_meta( $ticket_id, 'tc_checkins', true );
				$rows			 = array();
				$check_ins		 = apply_filters( 'tc_ticket_checkins_array', $check_ins );

				if ( isset( $check_ins ) && count( $check_ins ) > 0 && is_array( $check_ins ) ) {
					foreach ( $check_ins as $check_in ) {
						$r[ 'date_checked' ] = tc_format_date( $check_in[ 'date_checked' ] );
						$r[ 'status' ]		 = apply_filters( 'tc_check_in_status_title', $check_in[ 'status' ] );
						$rows[]				 = array( 'data' => $r );
					}
				}
				echo json_encode( $rows );
				exit;
			}
		}

		function ticket_checkin( $echo = true ) {

			if ( $this->get_api_key_id() ) {

				$api_key_id	 = $this->get_api_key_id();
				$ticket_id	 = ticket_code_to_id( $this->ticket_code );

				if ( $ticket_id ) {

					$ticket_instance = new TC_Ticket_Instance( $ticket_id );
					$ticket_type_id	 = apply_filters( 'tc_ticket_type_id', $ticket_instance->details->ticket_type_id );
					$ticket_type	 = new TC_Ticket( $ticket_type_id );
					$order			 = new TC_Order( $ticket_instance->details->post_parent );

					if ( $order->details->post_status == 'order_paid' ) {
						$order_is_paid = true;
					} else {
						$order_is_paid = false;
					}

					$order_is_paid = apply_filters( 'tc_order_is_paid', $order_is_paid, $order->details->ID );

					if ( $order_is_paid ) {
						//all good, continue with check-in process
					} else {
						_e( 'Ticket does not exist', 'tc' );
						exit;
					}

					$ticket_event_id = $ticket_type->get_ticket_event( $ticket_type_id );
				} else {
					_e( 'Ticket does not exist', 'tc' );
					exit;
				}

				if ( $this->get_api_event() != $ticket_event_id ) {//Only API key for the parent event can check-in this ticket
					if ( $this->get_api_event() !== 'all' ) {
						if ( $echo ) {
							_e( 'Insufficient permissions. This API key cannot check-in this ticket.', 'tc' );
						} else {
							return 403; //error code for incufficient persmissions
						}
						exit;
					}
				}

				$check_ins = $ticket_instance->get_ticket_checkins();

				$num_of_check_ins = apply_filters( 'tc_num_of_checkins', (is_array( $check_ins ) ? count( $check_ins ) : 0 ) );

				$available_checkins				 = get_post_meta( $ticket_type_id, 'available_checkins_per_ticket', true );
				$alternate_available_checkins	 = get_post_meta( $ticket_type_id, '_available_checkins_per_ticket', true );

				$available_checkins	 = !empty( $available_checkins ) ? $available_checkins : $alternate_available_checkins;
				$available_checkins	 = (is_numeric( $available_checkins ) ? $available_checkins : 9999); //9999 means unlimited check-ins but it's set for easier comparation

				if ( $available_checkins > $num_of_check_ins ) {
					$check_in_status		 = apply_filters( 'tc_checkin_status_name', true );
					$check_in_status_bool	 = true;
					do_action( 'tc_check_in_notification', $ticket_id );
				} else {
					$check_in_status		 = apply_filters( 'tc_checkin_status_name', false );
					$check_in_status_bool	 = false;
				}

				$new_checkins = array();

				if ( is_array( $check_ins ) ) {
					foreach ( $check_ins as $check_in ) {
						$new_checkins[] = $check_in;
					}
				}

				$new_checkin = array(
					"date_checked"	 => time(),
					"status"		 => $check_in_status ? apply_filters( 'tc_checkin_status_name', 'Pass' ) : apply_filters( 'tc_checkin_status_name', 'Fail' ),
					"api_key_id"	 => $api_key_id
				);

				$new_checkins[] = apply_filters( 'tc_new_checkin_array', $new_checkin );

				do_action( 'tc_before_checkin_array_update' );

				update_post_meta( $ticket_id, "tc_checkins", $new_checkins );

				do_action( 'tc_after_checkin_array_update' );

				$payment_date = apply_filters( 'tc_checkin_payment_date', tc_format_date( apply_filters( 'tc_ticket_checkin_order_date', $order->details->tc_order_date, $order->details->ID ) ) ); //date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $order->details->tc_order_date, false )

				if ( $payment_date == '' ) {
					$payment_date = 'N/A';
				}

				$name = apply_filters( 'tc_checkin_owner_name', $ticket_instance->details->first_name . ' ' . $ticket_instance->details->last_name );

				if ( trim( $name ) == '' ) {
					$name = 'N/A';
				}

				$address = apply_filters( 'tc_checkin_owner_address', $ticket_instance->details->address );

				if ( $address == '' ) {
					$address = 'N/A';
				}

				$city = apply_filters( 'tc_checkin_owner_city', $ticket_instance->details->city );

				if ( $city == '' ) {
					$city = 'N/A';
				}

				$state = apply_filters( 'tc_checkin_owner_state', $ticket_instance->details->state );

				if ( $state == '' ) {
					$state = 'N/A';
				}

				$country = apply_filters( 'tc_checkin_owner_country', $ticket_instance->details->country );

				if ( $country == '' ) {
					$country = 'N/A';
				}

				$data = array(
					'status'			 => $check_in_status_bool, //false
					'previous_status'	 => '',
					'pass'				 => true, //api is valid
					'name'				 => $name,
					'payment_date'		 => $payment_date,
					'address'			 => $address,
					'city'				 => $city,
					'state'				 => $state,
					'country'			 => $country,
					'checksum'			 => $this->ticket_code
				);

				$buyer_full_name = isset( $order->details->tc_cart_info[ 'buyer_data' ][ 'first_name_post_meta' ] ) ? ($order->details->tc_cart_info[ 'buyer_data' ][ 'first_name_post_meta' ] . ' ' . $order->details->tc_cart_info[ 'buyer_data' ][ 'last_name_post_meta' ]) : '';
				$buyer_email	 = isset( $order->details->tc_cart_info[ 'buyer_data' ][ 'email_post_meta' ] ) ? $order->details->tc_cart_info[ 'buyer_data' ][ 'email_post_meta' ] : '';

				$data[ 'custom_fields' ] = array(
					array( apply_filters( 'tc_ticket_checkin_custom_field_title', 'Ticket Type' ), apply_filters( 'tc_checkout_owner_info_ticket_title', $ticket_type->details->post_title, $ticket_type->details->ID ) ),
					array( apply_filters( 'tc_ticket_checkin_custom_field_title', 'Buyer Name' ), apply_filters( 'tc_ticket_checkin_buyer_full_name', $buyer_full_name, $order->details->ID ) ),
					array( apply_filters( 'tc_ticket_checkin_custom_field_title', 'Buyer E-mail' ), apply_filters( 'tc_ticket_checkin_buyer_email', $buyer_email, $order->details->ID ) ),
				);

				$data[ 'custom_fields' ] = apply_filters( 'tc_checkin_custom_fields', $data[ 'custom_fields' ], $ticket_instance->details->ID, $ticket_event_id, $order, $ticket_type );

				apply_filters( 'tc_checkin_output_data', $data );

				if ( $echo === true || $echo == 'echo' ) {
					echo json_encode( $data );
					exit;
				} else {
					return $data;
				}
			}
		}

		function tickets_info( $echo = true ) {
			if ( $this->get_api_key_id() ) {
				global $wpdb;

				/* $event_id = $this->get_api_event();

				  $query = apply_filters( 'tc_tickets_info_query', "SELECT tcp.ID, tcp.post_type, tco.post_status
				  FROM $wpdb->posts tcp
				  LEFT JOIN $wpdb->posts tco ON tcp.post_parent = tco.ID
				  WHERE tcp.post_type = 'tc_tickets_instances'
				  AND (tco.post_status = 'order_paid')
				  ORDER BY tco.post_date
				  DESC LIMIT %d
				  OFFSET %d
				  " );

				  $results = $wpdb->get_results(
				  $wpdb->prepare( $query, $this->results_per_page, (( $this->page_number - 1 ) * $this->results_per_page ) )
				  , OBJECT ); */


				/* START OF THE NEW API */
				$event_id = $this->get_api_event();

				$event_ticket_types = array();

				if ( $event_id == 'all' ) {
					$wp_events_search = new TC_Events_Search( '', '', '', 'publish' );
					foreach ( $wp_events_search->get_results() as $event ) {
						$event_obj			 = new TC_Event( $event->ID );
						$event_ticket_types	 = array_merge( $event_ticket_types, $event_obj->get_event_ticket_types() );
					}
				} else {
					$event				 = new TC_Event( $event_id );
					$event_ticket_types	 = $event->get_event_ticket_types();
				}

				$meta_query = array( 'relation' => 'OR' );

				foreach ( $event_ticket_types as $event_ticket_type ) {
					$meta_query[] = array(
						'key'	 => 'ticket_type_id',
						'value'	 => (string) $event_ticket_type,
					);
				}

				$orders_args = array(
					'posts_per_page' => -1,
					'post_type'		 => apply_filters( 'tc_order_post_type_name', array( 'tc_orders' ) ),
					'post_status'	 => apply_filters( 'tc_paid_post_statuses', array( 'order_paid' ) ),
					'fields'		 => 'ids',
					'cache_results'	 => false
				);

				$query			 = new WP_Query( $orders_args );
				$paid_orders_ids = $query->get_posts();

				$args = array(
					'post_type'			 => 'tc_tickets_instances',
					'post_status'		 => 'publish',
//					'posts_per_page' => -1,
					'meta_query'		 => $meta_query,
					'posts_per_page'	 => $this->results_per_page,
					'offset'			 => (( $this->page_number - 1 ) * $this->results_per_page ),
					'post_parent__in'	 => $paid_orders_ids
				);

				$results = get_posts( $args );
				/* END OF THE NEW API */

				$results_count = 0;

				foreach ( $results as $result ) {
					$ticket_instance = new TC_Ticket_Instance( $result->ID );
					$ticket_type	 = new TC_Ticket( $ticket_instance->details->ticket_type_id );

					//$ticket_type_event_id = get_post_meta( apply_filters( 'tc_ticket_type_id', $ticket_type->details->ID ), apply_filters( 'tc_event_name_field_name', 'event_name' ), true );

					$order = new TC_Order( $ticket_instance->details->post_parent );

					$continue = false;

					if ( in_array( $order->details->post_status, apply_filters( 'tc_paid_post_statuses', array( 'order_paid' ) ) ) ) {
						$order_is_paid = true;
					} else {
						$order_is_paid = false;
					}

					$order_is_paid = apply_filters( 'tc_order_is_paid', $order_is_paid, $order->details->ID );

					if ( $order_is_paid ) {
						/* OLD */
						$check_ins		 = get_post_meta( $ticket_instance->details->ID, 'tc_checkins', true );
						$checkin_date	 = '';

						if ( !empty( $check_ins ) ) {
							foreach ( $check_ins as $check_in ) {
								$checkin_date = tc_format_date( $check_in[ 'date_checked' ] );
							}
						}

						$r[ 'date_checked' ] = $checkin_date;

						$r[ 'payment_date' ]	 = tc_format_date( strtotime( $order->details->post_modified ) ); //date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $order->details->post_modified ), false )
						$r[ 'transaction_id' ]	 = $ticket_instance->details->ticket_code;
						$r[ 'checksum' ]		 = $ticket_instance->details->ticket_code;

						$buyer_full_name = isset( $order->details->tc_cart_info[ 'buyer_data' ][ 'first_name_post_meta' ] ) ? ($order->details->tc_cart_info[ 'buyer_data' ][ 'first_name_post_meta' ] . ' ' . $order->details->tc_cart_info[ 'buyer_data' ][ 'last_name_post_meta' ]) : '';
						$buyer_email	 = isset( $order->details->tc_cart_info[ 'buyer_data' ][ 'email_post_meta' ] ) ? $order->details->tc_cart_info[ 'buyer_data' ][ 'email_post_meta' ] : '';

						if ( !empty( $ticket_instance->details->first_name ) && !empty( $ticket_instance->details->last_name ) ) {
							$r[ 'buyer_first' ]	 = $ticket_instance->details->first_name;
							$r[ 'buyer_last' ]	 = $ticket_instance->details->last_name;
						} else {
							$r[ 'buyer_first' ]	 = apply_filters( 'tc_ticket_checkin_buyer_first_name', $order->details->tc_cart_info[ 'buyer_data' ][ 'first_name_post_meta' ], $order->details->ID );
							$r[ 'buyer_last' ]	 = apply_filters( 'tc_ticket_checkin_buyer_last_name', $order->details->tc_cart_info[ 'buyer_data' ][ 'last_name_post_meta' ], $order->details->ID );
						}

						$r[ 'custom_fields' ] = array(
							array( apply_filters( 'tc_ticket_checkin_custom_field_title', 'Ticket Type' ), apply_filters( 'tc_checkout_owner_info_ticket_title', $ticket_type->details->post_title, $ticket_type->details->ID ) ),
							array( apply_filters( 'tc_ticket_checkin_custom_field_title', 'Buyer Name' ), apply_filters( 'tc_ticket_checkin_buyer_full_name', $buyer_full_name, $order->details->ID ) ),
							array( apply_filters( 'tc_ticket_checkin_custom_field_title', 'Buyer E-mail' ), apply_filters( 'tc_ticket_checkin_buyer_email', $buyer_email, $order->details->ID ) ),
						);

						$r[ 'custom_fields' ] = apply_filters( 'tc_checkin_custom_fields', $r[ 'custom_fields' ], $result->ID, $event_id, $order, $ticket_type );

						$r[ 'custom_field_count' ] = count( $r[ 'custom_fields' ] );

						$r[ 'address' ] = '';
						if ( $r[ 'address' ] == '' ) {
							$r[ 'address' ] = 'N/A';
						}

						$r[ 'city' ] = '';
						if ( $r[ 'city' ] == '' ) {
							$r[ 'city' ] = 'N/A';
						}

						$r[ 'state' ] = '';
						if ( $r[ 'state' ] == '' ) {
							$r[ 'state' ] = 'N/A';
						}

						$r[ 'country' ] = '';
						if ( $r[ 'country' ] == '' ) {
							$r[ 'country' ] = 'N/A';
						}

						$rows[] = array( 'data' => $r );
						/* END OLD */

						$results_count++;
					}
				}

				$additional[ 'results_count' ]	 = $results_count;
				$rows[]							 = array( 'additional' => $additional );
				echo json_encode( $rows );
				exit;
			}
		}

	}

}
?>