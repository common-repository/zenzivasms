<?php

function getPhoneNumbers($user_list){
    $phone_numbers = "";
    $comma = ", ";
    $size = sizeof($user_list);
    $count = $size;

    foreach($user_list as $s_user){
        $single = true;
        $number = get_usermeta($s_user->ID, 'mobilenumber', $single);

        if($number != ""){
            $phone_numbers .= $number.$comma;
        }
        $count =  $count - 1;
    }
    $phone_numbers = substr($phone_numbers, 0, -2);
    return $phone_numbers;
}

// Removes duplicate numbers and sends message
function send_SMS ($destination, $text){
    $destination = explode(',', $destination);
    $jml = count($destination);
    
    for($i=0; $i<=$jml-1; $i++){
	    //global $user_ID;
	    mb_internal_encoding("UTF-8");
	    mb_http_output("UTF-8");
	    $username = get_option('userkey');
    	$password = get_option('passkey');
	    $url = get_option('http_api');
	    
	    // REGULER http://zenziva.com/apps/smsapi.php?userkey=f21hv4&passkey=12345&nohp=6285862067888&pesan=test sms
	    
	    $content =  $url.
	    						'?userkey='.rawurlencode($username).
	                '&passkey='.rawurlencode($password).
	                '&nohp='.rawurlencode($destination[$i]).
	                '&pesan='.rawurlencode($text);
	
	    $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $content);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$getresponse = curl_exec($ch);
			curl_close($ch);
	    $xmldata = new SimpleXMLElement($getresponse);
	    $status = $xmldata->message[0]->text;
  	}
		if($status == "Success"){
			return "Message Sent";
		}else{
			return "Message Failed<br />".$status;
		}
		
}

// adds mobile number field to the contact list displayed
function contact_mobile( $contactmethods ) {
  // Add Mobile
  $contactmethods['mobilenumber'] = 'Mobile Number';
  return $contactmethods;
}

//adds the mobile number column to the user table
function mobile_column( $defaults ) {
  $defaults['mobile_column'] = __('Mobile Number', 'user-column');
  return $defaults;
}

// returns the mobile number
function get_mobile_number($value, $column_name, $id) {
  if( $column_name == 'mobile_column' ) {
    if ( current_user_can('edit_users') )
      return get_usermeta($id, 'mobilenumber');
  }
}

//returns list of user roles
function getAllUsersWithRole($role) {
    $args = "";
    if($role != "all"){
        $args = array('role' => $role);
    }
        $wp_user_search = get_users($args);
        return $wp_user_search;
}

//Updates SMSGlobal settings
function update($user_id, $username, $password, $url){
    update_option("userkey", $username);
    update_option("passkey", $password);
    update_option("http_api", $url);
}

function getIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
?>