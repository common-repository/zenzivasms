<script type='text/javascript' src='<?php bloginfo('url');?>/wp-content/plugins/zenzivasms/js/jquery.validate.min.js'></script>
<script type="text/javascript">
	function reloadimg(){
		document.getElementById("captchah").src="<?php echo WP_PLUGIN_URL?>/zenzivasms/lib/captcha.php?"+Math.random();
	}
</script>

<?php
	require_once ('functions.php');
	
	if ( $_POST['sms_send_'.$sms_section_from] ){
		
		if (empty($_SESSION['captchah']) || trim(strtolower($_POST['captchah'])) != $_SESSION['captchah']) {
	    $captcha = "invalid";
	  } else {
	    $captcha = "valid";
	  }
	  
	  if($captcha == "valid"){
			$ceklimit = get_option('sms_limit');
			$theip = getIP();
			$traceip = get_option($theip);
			
			// indonesia time
			$timezone = "Asia/Jakarta";
			if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
			$tgl=date('y-m-d');
			
			if($traceip == ""){
		      if(update_option($theip, "1:".$tgl,true)){
		          $traceip = '0';
		      }
		  } else {
		  	$traceip = get_option($theip);
		  	$pecah = explode(":", $traceip);
				$traceip = $pecah[0];
				$tglnya = $pecah[1];
		  }
			
			if( $ceklimit != 0 && $ceklimit <= $traceip && $tgl == $tglnya ){
				$sms_show_msg	=	 "Message Failed !<br /><i>Maksimal ".$ceklimit." SMS per hari</i>";
				$sms_show_class	=	 "err";
			}else{
				if($tgl != $tglnya){
					$traceip = 1;
				}else{
					$traceip = $traceip+1;
				}
				$username = get_option('userkey');
		    $password = get_option('passkey');
		    $destination = $_POST['sms_mob_number'];
		    $text = urlencode(substr($_POST['sms_mob_msg'],0,150));
		    $url = get_option('http_api');
				
				$content =  $url.
		    						'?userkey='.rawurlencode($username).
		                '&passkey='.rawurlencode($password).
		                '&nohp='.rawurlencode($destination).
		                '&pesan='.htmlentities($text);
		                
				mb_internal_encoding("UTF-8");
	    	mb_http_output("UTF-8");
		    $ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $content);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				$getresponse = curl_exec($ch);
				curl_close($ch);
		    $xmldata = new SimpleXMLElement($getresponse);
		    $status = $xmldata->message[0]->text;

				if($status == "Success"){
					//$traceip = $traceip+1;
					update_option($theip, $traceip.":".$tgl);
					//return "Message Sent";
					$sms_show_msg	=	 "Message Sent";
					$sms_show_class	=	 "ok";
				}else{
					//return "Message Failed<br />".$status;
					$sms_show_msg	=	 "Message Failed<br /><i>".$status."<br />Hubungi administrator</i>";
					$sms_show_class	=	 "err";
				}
			}
		}else{
			$sms_show_msg	=	"Captcha salah";
			$sms_show_class	=	 "err";
		}
		unset($_SESSION['captcha']);
		
	}
	/*
	elseif( $_POST['sms_send_'.$sms_section_from] && !isset($_POST['captcha']) )
	{
		$sms_show_msg	=	 "Captcha Salah<i>".$status."<br />Masukkan gambar sesuai perintah ke lingkaran</i>";
		$sms_show_class	=	 "err";
	}
	*/
?>
<script type="text/javascript">
	//jQuery.cookie('<?php echo $UniqueFieldID; ?>', '<?php echo $UniqueSMSID; ?>', { expires: 7, path: '/' });
</script>



<div id="send_sms">
	<?php if (!empty($sms_show_msg)): ?>
    	<div id="sms_msg_box" class="<?php echo $sms_show_class; ?>">
        	<?php 
				echo $sms_show_msg;
				$sms_show_msg = '';
			?>
        </div>
	<?php endif; ?>
    <form action="" method="post" id="send_sms_form" class="validation">
    <ul class="sms_form">
    	<li>
        	<label>Nomor</label>
            <input type="text" class="required number" value="62" name="sms_mob_number" />
            <em>Format : 62xxxxx / 0xxxxx</em>
        </li>
        
        <li>
        	<label>Pesan</label>
            <textarea class="required" name="sms_mob_msg" maxlength="150"></textarea>
            <em>Maksimal 150 karakter</em>
        </li>
        
        <li>
					<img src="<?php echo WP_PLUGIN_URL?>/zenzivasms/lib/captcha.php" id="captchah" />
					<a href="#" onclick="javascript:reloadimg(); return false" id="change-image"><img src="<?php echo WP_PLUGIN_URL?>/zenzivasms/lib/img/reload.png" title="Change Image" /></a><br />
					<input type="text" class="required" value="" name="captchah" id="capt"/>
				</li>
								
        <!--
        <li>
        	<div class="ajax-fc-container">Captcha</div>
        </li>
        -->
        <li>
        	<input type="hidden" name="UniqueFieldID" value="<?php echo $UniqueFieldID; ?>" />
            <input type="hidden" name="<?php echo $UniqueFieldID; ?>" value="<?php echo $UniqueSMSID; ?>" />
            <input type="submit" class="button-primary" name="sms_send_<?php echo $sms_section_from ?>" value="Send" />
        </li>        
    </ul>
    </form>
</div>
<script>
	jQuery('#send_sms_form').validate();
</script>