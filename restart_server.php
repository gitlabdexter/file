<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
$values = array();
if($user_id_2 == 1 || $user_level_2 == 'superadmin'){
}else{
	echo "<script> swal({ title: 'Error!' , text: 'Sorry, you dont have permission to access this page.', button: false, closeOnClickOutside: false, icon: 'error' })  </script>";
	$db->RedirectToURL($db->base_url());
	exit;
}
    $serverip = $db->Sanitize(trim($_POST['serverip']));
    
    $key = $db->encryptor('decrypt', $_POST['_key']);
	$get_key = $db->Sanitize($key);
    
    $sql = "SELECT * FROM server_list WHERE server_ip='$serverip'";
    $qry = $db->sql_query("$sql") OR die();
	$row = $db->sql_fetchrow($qry);
	
	$server_user = 'adminmtk';
	$server_pass = 'Mediatek272124@@';
	
	$connection = ssh2_connect($serverip, 22);
	$authenticate = ssh2_auth_password($connection,$server_user,$server_pass);
    
    $valid = true;
	
	if(!$connection){
        $errormsg[] = '<li>Maybe your server is down or Wrong server information!</li>';
        $valid = false;
    }
        
    if(!$authenticate){
        $errormsg[] = '<li>Server authentication failed!</li>';
        $valid = false;
    }
    
    if(isset($_POST['submitted'])  == 'server_restart'){
        
        if($valid){
            if($get_key == 'firenetdev'){
                
                $restartserv = ssh2_exec($connection, "{ echo $server_pass; } | sudo -S reboot");
                
                if($restartserv){
                    $restart = $db->sql_query("UPDATE server_list SET status='98' WHERE server_ip='$serverip'");
                    if($restart){
                        $success_message = 'Server '.$serverip.' is restarting.';
                        $values['response'] = 1;
                        $values['msg'] = $success_message;
                    }else{
                        $error_message = 'Restarting server failed!';
                        $values['response'] = 2;
                        $values['msg'] = $error_message;
                    }
                }else{
                    $error_message = 'Restarting server failed!';
                    $values['response'] = 2;
                    $values['msg'] = $error_message;
                }
            }else{
                $error_message = 'Site key invalid!';
                $values['response'] = 2;
                $values['msg'] = $error_message;
            }
        }else{
            $values['response'] = 3;
            $errors = implode('',$errormsg);
            $values['errormsg'] = $errors;
        }
    }
    echo json_encode($values);
?>