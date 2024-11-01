<?php
/*
Plugin Name: Zenziva Contact Form
Plugin URI: http://www.zenziva.com
Description: Kirim email dan SMS alert ke administrator jika ada yang menghubungi melalui contact form. Kirim SMS alert kepada pengunjung yang telah mengisi contact form. Fitur kirim SMS menggunakan layanan dari <a href="http://www.zenziva.com" >Zenziva Online SMS Gateway</a>. Install dan masukkan shortcode <strong>[zconform]</strong> pada page atau post yang anda inginkan untuk menampilkan Contact Form.
Version: 1.5
Author: Hardcoder
Author URI: http://www.galerikita.net
License: 

Copyright (C) 2013  Zenziva
*/

if(!isset($_SESSION)){
	
	session_start();
}

function zconform_init() {
    global $user_ID;
    wp_enqueue_script('jquery');
    wp_enqueue_script('abah_script', WP_PLUGIN_URL .'/zenziva-contact-form/lib/js/karakter.js', array('jquery'), '1.0', true);		
}
add_action('init', 'zconform_init');

function zconform_shortcode(){
	global $post;
		$view = '<link rel="stylesheet" type="text/css" href="'.WP_PLUGIN_URL.'/zenziva-contact-form/lib/css/form.css">
		<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/zenziva-contact-form/lib/css/validationEngine.jquery.css" type="text/css" media="screen" title="no title" charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="'.WP_PLUGIN_URL.'/zenziva-contact-form/lib/css/template.css">
			<script src="'.WP_PLUGIN_URL.'/zenziva-contact-form/lib/js/jquery.validate.min.js" type="text/javascript"></script>
			<script type="text/javascript">
				function changeimg(){
					document.getElementById("captcha").src="'.WP_PLUGIN_URL.'/zenziva-contact-form/lib/captcha.php?"+Math.random();
				}
			</script>';
	
	if ( $_POST['contact_send'] ){
		if(get_option('zconform_captcha') == "1"){
			
			if (empty($_SESSION['captcha']) || trim(strtolower($_POST['captcha'])) != $_SESSION['captcha']) {
	        $captcha = "invalid";
	    } else {
	        $captcha = "valid";
	    }
		}else{
			$captcha = "valid";
		}
		
		if($captcha == "valid"){
			require_once ('lib/func.php');
			$destination = $_POST['nohp'];
			$text = get_option('sms_autorespon');
			$notif_admin = get_option('notify_admin');
			
			// Send Email
			if(get_option('zconform_smtp') == 1 && get_option('zconform_smtp_host') != "" && get_option('zconform_smtp_username') != "" && get_option('zconform_smtp_pass') != "" && get_option('zconform_smtp_port') != ""){
				require_once(WP_PLUGIN_DIR."/zenziva-contact-form/lib/class.phpmailer.php");
				
				$to = get_option('admin_email');
				$nama = get_option('zconform_name') == "1" ? 'Nama: '.$_POST['nama'] : 'Visitor';
				$email_pengirim = $_POST['email'];
				$tlp = get_option('zconform_nohp') == "1" ? '<br />Telephone: '.$_POST['nohp'] : '';
				$alamat = get_option('zconform_address') == "1" ? '<br />Alamat: '.$_POST['alamat'] : '';
				$pesan = get_option('zconform_msg') == "1" ? '<br /><br />'.$_POST['pesan'] : '';
				$subject = get_option('zconformsubjek');
				$isipesan = $nama."".$tlp."".$alamat."".$pesan;
				
				$mail = new PHPMailer(true);
				$mail->IsSMTP();
				try {
				  $mail->Host       = get_option('zconform_smtp_host');
				  $mail->SMTPDebug  = false;
				  $mail->SMTPAuth   = true;
				  $mail->Port       = get_option('zconform_smtp_port');
				  $mail->Username   = get_option('zconform_smtp_username');
				  $mail->Password   = get_option('zconform_smtp_pass');
				  $mail->AddAddress($to, $to);
				  $mail->SetFrom(get_option('zconform_smtp_email'), $_POST['nama']);
				  if(get_option('zconform_email') == "1" && $_POST['email'] != ""){
				  	$mail->AddReplyTo($email_pengirim, $nama);
				  }
				  $mail->IsHTML(true);
				  $mail->Subject = $subject;
				  $mail->Body = $isipesan;
				  //$mail->AltBody = $isipesan;
				  //$mail->MsgHTML(file_get_contents('contents.html'));
				  //$mail->AddAttachment('images/phpmailer.gif');
				  //$mail->AddAttachment('images/phpmailer_mini.gif');
				  //$mail->Send();
				  //echo "Message Sent OK</p>\n";
				  
				  if(!$mail->Send()) {
					  $sms_show_msg	=	"Mailer Error: " . $mail->ErrorInfo;
					  $sms_show_class	=	 "err";
					} else {
					  $sms_show_msg	=	get_option('msg_success');
						$sms_show_class	=	 "ok";
					}
				  
				}catch (phpmailerException $e) {
				  $stat_msg = $e->errorMessage();
				} catch (Exception $e) {
				  $stat_msg = $e->getMessage();
				}
				
				
			}else{
				$to = get_option('admin_email');
				$nama = get_option('zconform_name') == "1" ? 'Nama: '.$_POST['nama'] : '';
				$email_pengirim = get_option('zconform_email') == "1" ? $_POST['email'] : '';
				$tlp = get_option('zconform_nohp') == "1" ? '<br />Telephone: '.$_POST['nohp'] : '';
				$alamat = get_option('zconform_address') == "1" ? '<br />Alamat: '.$_POST['alamat'] : '';
				$pesan = get_option('zconform_msg') == "1" ? '<br /><br />'.$_POST['pesan'] : '';
				
				//$nama= $_POST['nama'];
				//$email_pengirim = $_POST['email'];
				//$tlp = $_POST['nohp'];
				//$alamat = $_POST['alamat'];
				//$pesan = $_POST['pesan'];
				$subject = get_option('zconformsubjek');
				$isipesan = $nama."".$tlp."".$alamat."".$pesan;
				
				$header = "MIME-Version: 1.0" . "\r\n";
				$header .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
				$header .= "From: ".$email_pengirim."\r\n" .
				     "X-Mailer: php";
				//mail($to, $subject, $isipesan, $header);
			
				// ---
			
				if (mail($to, $subject, $isipesan, $header))
				{
					$sms_show_msg	=	get_option('msg_success');
					$sms_show_class	=	 "ok";
				}else{
					$sms_show_msg	=	"Email tidak terkirim!<br /> Jika anda mengirim email pada localhost atau host anda telah menonaktifkan fungsi mail php, gunakan SMTP server";
					$sms_show_class	=	 "err";
				}
			}
			
			if($sms_show_class == "ok"){ // If the email has been sent, try to send SMS alert.
				if($text != ""){
					send($destination,$text);
				}
				
				if($notif_admin == "1"){
					$site = str_replace("http://","",get_option('siteurl'));
					$admin_email = get_option('admin_email');
					$alert = "Seorang pengunjung web anda $site, telah menghubungi anda melalui email $admin_email";
					$hpadmin = explode(",",get_option('hpadmin'));
					foreach ($hpadmin as $toadmin){
						if($toadmin){
							send($toadmin,$alert);
						}
					}
				}
				// ---
			}
			
		}else{
			$sms_show_msg	=	"Captcha salah";
			$sms_show_class	=	 "err";
		}
		unset($_SESSION['captcha']);
	}
	
	$width = get_option('zconformwidth');
	$inputwidth = 95;
	$textareawidth = 95;
	
	$view .= '<div id="form-div"' .($width!=""?'style="width: '.$width.'px"':"").'>';
		$view .='<div id="title">'.(get_option('zconformtitle')!=""?get_option('zconformtitle'):"Contact form").'</div>';
		
		if (!empty($sms_show_msg)){
			$view .= '<div id="sms_msg_box" class="'.$sms_show_class.'">';
				$view .= $sms_show_msg;
				$sms_show_msg = '';
			$view .= '</div>';
		}
	
		$view .= '<form action="" method="post" id="send_sms_form" class="validation">';
		$view .= '<ul class="sms_form">';
		
			if(get_option('zconform_name') == "1"){
			$view .= '<li>
									<label class="field">Nama</label>
									<input type="text" '.(get_option('zconform_name_r')=="1"?"class=required":"").' value="" name="nama" style="width: '.$inputwidth.'%; max-width: '.$inputwidth.'%;" />
								</li>';
			}
			
			if(get_option('zconform_email') == "1"){
			$view .= '<li>
									<label class="field">Email</label>
									<input type="text" '.(get_option('zconform_email_r')=="1"?"class=required":"").' value="" name="email" style="width: '.$inputwidth.'%; max-width: '.$inputwidth.'%;" />
								</li>';
			}
			
			if(get_option('zconform_nohp') == "1"){
			$view .= '<li>
									<label class="field">Nomor HP</label>
									<input type="text" '.(get_option('zconform_nohp_r')=="1"?"class=required":"").' value="" name="nohp" style="width: '.$inputwidth.'%; max-width: '.$inputwidth.'%;" />
								</li>';
			}
			
			if(get_option('zconform_address') == "1"){
			$view .= '<li>
									<label class="field">Alamat</label>
									<input type="text" '.(get_option('zconform_address_r')=="1"?"class=required":"").' value="" name="alamat" style="width: '.$inputwidth.'%; max-width: '.$inputwidth.'%;" />
								</li>';
			}
			
			if(get_option('zconform_msg') == "1"){
			$view .= '<li>
									<label class="field">Pesan</label>
									<textarea '.(get_option('zconform_msg_r')=="1"?"class=required":"").' name="pesan" style="width: '.$textareawidth.'%; max-width: '.$textareawidth.'%;" ></textarea>
								</li>';
			}
			
			if(get_option('zconform_captcha') == "1"){
			$view .= '<li>
									<img src="'.WP_PLUGIN_URL.'/zenziva-contact-form/lib/captcha.php" id="captcha" />
									<a href="#" onclick="javascript:changeimg(); return false" id="change-image"><img src="'.WP_PLUGIN_URL.'/zenziva-contact-form/lib/img/reload.png" title="Change Image" /></a><br />
									<input type="text" class="required" value="" name="captcha" id="capt"/>
								</li>';
			}
			
			$view .= '<li class="submit">
									<input type="submit" class="submit" name="contact_send" value="Send" />
								</li>';
			
		$view .= '</ul>';
		$view .= '</form>';
	$view .= '</div>';
	
	$view .= '<script>jQuery("#send_sms_form").validate();</script>';
	return $view;
}

add_shortcode('zconform','zconform_shortcode');

require_once ('lib/func.php');

if ( is_admin() ){
  add_action('admin_menu', 'zconform_menu');  
}

function zconform_menu() {
   add_menu_page('Zenziva Contact Form', 'zContact Form', 8, __FILE__, 'zconform_settings', WP_PLUGIN_URL . '/zenziva-contact-form/zconform.png');
   add_submenu_page(__FILE__, 'Dashboard', 'Dashboard', 8, 'dashboard', 'zenziva_dashboard');
}

function zconform_settings(){
    add_meta_box("settings_box", "User API Settings", "api_settings", "api_s");
    add_meta_box("settings_box", "Contact Form Settings", "form_settings", "form_s");
    add_meta_box("settings_box", "Mail Settings", "email_settings", "email_s");
    add_meta_box("settings_box", "More", "quick_link", "quick_s");
    add_meta_box("settings_box", "Information", "instruction", "instruction_s"); ?>
    <div class="wrap">
            <h2><?php _e('Zenziva Contact Form Settings') ?></h2>
          <form action="" name="api_settings" id="api_settings" method="POST">
            <div id="dashboard-widgets-wrap">
                    <div class="metabox-holder">
                            <div style="float:left; width:40%;" class="inner-sidebar1">
                                    <?php do_meta_boxes('api_s','advanced','');  ?>
                            </div>
                            <div style="float:left; width:33%;" class="inner-sidebar1">
                                    <?php do_meta_boxes('email_s','advanced','');  ?>
                            </div>
                            <div style="float:right; width:26%;" class="inner-sidebar1">
                                    <?php do_meta_boxes('quick_s','advanced','');  ?>
                            </div>
                            <div style="float:left; width:73%;" class="inner-sidebar1">
                                    <?php do_meta_boxes('form_s','advanced','');  ?>
                                    <span class="submit"><input type="submit" class="button-primary" value="Save Changes" /></span><br /><br /><br />
                            </div>
                            <div style="float:left; width:100%;" class="inner-sidebar1">
                                    <?php do_meta_boxes('instruction_s','advanced','');  ?>
                            </div>
                    </div>
            </div>
          </form>
    </div>
<?php  }

function zenziva_dashboard(){ ?>
    <div class="wrap">
    	<iframe src="http://www.zenziva.com/login/" frameBorder="0" width="98%" height="530"></iframe>    				
    </div>
<?php  }

function email_settings(){
    $zconform_smtp_host = get_option('zconform_smtp_host');
    $zconform_smtp_username = get_option('zconform_smtp_username');
    $zconform_smtp_pass = get_option('zconform_smtp_pass');
    $zconform_smtp_port = get_option('zconform_smtp_port');
    $zconform_smtp_email = get_option('zconform_smtp_email');
    
    if(isset($_POST['zconformuserkey'])){
        $zconform_smtp_host = $_POST['zconform_smtp_host'];
        $zconform_smtp_username = $_POST['zconform_smtp_username'];
        $zconform_smtp_pass = $_POST['zconform_smtp_pass'];
        $zconform_smtp_port = $_POST['zconform_smtp_port'];
        $zconform_smtp_email = $_POST['zconform_smtp_email'];
        $zconform_smtp = $_POST['zconform_smtp'];
        
        update_option("zconform_smtp", $zconform_smtp);
        update_option("zconform_smtp_host", $zconform_smtp_host);
    		update_option("zconform_smtp_username", $zconform_smtp_username);
    		update_option("zconform_smtp_pass", $zconform_smtp_pass);
    		update_option("zconform_smtp_port", $zconform_smtp_port);
    		update_option("zconform_smtp_email", $zconform_smtp_email);
        
    }?>

    <table width="100%" cellpadding="5" cellspacing="5" border="0">
				
				<tr>
        	<td valign="top">SMTP</td>
            <td><label><input type="checkbox" id="zconform_smtp" name="zconform_smtp" value="1" <?php echo get_option('zconform_smtp')=="1"?"checked":""; ?>> Gunakan SMTP server</label></td>
        </tr>
        <tr>
        	<td>SMTP Host</td>
            <td><input type="text" id="zconform_smtp_host" name="zconform_smtp_host" size="40px" value="<?php echo $zconform_smtp_host;?>"></td>
        </tr>
        <tr>
        	<td>SMTP Username</td>
            <td><input type="text" id="zconform_smtp_username" name="zconform_smtp_username" size="40px" value="<?php echo $zconform_smtp_username;?>"></td>
        </tr>
        <tr>
        	<td>SMTP Password</td>
            <td><input type="password" id="zconform_smtp_pass" name="zconform_smtp_pass" size="40px" value="<?php echo $zconform_smtp_pass;?>"></td>
        </tr>
        <tr>
        	<td>SMTP Port</td>
            <td><input type="text" id="zconform_smtp_port" name="zconform_smtp_port" size="40px" value="<?php echo $zconform_smtp_port;?>"></td>
        </tr>
        <tr>
        	<td>SMTP Email</td>
            <td><input type="text" id="zconform_smtp_email" name="zconform_smtp_email" size="40px" value="<?php echo $zconform_smtp_email;?>"></td>
        </tr>
	</table>
	<br />
	<div style="font-size:11px"><i>Gunakan SMTP jika host anda telah menonaktifkan fungsi mail php atau jika anda menjalankan script pada localhost.</i></div>
<?php }

function api_settings(){
    $username = get_option('zconformuserkey');
    $password = get_option('zconformpasskey');
    $url = get_option('zconformhttp_api');
    $hpadmin = get_option('hpadmin');
    $sms_autorespon = get_option('sms_autorespon');

    if(isset($_POST['zconformuserkey'])){
        $username = $_POST['zconformuserkey'];
        $password = $_POST['zconformpasskey'];
        $url = $_POST['zconformurl'];
        $notify_admin = $_POST['notify_admin'];
        $hpadmin = $_POST['hpadmin'];
        $sms_autorespon = $_POST['sms_autorespon'];
        
        update_option("zconformuserkey", $username);
    		update_option("zconformpasskey", $password);
    		update_option("zconformhttp_api", $url);
    		update_option("notify_admin", $notify_admin);
    		update_option("hpadmin", $hpadmin);
    		update_option("sms_autorespon", $sms_autorespon);
    
        //save_api($user_ID, $username, $password, $url);
        /*
        echo '<div class="updated"><p><strong>';
				echo __( 'API setting disimpan !' );
				echo '</strong></p></div>';
				*/
    }?>

    <table width="100%" cellpadding="5" cellspacing="5" border="0">
		
        <tr>
        	<td width="200">Userkey</td>
            <td><input type="text" id="zconformuserkey" name="zconformuserkey" size="40px" value="<?php echo $username;?>"></td>
        </tr>
        <tr>
        	<td>Passkey</td>
            <td><input type="password" id="zconformpasskey" name="zconformpasskey" size="40px" value="<?php echo $password;?>"></td>
        </tr>
        <tr>
        	<td>HTTP API</td>
            <td>
            	<input type="text" id="zconformurl" name="zconformurl" size="60px" value="<?php echo $url;?>">
            </td>
        </tr>
        <tr>
        	<td>SMS Notify</td>
            <td>
            	<label><input type="checkbox" id="notify_admin" name="notify_admin" value="1" <?php echo get_option('notify_admin')=="1"?"checked":""; ?>> Kirim SMS pemberitahuan ke admin</label>
            </td>
        </tr>
        <tr>
        	<td valign="top">Nomor HP Admin</td>
            <td>
            	<input type="text" id="hpadmin" name="hpadmin" size="40px" value="<?php echo $hpadmin;?>"><div style="font-size:11px"><i>Pisahkan dengan koma</i></div>
            </td>
        </tr>
        <tr>
        	<td valign="top">SMS auto respon</td>
            <td>
            	<!--<textarea onChange="Counter(this.form,"message");" onKeyPress="Counter(this.form,"message");" onKeyUp="Counter(this.form,"message");" onKeyDown="Counter(this.form,"message");" name="message" rows="6" cols="51"></textarea>
                        <DIV ID="CharCounter"></DIV>-->
              <textarea oncontextmenu="return false;" id="sms_autorespon" name="sms_autorespon" rows="6" cols="51" onFocus="toCount('sms_autorespon',155);", onChange="toCount('sms_autorespon',155);", onKeyUp="toCount('sms_autorespon',155);", onKeyDown="toCount('sms_autorespon',155);"><?php echo $sms_autorespon;?></textarea>
            	<DIV ID="CharCounter"></DIV>
            	<div style="font-size:11px"><i>Isi SMS akan dikirim ke pengunjung yang telah mengisi Contact Form. Jika mengaktifkan fungsi ini, isian Nomor HP harus ada pada form.<br />Kosongkan untuk menonaktifkan SMS auto repson.</i></div>
            </td>
        </tr>
        
	</table>
<?php }

function form_settings(){
    $formtitle = get_option('zconformtitle');
    $formsubjek = get_option('zconformsubjek');
    $formwidth = get_option('zconformwidth');
    $msg_success = get_option('msg_success');

    if(isset($_POST['zconformtitle'])){
        $formtitle = $_POST['zconformtitle'];
        $formsubjek = $_POST['zconformsubjek'];
        $formwidth = $_POST['zconformwidth'];
        $msg_success = $_POST['msg_success'];
        
        $zconform_name = $_POST['zconform_name'];
        $zconform_name_r = $_POST['zconform_name_r'];
        $zconform_email = $_POST['zconform_email'];
        $zconform_email_r = $_POST['zconform_email_r'];
        $zconform_nohp = $_POST['zconform_nohp'];
        $zconform_nohp_r = $_POST['zconform_nohp_r'];
        $zconform_address = $_POST['zconform_address'];
        $zconform_address_r = $_POST['zconform_address_r'];
        $zconform_msg = $_POST['zconform_msg'];
        $zconform_msg_r = $_POST['zconform_msg_r'];
        $zconform_captcha = $_POST['zconform_captcha'];
        
        update_option("zconformtitle", $formtitle);
    		update_option("zconformsubjek", $formsubjek);
    		update_option("zconformwidth", $formwidth);
    		update_option("msg_success", $msg_success);
    		
    		update_option("zconform_name", $zconform_name);
    		update_option("zconform_name_r", $zconform_name_r);
    		update_option("zconform_email", $zconform_email);
    		update_option("zconform_email_r", $zconform_email_r);
    		update_option("zconform_nohp", $zconform_nohp);
    		update_option("zconform_nohp_r", $zconform_nohp_r);
    		update_option("zconform_address", $zconform_address);
    		update_option("zconform_address_r", $zconform_address_r);
    		update_option("zconform_msg", $zconform_msg);
    		update_option("zconform_msg_r", $zconform_msg_r);
    		update_option("zconform_captcha", $zconform_captcha);
    		
        //save_api($user_ID, $username, $password, $url);
        echo '<div class="updated"><p><strong>';
				echo __( 'Contact form setting disimpan !' );
				echo '</strong></p></div>';
    }?>

    <!--<form action="" name="form_settings" id="form_settings" method="POST">-->
    	<table width="100%" cellpadding="5" cellspacing="5" border="0">
    		<tr>
    			<td width="50%">
				   	<table width="100%" cellpadding="5" cellspacing="5" border="0">
						
				        <tr>
				        	<td width="100">Nama Form</td>
				          <td><input type="text" id="zconformtitle" name="zconformtitle" size="40px" value="<?php echo $formtitle;?>"></td>
				        </tr>
				        <tr>
				        	<td>Subjek Form</td>
				          <td><input type="text" id="zconformsubjek" name="zconformsubjek" size="40px" value="<?php echo $formsubjek;?>"></td>
				        </tr>
				        <tr>
				        	<td>Ukuran Form</td>
				          <td>Width: <input type="text" id="zconformwidth" name="zconformwidth" size="5px" value="<?php echo $formwidth;?>">px &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Height: <input type="text" disabled="" size="5px" value="auto"></td>
				        </tr>
				        <tr>
				        	<td valign="top">Pesan sukses</td>
				          <td>
				            <textarea id="msg_success" name="msg_success" rows="3" cols="37" ><?php echo $msg_success;?></textarea>
				          </td>
				        </tr>
				        <!--
				        <tr>
				        	<td>&nbsp;</td>
				          <td><span class="submit"><input type="submit" class="button-primary" value="Save Changes" /></span></td>
				        </tr>
				        -->
						</table>
					</td>
					<td valign="top">
						<table width="100%" cellpadding="1" cellspacing="1" border="0">
							
								<tr>
									<td colspan="2"><strong>Pilihan Field Isian:</strong><br />&nbsp;</td>
								</tr>
				        <tr>
				        	<td width="100">
				          	<input type="checkbox" id="zconform_name" name="zconform_name" value="1" <?php echo get_option('zconform_name')=="1"?"checked":""; ?>> Nama
				          </td>
				          <td>
				          	<input type="checkbox" id="zconform_name_r" name="zconform_name_r" value="1" <?php echo get_option('zconform_name_r')=="1"?"checked":""; ?>> Harus diisi
				          </td>
				        </tr>
				        <tr>
				        	<td>
				          	<input type="checkbox" id="zconform_email" name="zconform_email" value="1" <?php echo get_option('zconform_email')=="1"?"checked":""; ?>> Email
				          </td>
				          <td>
				          	<input type="checkbox" id="zconform_email_r" name="zconform_email_r" value="1" <?php echo get_option('zconform_email_r')=="1"?"checked":""; ?>> Harus diisi
				          </td>
				        </tr>
				        <tr>
				        	<td>
				          	<input type="checkbox" id="zconform_nohp" name="zconform_nohp" value="1" <?php echo get_option('zconform_nohp')=="1"?"checked":""; ?>> Nomor HP
				          </td>
				          <td>
				          	<input type="checkbox" id="zconform_nohp_r" name="zconform_nohp_r" value="1" <?php echo get_option('zconform_nohp_r')=="1"?"checked":""; ?>> Harus diisi
				          </td>
				        </tr>
				        <tr>
				        	<td>
				          	<input type="checkbox" id="zconform_address" name="zconform_address" value="1" <?php echo get_option('zconform_address')=="1"?"checked":""; ?>> Alamat
				          </td>
				          <td>
				          	<input type="checkbox" id="zconform_address_r" name="zconform_address_r" value="1" <?php echo get_option('zconform_address_r')=="1"?"checked":""; ?>> Harus diisi
				          </td>
				        </tr>
				        <tr>
				        	<td>
				          	<input type="checkbox" id="zconform_msg" name="zconform_msg" value="1" <?php echo get_option('zconform_msg')=="1"?"checked":""; ?>> Isi Pesan
				          </td>
				          <td>
				          	<input type="checkbox" id="zconform_msg_r" name="zconform_msg_r" value="1" <?php echo get_option('zconform_msg_r')=="1"?"checked":""; ?>> Harus diisi
				          </td>
				        </tr>
				        <tr>
				        	<td>
				          	<input type="checkbox" id="zconform_captcha" name="zconform_captcha" value="1" <?php echo get_option('zconform_captcha')=="1"?"checked":""; ?>> Captcha
				          </td>
				        </tr>
				        
						</table>
					</td>
				</tr>
			</table>
			
  <!--</form>-->
<?php }

function instruction(){
	?>
		<div style="padding: 5px; font-size:11px">
			<ul class="sms_list">
				<li>HTTP API (Zenziva Reguler / Free Trial): <b>http://zenziva.com/apps/smsapi.php</b></li>
				<li>Userkey dan Passkey bisa didapatkan pada halaman member area SETTING > API SETTING.</li>
	      <li>Plugin Wordpress ZenzivaSMS menggunakan account dari Zenziva. Jika belum mempunyai account, silahkan <a href="http://www.zenziva.com/harga/" target="_blank">daftar</a> terlebih dahulu.</li>
	      <li>http API bisa didapatkan <a href="http://www.zenziva.com/apps/api.php" target="_blank">disini</a>. Pilih paket SMS yang akan digunakan (Reguler, Coorporate atau Masking) atau paket versi gratis (10 SMS per hari). Jika belum registrasi, silahkan daftar <a href="http://www.zenziva.com/harga/" target="_blank">disini</a></li>
	      <li>Install dan masukkan shortcode [zconform] pada page atau post yang anda inginkan untuk menampilkan Contact Form.</li>
      </ul><br />
			<i><strong>Catatan:</strong> Untuk paket versi gratis, pada akhir SMS akan disertakan tag "[sms by zenziva.com]" </i>
		</div>
		
<?php }

function quick_link(){?>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/id_ID/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>

            <table class="wp-list-table widefat fixed bookmarks">
            	<thead>
                <tr>
                	<th>Quick Links</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                	<td>
                    <ul class="sms_list">
                    	<li><a href="http://www.zenziva.com" target="_blank">Visit Our Site</a></li>
                      <li><a href="http://www.zenziva.com/apps/credit.php" target="_blank">Add SMS Credit</a></li>
                      <li><a href="http://zenziva.com/harga/" target="_blank">Price</a></li>
                      <li><a href="http://zenziva.com/artikel/" target="_blank">Article</a></li>
                      <li><a href="http://zenziva.com/kontak/" target="_blank">Contact us</a></li>
                    </ul>
                    </td>
                </tr>
                </tbody>
            </table>
            <br/>
            <table class="wp-list-table widefat fixed bookmarks">
            	<thead>
                <tr>
                	<th>Facebook</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                	<td><div class="fb-like-box" data-href="http://www.facebook.com/ZenzivaSmsBroadcast" data-width="267" data-show-faces="true" data-stream="false" data-header="true"></div></td>
                </tr>
                </tbody>
            </table>
<?php }?>