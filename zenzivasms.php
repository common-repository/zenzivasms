<?php
/*
Plugin Name: Zenziva SMS
Plugin URI: http://www.zenziva.com
Description: Kirim SMS dari WordPress, kirim SMS pemberitahuan kepada user / member tentang posting terbaru, widget kirim SMS gratis bagi pengunjung web/blog menggunakan <a href="http://www.zenziva.com" > Zenziva SMS </a>, hanya untuk Provider Indonesia. Anda bisa <a href="http://www.zenziva.com/harga/">Daftar</a> gratis untuk mencoba plugin ini.
Version: 1.6
Author: Hardcoder
Author URI: http://www.galerikita.net
License: 

Copyright (C) 2013  Zenziva

Originally By SMSGlobal
*/

function my_init() {
    global $user_ID;
    wp_enqueue_script('jquery');
    wp_enqueue_script('my_script', WP_PLUGIN_URL .' /zenzivasms/checkLength.js', array('jquery'), '1.0', true);

    if(get_usermeta($user_ID, 'sendBlogAlerts') != ""){
        add_filter('manage_posts_columns', 'add_send_column');
        add_action('manage_posts_custom_column',  'add_send_column_content');
    }
}
add_action('init', 'my_init');
require_once ('functions.php');

add_action('admin_menu', 'add_menu');

//creates left-menu - SMS Alert
function add_menu() {
   add_menu_page('ZenzivaSMS', 'ZenzivaSMS', 8, __FILE__, 'display_settings', WP_PLUGIN_URL . '/zenzivasms/zenzivasms.png');
   add_submenu_page(__FILE__, 'Send SMS', 'Send SMS', 8, 'SendSMS', 'sms_main_control');
}

// Add main control box
function sms_main_control(){
        add_meta_box("send_sms_box", "Send SMS", "sms_meta_box", "sms"); ?>

        <div class="wrap">
                <h2><?php _e('Send SMS from Wordpress') ?></h2>
                <div id="dashboard-widgets-wrap">
                        <div class="metabox-holder">
                                <div style="float:left; width:48%;" class="inner-sidebar1">
                                        <?php do_meta_boxes('sms','advanced','');  ?>
                                </div>
                        </div>
                </div>
        </div>
<?php }

// Add Send SMS meta box (wordpress css)
function sms_meta_box(){
    global $user_ID;
  ?>
    <div style="padding: 10px;">

<?php
    // if user has pressed Send Message - send message
    if(isset($_POST['user_type'])){
        if($_POST['user_type'] != "none"){
            $users_list = getAllUsersWithRole($_POST['user_type']);
            $phone_numbers = getPhoneNumbers($users_list);
        }
        else{
            $phone_numbers = $_POST['ph_numbers'];
        }
        $message = $_POST['message'];
        $r_message = send_SMS($phone_numbers, $message);
        echo $r_message;
    }
    //else display the form
    else {
    	$cekapi = get_option('http_api');
    	if(empty($cekapi)){
    		echo "HTTP API BELUM DI SET !<br /><font size='1px'><i>Silahkan isi http Api terlebih dahulu pada menu <a href='admin.php?page=zenzivasms/zenzivasms.php'>ZenzivaSMS</a>, untuk menggunakan fitur kirim SMS.</i></font>";
    	} else {
    	?>
        <form action="" name="send_sms_form" id="send_sms_form" method="POST" >
            
        	<table width="100%" cellpadding="2" cellspacing="2" border="0">
                <tr>
                	<td width="200"><font size="2"><br/>Nomor Tujuan</font></td>
                    <td><input name="ph_numbers" id="ph_numbers" type="text" size="54" /></td>
                </tr>
                <tr>
                	<td><strong><em>atau</em></strong> Pilih grup</td>
                    <td>
                    	<select name="user_type" id="user_type">
                            <option value="none">None</option>
                            <option value="all">All Users</option>
                            <option value="administrator">Admins</option>
                            <option value="editor">Editors</option>
                            <option value="author">Authors</option>
                            <option value="contributor">Contributors</option>
                            <option value="subscriber">Subscriber</option>
                        </select>
                    </td>
                </tr>
                <tr>
                	<td valign="top"><br/>Isi Pesan</td>
                    <td>
                    	<textarea onChange="updateTextBoxCounter(this.form);" onKeyPress="updateTextBoxCounter(this.form);" onKeyUp="updateTextBoxCounter(this.form);" onKeyDown="updateTextBoxCounter(this.form);" name="message" rows="6" cols="51"></textarea>
                        <DIV ID="InfoCharCounter"></DIV>
                    </td>         
                </tr>
                <tr>
                	<td>&nbsp;</td>
                    <td><span class="submit"><input type="submit" class="button-primary" value=" Send " /></span></td>
                </tr>
            </table>
        </form>
        <br /><br />
        <font size="1px"><i>Kirim SMS dari Wordpress, masukkan nomor tujuan atau pilih grup.<br />Jika ingin mengirim ke lebih dari satu nomor, pisahkan dengan koma tanpa spasi</i></font><br />
      <?php } ?>
    <?php } ?>
    </div>
<?php } 

// Mobile number fields and columns
add_filter('user_contactmethods','contact_mobile',10,1);
add_action('manage_users_custom_column', 'get_mobile_number', 15, 3);
add_filter('manage_users_columns', 'mobile_column', 15, 1);

//display Post Alert form and SMSGlobal Settings form
function display_settings(){
    add_meta_box("settings_box", "Alert Settings", "blog_settings", "blog_s");
    add_meta_box("settings_box", "User API Settings", "user_settings", "user_s");
    add_meta_box("settings_box", "Widget Setting", "widget_settings", "widget_s");
    add_meta_box("settings_box", "Instruction", "petunjuk_settings", "petunjuk_s");
    add_meta_box("settings_box", "More", "test_settings", "test_s"); ?>
    <div class="wrap">
            <h2><?php _e('Zenziva SMS Settings') ?></h2>
            <div id="dashboard-widgets-wrap">
                    <div class="metabox-holder">
                            <div style="float:left; width:74%;" class="inner-sidebar1">
                                    <?php do_meta_boxes('user_s','advanced','');  ?>
                                    <?php do_meta_boxes('blog_s','advanced','');  ?>
                                    <?php do_meta_boxes('widget_s','advanced','');  ?>
                                    <?php do_meta_boxes('petunjuk_s','advanced','');  ?>
                            </div>
                            <div style="float:right; width:25%;" class="inner-sidebar1">
                                    <?php do_meta_boxes('test_s','advanced','');  ?>
                            </div>
                    </div>
            </div>
    </div>
<?php  }

function widget_settings(){
    $smslimit = get_option('sms_limit');
    
    // to add meta data to the table
	  if($smslimit == ""){
	      if(update_option("sms_limit", 0,true)){
	          $smslimit = '0';
	      }
	  } else {
	  	$smslimit = get_option('sms_limit');
	  }

    if(isset($_POST['limit'])){
        $smslimit = $_POST['limit'];
        update_option("sms_limit", $smslimit);
        echo '<div class="updated"><p><strong>';
				echo __( 'Widget setting disimpan !' );
				echo '</strong></p></div>';
    }?>

    <form action="" name="widget_settings" id="blog_settings" method="POST" >
   	<table width="100%" cellpadding="5" cellspacing="5" border="0">
		
        <tr>
        	<td width="100">SMS Limit</td>
          <td><input type="text" id="limit" name="limit" value="<?php echo $smslimit;?>">&nbsp;<span class="submit"><input type="submit" class="button-primary" value="Save Changes" /></span></td>
        </tr>
	</table>
    </form>
    <br />
    <div style="padding: 5px; font-size:11px"><i>Anda dapat menentukan jumlah SMS per hari yang dapat dikirim oleh pengunjung dari widget Zenziva SMS.<br />Masukkan nilai 0 untuk tidak membatasi jumlah SMS yang dapat dikirm pengunjung.</b> </i></div>
<?php }

function test_settings(){
	?>
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
                	<td><div class="fb-like-box" data-href="http://www.facebook.com/ZenzivaSmsBroadcast" data-width="255" data-show-faces="true" data-stream="false" data-header="true"></div></td>
                </tr>
                </tbody>
            </table>
            <br/>
           
<?php }

function petunjuk_settings(){
	?>
		<div style="padding: 5px; font-size:11px">
			<ul class="sms_list">
	      <li>http API bisa didapatkan <a href="http://www.zenziva.com/apps/api.php" target="_blank">disini</a>. Pilih paket SMS yang akan digunakan (Reguler, Coorporate atau Masking) atau paket versi gratis (10 SMS per hari). Jika belum registrasi, silahkan daftar <a href="http://www.zenziva.com/harga/" target="_blank">disini</a></li>
	      <li>Setting Widget ( Appearance > Widgets ) masukkan widget ZenzivaSMS ke sidebar yang diinginkan.</li>
      </ul><br />
			<i><strong>Catatan:</strong> Untuk paket versi gratis, pada akhir SMS akan disertakan tag "[sms by zenziva.com]" </i>
		</div>
		
<?php }

// User settings form and store settings
function user_settings(){
    $username = get_option('userkey');
    $password = get_option('passkey');
    $url = get_option('http_api');

    if(isset($_POST['userkey'])){
        $username = $_POST['userkey'];
        $password = $_POST['passkey'];
        $url = $_POST['url'];
        update($user_ID, $username, $password, $url);
        echo '<div class="updated"><p><strong>';
				echo __( 'API setting disimpan !' );
				echo '</strong></p></div>';
    }?>

    <form action="" name="user_settings" id="blog_settings" method="POST" >
   	<table width="100%" cellpadding="5" cellspacing="5" border="0">
		
        <tr>
        	<td width="100">Userkey</td>
            <td><input type="text" id="userkey" name="userkey" value="<?php echo $username;?>"></td>
        </tr>
        <tr>
        	<td>Passkey</td>
            <td><input type="password" id="passkey" name="passkey" value="<?php echo $password;?>"></td>
        </tr>
        
        <tr>
        	<td>http API</td>
            <td>
            	<input type="text" id="url" name="url" size="80px" value="<?php echo $url;?>">
            </td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
            <td><span class="submit"><input type="submit" class="button-primary" value="Save Changes" /></span></td>
        </tr>
	</table>
    </form>
    <br />
    <div style="padding: 5px; font-size:11px"><i>http API (Zenziva Reguler / Free Trial): <b>http://zenziva.com/apps/smsapi.php</b> </i></div>
    <div style="padding: 5px; font-size:11px"><i>Userkey dan Passkey bisa didapatkan pada halaman member area SETTING > API SETTING.</i><br /></div>
    <div style="padding: 5px; font-size:11px"><i>Plugin Wordpress ZenzivaSMS menggunakan account dari Zenziva. Jika belum mempunyai account, silahkan <a href="http://www.zenziva.com/harga/" target="_blank">daftar</a> terlebih dahulu.</i><br /></div>
<?php }

// Post alert settings and store data
function blog_settings(){
  //declaring variables
  global $user_ID;
  $send_blog_alerts = '';
  $send_alert = get_usermeta($user_ID, 'sendBlogAlerts');
  $roles_list = "";

  // to add meta data to the table
  if($send_alert == ""){
      if(add_user_meta($user_ID, "sendBlogAlerts", 0,true)){
          if(add_user_meta($user_ID, "roles_send_list", " ", true)){
                $send_blog_alerts = '0';
          }
      }
  }
  //if data is in the table
  else{
      if($send_alert == 1){
          $roles_list = get_usermeta($user_ID, 'roles_send_list');
          $send_blog_alerts = '1';
      }
      if($send_alert == 0){
          $send_blog_alerts = '0';
      }
  }

  //Changes are made
  if(isset($_POST['blog_alert']) ){
      $roles_list="";
      $send_blog_alerts = $_POST['blog_alert'];

      foreach($_POST as $i=>$value){
          if($i == "blog_alert")
              update_usermeta($user_ID, 'sendBlogAlerts', $_POST['blog_alert']);
          else {
              $roles_list .= $i." ";
          }
     }
     update_usermeta($user_ID, 'roles_send_list', $roles_list);
     echo '<div class="updated"><p><strong>';
		 echo __( 'Alert setting disimpan !' );
		 echo '</strong></p></div>';
  }

  ?>
 <!--<form name="blog_settings" id="blog_settings" method="POST" action="<?= 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] ?>">-->
 <form action="" name="blog_settings" id="blog_settings" method="POST" >
     <table width="100%" cellpadding="5" cellspacing="5" border="0">
        <tr>
        	<td width="200">Kirim Pemberitahuan Posting Baru</td>
            <td>
            	<select name="blog_alert" id="blog_alert">
                    <option value="1" name="op1" <?php if($send_blog_alerts=='1') echo ' selected="selected"'; ?>>Yes</option>
                    <option value="0" name="op2" <?php if($send_blog_alerts=='0') echo ' selected="selected"'; ?>>No</option>
                 </select>
            </td>
        </tr>
        <tr>
        	<td valign="top"><br />Pilih Grup</td>
            <td>
                <br />
            	<label><input id="box1" type="checkbox" name="all"  <?php if((strlen(strstr($roles_list, "all")) > 0)) echo 'checked'; ?><?php if($send_blog_alerts=='0') echo 'disabled'; ?> > All Users </label>
				<br /><br /> <b>or</b>
                <br /><br /> <label><input class="box2" type="checkbox" name="administrator" value ="administrator" <?php if((strlen(strstr($roles_list, "administrator")) > 0)) echo 'checked'; ?> <?php if($send_blog_alerts=='0') echo 'disabled'; ?> > Admins</label>&nbsp;&nbsp;&nbsp;<label><input class="box2" type="checkbox" name="editor" value="editor" <?php if((strlen(strstr($roles_list, "editor")) > 0)) echo 'checked'; ?> <?php if($send_blog_alerts=='0') echo 'disabled'; ?> > Editors</label>&nbsp;&nbsp;&nbsp;<label><input class="box2" type="checkbox" name="author" value ="author" <?php if((strlen(strstr($roles_list, "author")) > 0)) echo 'checked'; ?> <?php if($send_blog_alerts=='0') echo 'disabled'; ?> > Authors</label>&nbsp;&nbsp;&nbsp;<label><input class="box2" type="checkbox" name="contributor" value ="contributor" <?php if((strlen(strstr($roles_list, "contributor")) > 0)) echo 'checked'; ?> <?php if($send_blog_alerts=='0') echo 'disabled'; ?> > Contributors</label>&nbsp;&nbsp;&nbsp;<label><input class="box2" type="checkbox" name="subscriber" value ="subscriber" <?php if((strlen(strstr($roles_list, "subscriber")) > 0)) echo 'checked'; ?> <?php if($send_blog_alerts=='0') echo 'disabled'; ?> > Subscriber</label>
            </td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
            <td><br/><span class="submit"><input type="submit" class="button-primary" value="Save Changes" /></span></td>
        </tr>
	</table>
    </form>
    <br />
    <div style="padding: 5px; font-size:11px"><i>Jika anda mengaktifkan fungsi ini, anda dapat mengirim SMS pemberitahuan kepada user mengenai Postingan terbaru anda. Kirim SMS pemberitahuan dengan cara masuk ke menu <strong>Post</strong> > <strong>All Post</strong> > klik <strong>Send SMS</strong> pada field tabel <strong>Send Post Alert</strong>.</i><br /></div>
<?php }

// Add column name Send Post Alert
function add_send_column($columns) {
    $columns['send_p_alert'] = 'Send Post Alert';
    return $columns;
}

//Add "Send SMS" content and Send Messages
function add_send_column_content($name) {
    global $post;
    global $user_ID;
    $phoneNumbers = "";


    if(isset($_REQUEST['id']) && $_REQUEST['id'] != ""){
        $temp_post =  get_post($_REQUEST['id']);

        unset($_REQUEST['id']);

        $title = $temp_post->post_title;
        $link = $temp_post->guid;

        $roles_list = get_usermeta($user_ID, 'roles_send_list');
        $roles = explode(" ", $roles_list);
        $count = 0;
       // print_r ($roles);
        foreach($roles as $role){
            $users_list = getAllUsersWithRole($role);
            $tempNumbers = getPhoneNumbers($users_list);
            if($tempNumbers != ""){
                if($count != 0){
                    $phoneNumbers.= ", ".$tempNumbers;
                }
                else{
                    $phoneNumbers.= $tempNumbers;
                    $count = $count + 1;
                }
            }
        }
        
        //$message = "New Post: <a href='".$link."'>\"".$title. "\"</a>";
        $message = "Posting Baru: \"".$title. "\" link: $link";
        $r_message = send_SMS($phoneNumbers, $message);
        $phoneNumbers = "";
        echo '<div class="updated"><p><strong>';
				echo __( 'Pesan dikirim !' );
				echo '</strong></p></div>';
    }

    switch ($name) {
        case 'send_p_alert':
            $click_sms = "<a href='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']. "?"."id=".$post->ID."' id='click_sms' title='Kirim SMS Pemberitahuan Posting Baru'>Send SMS</a>";
            echo $click_sms;
    }
}

include('plugin.php');
?>