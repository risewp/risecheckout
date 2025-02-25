<?php
defined( 'ABSPATH' ) || exit;

risecheckout_get_header( 'checkout' );

echo WC_Shortcodes::checkout( array() );

risecheckout_get_footer( 'checkout' );
