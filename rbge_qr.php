<?php
/*
Plugin Name: RBGE QR-Code Plugin
Description: Displays official RBGE QR Codes in the admin interface for posts
Version: 0.1
Author: Roger Hyam
License: GPL2
*/

add_action('add_meta_boxes', array('RbgeQr','init') );

class RbgeQr {

	// Create the function used in the action hook
	public static function init() {
		error_log("RbgeQr:init");
		add_meta_box(
		            'rbge_qr_meta_box',
		            'RBGE QR-Code Generator',
		             array('RbgeQr','render'),
		             'post',
					 'side'
		        );	
	}

	public static function render($post) {
		//error_log(print_r($post, true));
		
		$viewURL = plugins_url('generator.php', __FILE__) . "?data=" . $post->ID ."&size";
		$downloadURL = plugins_url('generator.php', __FILE__) . "?download=true&data=" . $post->ID ."&size";
		
		echo "<p>Click one of the links below to view or download a QR Code that links to this story.</p>";
		
		echo "<ul>";
		
		echo "<li><strong>Small:</strong> [<a href=\"$viewURL=4\">View</a>] [<a href=\"$downloadURL=4\">Download</a>]</li>";
		echo "<li><strong>Medium:</strong> [<a href=\"$viewURL=10\">View</a>] [<a href=\"$downloadURL=10\">Download</a>]</li>";
		echo "<li><strong>Large:</strong> [<a href=\"$viewURL=18\">View</a>] [<a href=\"$downloadURL=18\">Download</a>]</li>";
		
		echo "</ul>";
		
		echo "<p style=\"color: red;\">Remember to check the QR Code works correctly in its printed form before publishing it.</p>";
	} 

}



?>