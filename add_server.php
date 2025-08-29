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
    $servername = $db->Sanitize(trim($_POST['servername']));
    $serverip = $db->Sanitize(trim($_POST['serverip']));
    $serverpass = $db->Sanitize(trim($_POST['serverpass']));
    $serveruser = $db->Sanitize(trim($_POST['serveruser']));
    
    $serverport = $db->Sanitize(trim($_POST['serverport']));
    $servertype = $db->Sanitize(trim($_POST['servertype']));
    
    $servercustomtcp = $db->Sanitize(trim($_POST['servercustomtcp']));
    $servercustomudp = $db->Sanitize(trim($_POST['servercustomudp']));
    $servercustomssl = $db->Sanitize(trim($_POST['servercustomssl']));
    $servercustomvless = $db->Sanitize(trim($_POST['servercustomvless']));
    $serverobfs = $db->Sanitize(trim($_POST['serverobfs']));
    $serverauthstr = $db->Sanitize(trim($_POST['serverauthstr']));
    
    $hysteria_type = $db->Sanitize(trim($_POST['hysteriatype']));
    
    $sshConnect = ssh2_connect($serverip, $serverport);
    $sshAuth = ssh2_auth_password($sshConnect,$serveruser,$serverpass);
    
    $key = $db->encryptor('decrypt', $_POST['_key']);
	$get_key = $db->Sanitize($key);
    
    // Getting country code
    $query = @unserialize(file_get_contents('http://ip-api.com/php/'.$serverip));
    if($query && $query['status'] == 'success') {
        $countryCode = $query['countryCode'];
    }else{
        $countryCode = 'PH';
    }
    
    if($servertype == '1'){
        $service = 'ubuntu_ovpn';
    }elseif($servertype == '2'){
        $service = 'ubuntu_openconnect';
    }elseif($servertype == '4'){
        $service = 'ubuntu_ovpnws';
    }elseif($servertype == '5'){
        $service = 'ubuntu_aio';
    }elseif($servertype == '8'){
        $service = 'debian_ovpn';
    }elseif($servertype == '9'){
        $service = 'debian_openconnect';
    }elseif($servertype == '11'){
        $service = 'debian_ovpnws';
    }elseif($servertype == '13'){
        $service = 'debian_aio';
    }elseif($servertype == '12'){
        $service = 'debian_openconnectws';
    }elseif($servertype == '15'){
        $service = 'centos_ovpn';
    }elseif($servertype == '18'){
        $service = 'centos_ovpnws';
    }elseif($servertype == '31'){
        $service = 'debian_xray';
    }elseif($servertype == '41'){
        $service = 'ubuntu_hysteria';
    }elseif($servertype == '42'){
        $service = 'debian_hysteria';
    }elseif($servertype == '43'){
        $service = 'centos_hysteria';
    }elseif($servertype == '51'){
        $service = 'ubuntu_socksip';
    }elseif($servertype == '62'){
        $service = 'debian_hysteria_free';
    }else{
        $service = 'na';
    }
    
    if($hysteria_type == '1'){ 
        $hysteriatype = 'default';
    }elseif($hysteria_type == '2'){
        $hysteriatype = 'zi';
    }else{
        $hysteriatype = 'default';
    }
    
    if(isset($_POST['submitted'])  == 'add_server'){
        $valid = true;
        
        
        
///////////////////////////////////////////////////////////        
        
        
        if($servertype == '1' || $servertype == '2' || $servertype == '4' || $servertype == '5' || $servertype == '8' || $servertype == '9' || $servertype == '11' || $servertype == '13' || $servertype == '12' || $servertype == '15' || $servertype == '18'){
            if(empty($servername)){
            	$errormsg[] = '<li>Enter server name.</li>';
                $valid = false;
            }
            
            if(empty($serverip)){
            	$errormsg[] = '<li>Enter server ip.</li>';
                $valid = false;
            }
            
            if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $serverip)){
                $errormsg[] = '<li>Invalid server ip.</li>';
                $valid = false;
            }
            
            if(!$sshConnect){
                $errormsg[] = '<li>Maybe your server is down or Wrong server information!</li>';
                $valid = false;
            }
            
            if(!$sshAuth){
                $errormsg[] = '<li>Server authentication failed!</li>';
                $valid = false;
            }
            
            if(empty($serverpass)){
            	$errormsg[] = '<li>Enter server pass.</li>';
                $valid = false;
            }
            
            if(empty($serveruser)){
            	$errormsg[] = '<li>Enter server user.</li>';
                $valid = false;
            }
            
            if(empty($serverport)){
            	$errormsg[] = '<li>Enter server port.</li>';
                $valid = false;
            }
            
            if(empty($servertype)){
            	$errormsg[] = '<li>Enter server type.</li>';
                $valid = false;
            }
            
            //Custom Ports Here
            if(empty($servercustomtcp)){
            	$errormsg[] = '<li>Enter custom tcp port.</li>';
                $valid = false;
            }
            
            if(empty($servercustomudp)){
                $errormsg[] = '<li>Enter custom udp port.</li>';
                $valid = false;
            }
            
            if(empty($servercustomssl)){
                $errormsg[] = '<li>Enter custom ssl port.</li>';
                $valid = false;
            }
            
            if(empty($serverobfs)){
                $errormsg[] = '<li>Enter custom hysteria obfs.</li>';
                $valid = false;
            }
            
            $server_qry = $db->sql_query("SELECT server_ip FROM server_list WHERE server_ip='$serverip'");
        	$server_chk = $db->sql_numrows($server_qry);
        	if($server_chk > 0){
                $errormsg[] = '<li>Server IP is already in database record.</li>';
                $valid = false;
        	}
            
            if($servertype == '13' || $servertype == '5'){
                if(empty($servercustomvless)){
                    $errormsg[] = '<li>Enter custom vless port.</li>';
                    $valid = false;
                }
            }else{
                
            }
            
            if($servertype == '13' || $servertype == '5'){
                if($servercustomtcp == '8080' || $servercustomtcp == '442' || $servercustomtcp == '3128' || $servercustomtcp == '443' || $servercustomtcp == '53' || $servercustomtcp == '2222' || $servercustomtcp == '5666'){
                	$errormsg[] = '<li>This tcp port '.$servercustomtcp.' is not allowed.</li>';
                    $valid = false;
                }
                
                if($servercustomudp == '8080' || $servercustomudp == '442' || $servercustomudp == '3128' || $servercustomudp == '443' || $servercustomudp == '53' || $servercustomtcp == '2222' || $servercustomtcp == '5666'){
                	$errormsg[] = '<li>This udp port '.$servercustomudp.' is not allowed.</li>';
                    $valid = false;
                }
            }else{
                if($servercustomtcp == '8080' || $servercustomtcp == '3128' || $servercustomtcp == '443'){
                	$errormsg[] = '<li>This tcp port '.$servercustomtcp.' is not allowed.</li>';
                    $valid = false;
                }
                
                if($servercustomudp == '8080' || $servercustomudp == '3128' || $servercustomudp == '443'){
                	$errormsg[] = '<li>This udp port '.$servercustomudp.' is not allowed.</li>';
                    $valid = false;
                }
            }
            
            //Conflict TCP Ports Here
            
            if($servercustomtcp == $servercustomudp){
                    $errormsg[] = '<li>Cannot set same port number on tcp and udp.</li>';
                    $valid = false;
                }
            
            if($servercustomtcp == $servercustomssl){
                    $errormsg[] = '<li>Cannot set same port number on tcp and ssl.</li>';
                    $valid = false;
                }
            
            //Conflict UDP Ports Here
            
            if($servercustomudp == $servercustomssl){
                    $errormsg[] = '<li>Cannot set same port number on udp and ssl.</li>';
                    $valid = false;
                }
                
            //Conflict SSL Ports Here
            
            if($servertype == '13' || $servertype == '5'){
                
                if($servercustomtcp == $servercustomvless){
                        $errormsg[] = '<li>Cannot set same port number on tcp and vless.</li>';
                        $valid = false;
                    }
                
                if($servercustomudp == $servercustomvless){
                        $errormsg[] = '<li>Cannot set same port number on udp and vless.</li>';
                        $valid = false;
                    }
                    
                if($servercustomssl == $servercustomvless){
                        $errormsg[] = '<li>Cannot set same port number on ssl and vless.</li>';
                        $valid = false;
                    }
            }else{
                
            }
            
            //Ports max length
            
            if(strlen($servercustomtcp) > 4 || strlen($servercustomtcp) < 3){
                $errormsg[] = '<li>Servertcp port should be a 3-4 digit port.</li>';
                $valid = false;
            }
            
            if(strlen($servercustomudp) > 4 || strlen($servercustomudp) < 2){
                $errormsg[] = '<li>Serverudp port should be a 2-4 digit port.</li>';
                $valid = false;
            }
            
            if(strlen($servercustomssl) > 4 || strlen($servercustomssl) < 3){
                $errormsg[] = '<li>Serverssl port should be a 3-4 digit port.</li>';
                $valid = false;
            }
            
            if($servertype == '13' || $servertype == '5'){
                
                if(strlen($servercustomvless) > 4 || strlen($servercustomvless) < 3){
                    $errormsg[] = '<li>Servervless port should be a 3-4 digit port.</li>';
                    $valid = false;
                }
            }else{
                
            }
            
            if($servertype == 2 || $servertype == 9 || $servertype == 12){
            $serverudp = $servercustomtcp;
            $valid = true;
            }else{
            $serverudp = $servercustomudp;
            if($servercustomudp < 53){
                $errormsg[] = '<li>Port is not allowed lower than 53 udp.</li>';
                $valid = false;
            } 
            }
            
            if($service == 'debian_aio' || $service == 'ubuntu_aio'){                                        //if AIO
                $dns_query = $db->sql_query("SELECT dns_domain, dns_global, dns_zone, dns_email FROM site_options");
                $dns_row = $db->sql_fetchrow($dns_query);
                    
                $cf_domain = $dns_row['dns_domain'];
                $cloudflaredomain = $db->decrypt_key2($cf_domain);
                $dns_domain = $db->encryptor2('decrypt', $cloudflaredomain);
                	
                $cf_zone = $dns_row['dns_zone'];
                $cloudflarezone = $db->decrypt_key2($cf_zone);
                $dns_zone = $db->encryptor2('decrypt', $cloudflarezone);
                	
                $cf_global = $dns_row['dns_global'];
                $cloudflareglobal = $db->decrypt_key2($cf_global);
                $dns_global = $db->encryptor2('decrypt', $cloudflareglobal);
                	
                $cf_email = $dns_row['dns_email'];
                $cloudflareemail = $db->decrypt_key2($cf_email);
                $dns_email = $db->encryptor2('decrypt', $cloudflareemail);
                    
                //Generating A Record
                $dnshost = ran_prefix();
                $dns_type = 'A';
                $dns_ttl = 1;
                $dns_proxied = false;
                    
                $ch = curl_init();		
                $apiURL = json_encode(  array( "type"=> $dns_type, "name" => $dnshost, "content" => $serverip, "ttl" => $dns_ttl, "proxied" => $dns_proxied ));
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $apiURL );
                curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$dns_zone/dns_records/");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-Auth-Email: '.$dns_email.'',
                'X-Auth-Key: '.$dns_global.'',
                'Cache-Control: no-cache',
                // 'Content-Type: multipart/form-data; charset=utf-8',
                'Content-Type:application/json',
                'purge_everything: true'
                ));
                $content  = curl_exec($ch);
                curl_close($ch);
                /* PARSING RESPONSE */
                $response = json_decode($content,true);
                /* RETURN */
                    
                if($response['success'] == true){
                    $SUB_DOMAIN = $dnshost.'.'.$dns_domain;
                    
                    $SUB_DOMAIN_ID = $response['result']['id'];
                        
                    //Generating NS Record
                 	$dnshost1 = ran_prefix();
                    $dns_type1 = 'NS';
                    $dns_ttl1 = 1;
                        
                    $ch = curl_init();		
                    $apiURL = json_encode(  array( "type"=> $dns_type1, "name" => $dnshost1, "content" => $SUB_DOMAIN, "ttl" => $dns_ttl1 ));
                    curl_setopt( $ch, CURLOPT_POSTFIELDS, $apiURL );
                    curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$dns_zone/dns_records/");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'X-Auth-Email: '.$dns_email.'',
                    'X-Auth-Key: '.$dns_global.'',
                    'Cache-Control: no-cache',
                    // 'Content-Type: multipart/form-data; charset=utf-8',
                    'Content-Type:application/json',
                    'purge_everything: true'
                    ));
                    $content  = curl_exec($ch);
                    curl_close($ch);
                    /* PARSING RESPONSE */
                    $response = json_decode($content,true);
                    /* RETURN */
                        
                    if($response['success'] == true){
                        $NS_DOMAIN = $dnshost1.'.'.$dns_domain;
                        
                        $NS_DOMAIN_ID = $response['result']['id'];

                    }else{
                        $errormsg[] = '<li>Failed creating NS Record, Please re-install the server.</li>';
                        $valid = false;
                    }
                }else{
                    $errormsg[] = '<li>Failed creating A Record, Please re-install the server.</li>';
                    $valid = false;
                }
            }else{
                
            }	      
        }elseif($servertype == '31'){
            if(empty($servername)){
            	$errormsg[] = '<li>Enter server name.</li>';
                $valid = false;
            }
            
            if(empty($serverip)){
            	$errormsg[] = '<li>Enter server ip.</li>';
                $valid = false;
            }
            
            if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $serverip)){
                $errormsg[] = '<li>Invalid server ip.</li>';
                $valid = false;
            }
            
            if(!$sshConnect){
                $errormsg[] = '<li>Maybe your server is down or Wrong server information!</li>';
                $valid = false;
            }
            
            if(!$sshAuth){
                $errormsg[] = '<li>Server authentication failed!</li>';
                $valid = false;
            }
            
            if(empty($serverpass)){
            	$errormsg[] = '<li>Enter server pass.</li>';
                $valid = false;
            }
            
            if(empty($serveruser)){
            	$errormsg[] = '<li>Enter server user.</li>';
                $valid = false;
            }
            
            if(empty($serverport)){
            	$errormsg[] = '<li>Enter server port.</li>';
                $valid = false;
            }
            
            $server_qry = $db->sql_query("SELECT server_ip FROM server_list WHERE server_ip='$serverip'");
        	$server_chk = $db->sql_numrows($server_qry);
        	if($server_chk > 0){
                $errormsg[] = '<li>Server IP is already in database record.</li>';
                $valid = false;
        	}
        	
            $dns_query = $db->sql_query("SELECT dns_domain, dns_global, dns_zone, dns_email FROM site_options WHERE id='1'");
            $dns_row = $db->sql_fetchrow($dns_query);
                    
            $cf_domain = $dns_row['dns_domain'];
            $cloudflaredomain = $db->decrypt_key2($cf_domain);
            $dns_domain = $db->encryptor2('decrypt', $cloudflaredomain);
                	
            $cf_zone = $dns_row['dns_zone'];
            $cloudflarezone = $db->decrypt_key2($cf_zone);
            $dns_zone = $db->encryptor2('decrypt', $cloudflarezone);
                	
            $cf_global = $dns_row['dns_global'];
            $cloudflareglobal = $db->decrypt_key2($cf_global);
            $dns_global = $db->encryptor2('decrypt', $cloudflareglobal);
                	
            $cf_email = $dns_row['dns_email'];
            $cloudflareemail = $db->decrypt_key2($cf_email);
            $dns_email = $db->encryptor2('decrypt', $cloudflareemail);
                    
            //Generating A Record
            $dnshost = ran_prefix();
            $dns_type = 'A';
            $dns_ttl = 1;
            $dns_proxied = false;
                    
            $ch = curl_init();		
            $apiURL = json_encode(  array( "type"=> $dns_type, "name" => $dnshost, "content" => $serverip, "ttl" => $dns_ttl, "proxied" => $dns_proxied ));
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $apiURL );
            curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$dns_zone/dns_records/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Email: '.$dns_email.'',
            'X-Auth-Key: '.$dns_global.'',
            'Cache-Control: no-cache',
            // 'Content-Type: multipart/form-data; charset=utf-8',
            'Content-Type:application/json',
            'purge_everything: true'
            ));
            $content  = curl_exec($ch);
            curl_close($ch);
            /* PARSING RESPONSE */
            $response = json_decode($content,true);
            /* RETURN */
                    
            if($response['success'] == true){
                $SUB_DOMAIN = $dnshost.'.'.$dns_domain;
                
                $SUB_DOMAIN_ID = $response['result']['id'];
            }else{
                $errormsg[] = '<li>Failed creating A Record, Please re-install the server.</li>';
                $valid = false;
            }
        }elseif($servertype == '41' || $servertype == '42' || $servertype == '43'){
            if(empty($servername)){
            	$errormsg[] = '<li>Enter server name.</li>';
                $valid = false;
            }
            
            if(empty($serverip)){
            	$errormsg[] = '<li>Enter server ip.</li>';
                $valid = false;
            }
            
            if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $serverip)){
                $errormsg[] = '<li>Invalid server ip.</li>';
                $valid = false;
            }
            
            if(!$sshConnect){
                $errormsg[] = '<li>Maybe your server is down or Wrong server information!</li>';
                $valid = false;
            }
            
            if(!$sshAuth){
                $errormsg[] = '<li>Server authentication failed!</li>';
                $valid = false;
            }
            
            if(empty($serverpass)){
            	$errormsg[] = '<li>Enter server pass.</li>';
                $valid = false;
            }
            
            if(empty($serveruser)){
            	$errormsg[] = '<li>Enter server user.</li>';
                $valid = false;
            }
            
            if(empty($serverport)){
            	$errormsg[] = '<li>Enter server port.</li>';
                $valid = false;
            }
            
            if(empty($serverobfs)){
                $errormsg[] = '<li>Enter custom hysteria obfs.</li>';
                $valid = false;
            }
            
            $server_qry = $db->sql_query("SELECT server_ip FROM server_list WHERE server_ip='$serverip'");
        	$server_chk = $db->sql_numrows($server_qry);
        	if($server_chk > 0){
                $errormsg[] = '<li>Server IP is already in database record.</li>';
                $valid = false;
        	}
        	
        }elseif($servertype == '51'){
            if(empty($servername)){
            	$errormsg[] = '<li>Enter server name.</li>';
                $valid = false;
            }
            
            if(empty($serverip)){
            	$errormsg[] = '<li>Enter server ip.</li>';
                $valid = false;
            }
            
            if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $serverip)){
                $errormsg[] = '<li>Invalid server ip.</li>';
                $valid = false;
            }
            
            if(!$sshConnect){
                $errormsg[] = '<li>Maybe your server is down or Wrong server information!</li>';
                $valid = false;
            }
            
            if(!$sshAuth){
                $errormsg[] = '<li>Server authentication failed!</li>';
                $valid = false;
            }
            
            if(empty($serverpass)){
            	$errormsg[] = '<li>Enter server pass.</li>';
                $valid = false;
            }
            
            if(empty($serveruser)){
            	$errormsg[] = '<li>Enter server user.</li>';
                $valid = false;
            }
            
            if(empty($serverport)){
            	$errormsg[] = '<li>Enter server port.</li>';
                $valid = false;
            }
            
            $server_qry = $db->sql_query("SELECT server_ip FROM server_list WHERE server_ip='$serverip'");
        	$server_chk = $db->sql_numrows($server_qry);
        	if($server_chk > 0){
                $errormsg[] = '<li>Server IP is already in database record.</li>';
                $valid = false;
        	}
        	
        }elseif($servertype == '61' || $servertype == '62' || $servertype == '63'){
            if(empty($servername)){
            	$errormsg[] = '<li>Enter server name.</li>';
                $valid = false;
            }
            
            if(empty($serverip)){
            	$errormsg[] = '<li>Enter server ip.</li>';
                $valid = false;
            }
            
            if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $serverip)){
                $errormsg[] = '<li>Invalid server ip.</li>';
                $valid = false;
            }
            
            if(!$sshConnect){
                $errormsg[] = '<li>Maybe your server is down or Wrong server information!</li>';
                $valid = false;
            }
            
            if(!$sshAuth){
                $errormsg[] = '<li>Server authentication failed!</li>';
                $valid = false;
            }
            
            if(empty($serverpass)){
            	$errormsg[] = '<li>Enter server pass.</li>';
                $valid = false;
            }
            
            if(empty($serveruser)){
            	$errormsg[] = '<li>Enter server user.</li>';
                $valid = false;
            }
            
            if(empty($serverport)){
            	$errormsg[] = '<li>Enter server port.</li>';
                $valid = false;
            }
            
            if(empty($serverobfs)){
                $errormsg[] = '<li>Enter custom hysteria obfs.</li>';
                $valid = false;
            }
            
            if(empty($serverauthstr)){
                $errormsg[] = '<li>Enter custom hysteria authstr.</li>';
                $valid = false;
            }
            
            $server_qry = $db->sql_query("SELECT server_ip FROM server_list WHERE server_ip='$serverip'");
        	$server_chk = $db->sql_numrows($server_qry);
        	if($server_chk > 0){
                $errormsg[] = '<li>Server IP is already in database record.</li>';
                $valid = false;
        	}
        }
        
/////////////////////////////////////////////////////        
        
        
        
        
        if($valid){
            if($get_key == 'firenetdev'){
                
                $ch2 = curl_init($base_url."api/push.php?tcp=$servercustomtcp&udp=$serverudp&ssl=$servercustomssl&vless=$servercustomvless&obfs=$serverobfs&authstr=$serverauthstr&hysteria=$hysteriatype&subdomain=$SUB_DOMAIN&nsdomain=$NS_DOMAIN&service=$service&ip=$serverip");
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch2, CURLOPT_HEADER, 0);
                $create_script = curl_exec($ch2);
                curl_close($ch2);
                
                if($create_script){
                    
                    $token = '"Authorization: token '.$git_token.'"';
                    $link = "https://raw.githubusercontent.com/$git_username/$site_url/server_script/$service";
                    
                    $kill_root = ssh2_exec($sshConnect, "kill -9 $(pgrep bash)");
                    if($kill_root){
                        
                        if($servertype == '15' || $servertype == '18' || $servertype == '43'){
                            $stream = ssh2_exec($sshConnect, "rm -f /usr/local/etc/.system; yum update -y; yum install screen -y; yum install curl -y; yum install sudo -y; yum install wget -y; curl -L -v -H $token $link > /usr/local/etc/.system; chmod +x /usr/local/etc/.system; screen -AmdS installer bash /usr/local/etc/.system");
                        }else{
                            $stream = ssh2_exec($sshConnect, "rm -f /usr/local/etc/.system; apt update -y; apt install screen -y; apt install curl -y; apt install sudo -y; apt install wget -y; curl -L -v -H $token $link > /usr/local/etc/.system; chmod +x /usr/local/etc/.system; screen -AmdS installer bash /usr/local/etc/.system");
                        }
                        
                        if($stream){
                            $result = $db->sql_query("INSERT INTO server_list 
                                        (server_name, server_ip, server_user, server_pass, flag, a_id, ns_id, auth_str, port_tcp, port_udp, port_ssh, protocol, status)
                                        VALUES
                                        ('".$db->SanitizeForSQL($servername)."','".$db->SanitizeForSQL($serverip)."','".$db->SanitizeForSQL($serveruser)."',
                                        '".$db->SanitizeForSQL($serverpass)."','".$db->SanitizeForSQL($countryCode)."','".$db->SanitizeForSQL($SUB_DOMAIN_ID)."','".$db->SanitizeForSQL($NS_DOMAIN_ID)."','".$db->SanitizeForSQL($serverauthstr)."','".$db->SanitizeForSQL($servercustomtcp)."',
                                        '".$db->SanitizeForSQL($servercustomudp)."','".$db->SanitizeForSQL($serverport)."',
                                        '".$db->SanitizeForSQL($servertype)."',99)");
                            
                            if($result){
                                $success_message = '<li>Server <code>'.$servername.'</code> added and now installing!</li><li>Please wait atleast 5 minutes before adding new server again.</li>';
                                $values['response'] = 1;
                                $values['msg'] = $success_message;
                            }else{
                                $error_message = 'Server adding failed!';
                                $values['response'] = 2;
                                $values['msg'] = $error_message;
                            }
                        }else{
                            $error_message = 'Server installation failed, Please contact administrator!';
                            $values['response'] = 2;
                            $values['msg'] = $error_message;
                        }
                    }else{
                        $error_message = 'Server installation failed, Please contact administrator!!';
                        $values['response'] = 2;
                        $values['msg'] = $error_message;
                    }
                }else{
                    $error_message = 'Server adding failed 2!';
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