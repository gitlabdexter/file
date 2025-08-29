<?php
ob_start();
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
$values = array();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'developer' || $user_level_2 == 'reseller'){
}else{
	echo "<script> swal({ title: 'Error!' , text: 'Sorry, you dont have permission to access this page.', button: false, closeOnClickOutside: false, icon: 'error' })  </script>";
	$db->RedirectToURL($db->base_url());
	exit;
}
    $apiLink = $chk;
    $api_01 = $db->decrypt_key($apiLink);
	$linkapi = $db->encryptor('decrypt', $api_01);
	$dom = 'domain='.$siteURL;
	$lic = '&code='.$site_license;
    $apiURL = $linkapi.$dom.$lic;
    
    $url = $linkapi."domain=".$siteURL."&code=".$site_license;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $resp = curl_exec($curl);
    curl_close($curl);
                
    $apiData = json_decode($resp, true); 
    $statusCode = $apiData['status'];
    $MSG = $apiData['msg'];
    
    if($statusCode === 1){
		$values['response'] = 1;
		$values['licmsg'] = $MSG;
    }elseif($statusCode === 2){
        $values['response'] = 2;
        $values['licmsg'] = $MSG;
    }elseif($statusCode === 3){
        $values['response'] = 3;
        $values['licmsg'] = $MSG;
    }

    echo json_encode($values);
?>