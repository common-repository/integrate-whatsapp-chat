<?php
/**
*@package integratewhatsappchat
*/
/*
Plugin Name: Integrate WhatsApp Chat
Plugin URI: https://www.whatsappapi.in/wordpress
Description: Send OTP, Alert, Notification, Invoice, Booking Information, Image (.png or .jpg), PDF file directly on customer WhatsApp.
Version: 1.0.0
Author: WhatsAppAPI.in
Author URI: https://www.whatsappapi.in
License: GPLv2 or later
Text Domain: integrate-whatsapp-chat
*/

defined("ABSPATH") or die("Hey, You can't access this file.");

if(!function_exists("add_action"))
{
	echo "Hey, You can't access this file.";
	exit();
}

add_action('admin_menu', 'whatsappchat_plugin_setup_menu');
 
function whatsappchat_plugin_setup_menu(){
        add_menu_page( 'WhatsApp Chat Setting', 'WhatsApp Chat', 'manage_options', 'whatsapp-chat-setting', 'whatsappchat_init' ,plugin_dir_url( __FILE__ ) .'icon/logo.png');
}
function whatsappchat_init()
{
	whatsappchat_handle_post();
	global $wpdb;
	$table = $wpdb->prefix . 'whatsappchat_setting';
	$row_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table LIMIT 1" ,ARRAY_A));
	// echo $thepost->post_title; 
	?>
	<div class="wrap"> 
	    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	    <form method="post">
	    	<?php wp_nonce_field('whatsappchat-action', 'whatsappchat-nonce'); ?>
		    <table cellpadding="5">
		    	<tr>
		    		<td>From Number</td>
		    		<td><input type="text" name="whatsapp-number" placeholder="eg. 917777877080" value="<?php echo $row_data->from_no; ?>" size="50" required /></td>
		    	</tr>
		    	<tr>
		    		<td>Your API Key</td>
		    		<td><input type="text" name="whatsapp-key" placeholder="eg. 544sd6f546s5df4" value="<?php echo $row_data->token; ?>" size="50" required /></td>
		    	</tr>
		    </table>
    		<?php
        	submit_button();
	        ?>
	    </form>
	 	<fieldset>
	 		<legend><h1>How to use?</h1></legend>
	 		<p><strong>Step 1</strong> - Generate your api key & pair whatsapp number on <a target="_blank" href="https://www.whatsappapi.in">https://www.whatsappapi.in</a></p>
	 		<p><strong>Step 2</strong> - Fill up details above form</p>
	 		<p><strong>Step 3</strong> - Use below function from anywhere to message on whatsapp </p>
	 		<p><strong>Send Text Message</strong> - whatsappMessage($country_code='91',$number = '987654****',$message = 'Order Notification on WhatsApp');</p>
	 		<p><strong>Send Image</strong> - whatsappMessage($country_code='91',$number = '987654****',$message = 'https://www.whatsappapi.in/front-assets/img/logo.png',$is_file = true);</p>
	 		<p><strong>Send PDF</strong> - whatsappMessage($country_code='91',$number = '987654****',$message = 'https://www.whatsappapi.in/dummy.pdf',$is_file = true);</p>
	 		<br />
	 		<p><strong>Notes</strong> - file_get_contents() function should be enable on server</p>
	 	</fieldset>
	</div><!-- .wrap --> <?php
}
function whatsappchat_handle_post()
{
	if(!empty($_POST['whatsapp-key']) && ctype_alnum($_POST['whatsapp-key']) && !empty($_POST['whatsapp-number']) && is_numeric($_POST['whatsapp-number']))
	{
		if(wp_verify_nonce($_REQUEST['whatsappchat-nonce'], 'whatsappchat-action') && (current_user_can('editor') || current_user_can('administrator')))
		{
			global $wpdb;
			$whatsapp_key = sanitize_text_field($_POST['whatsapp-key']);
			$whatsapp_no = sanitize_text_field($_POST['whatsapp-number']);
		    $table = $wpdb->prefix . 'whatsappchat_setting';
		    $whatsapp_row_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table;",ARRAY_A));
		    if(!empty($whatsapp_row_count))
		    {
		    	$success = $wpdb->query( $wpdb->prepare("UPDATE $table 
			                SET from_no = %s, token = %s 
			             WHERE id > 0 limit 1",$whatsapp_no, $whatsapp_key, ARRAY_A)
			    );
		    	if($success)
		    	{
		    		echo '<div class="updated notice"><p>Update Successfully</p></div>'; 
		    	}
		    }
		    else
		    {
			    $data = array(
			        'from_no' => $whatsapp_no,
			        'token' => $whatsapp_key
			    );
			    $format = array(
			        '%s'
			    );
			    $success=$wpdb->insert( $table, $data, $format );
			    if($success){
			        echo '<div class="updated notice"><p>Save Successfully</p></div>'; 
			    }
			}
		}
	}
}

function whatsappMessage($country_code,$number,$message,$is_file = false)
{
	global $wpdb;
  	global $table_name;
  	$api_link = 'https://www.whatsappapi.in/api?';
  	
  	$table_name = $wpdb->prefix . 'whatsappchat_setting';
  	$row_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name LIMIT 1" ,ARRAY_A));
  	if(!empty($row_data))
  	{
  		if($is_file === false)
  		{
	  		$parameters = 'token='.$row_data->token.'&action=text&from='.$row_data->from_no.'&country='.$country_code.'&to='.$number.'&uid='.uniqid().'&text='.urlencode($message);
	  	}
	  	else
	  	{
	  		$file_ext = pathinfo($message, PATHINFO_EXTENSION);
	  		if($file_ext == 'png' || $file_ext == 'jpg')
	  		{
	  			$parameters = 'token='.$row_data->token.'&action=image&from='.$row_data->from_no.'&country='.$country_code.'&to='.$number.'&uid='.uniqid().'&image='.urlencode($message);
	  		}
	  		elseif ($file_ext == 'pdf') {
	  			$parameters = 'token='.$row_data->token.'&action=pdf&from='.$row_data->from_no.'&country='.$country_code.'&to='.$number.'&uid='.uniqid().'&pdf='.urlencode($message);
	  		}
	  		else
	  		{
	  			return false;
	  		}
	  	}
	  	if(!empty($parameters))
	  	{
			file_get_contents($api_link.$parameters);
			return true;
		}
		else
		{
			return false;
		}
  	}
  	else
  	{
  		return false;
  	}
}

class WhatsAppChat
{
	function activate()
	{
		// generate CPT
		// flush rewrite rules
		global $wpdb;
	  	global $table_name;
	  	$table_name = $wpdb->prefix . 'whatsappchat_setting';
	 
		// create the ECPT metabox database table
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) 
		{
			$sql = "CREATE TABLE " . $table_name . " (
			`id` mediumint(9) NOT NULL AUTO_INCREMENT,
			`from_no` varchar(20) NOT NULL,
			`token` varchar(50) NOT NULL,
			PRIMARY KEY (id)
			);";
	 
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}
}

$whatsappChat = new WhatsAppChat();

// activation
register_activation_hook( __FILE__ , array('whatsappChat','activate'));

