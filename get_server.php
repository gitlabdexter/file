<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
require_once '../../includes/functions.php';
chkSession();
$values = array();
if($user_id_2 == 1 || $user_level_2 == 'superadmin' || $user_level_2 == 'developer'){
}else{
	echo "<script> swal({ title: 'Error!' , text: 'Sorry, you dont have permission to access this page.', button: false, closeOnClickOutside: false, icon: 'error' })  </script>";
	$db->RedirectToURL($db->base_url());
	exit;
}

if(!isset($_GET['server_ip']) || empty($_GET['server_ip'])){
	echo '<script>alert("Error");</script>';
	$db->RedirectToURL($db->base_url());
	exit;	
}else{
	$serverip = strip_tags(trim($_GET['server_ip']));
	
	$valid = true;
    
    if($valid){
        
        $query = $db->sql_query("SELECT * FROM server_list WHERE server_ip='$serverip'");
        $row = $db->sql_fetchrow($query);
     		
     		if($row['proto'] == 'aio'){
     		    $proto = 'openvpn';
     		    $online_ssh = strtoupper($row['ssh_online']);
     		    
     		    //XRAY STATUS
         		$xray = '<span class="form-control" tabindex="1" readonly><b>XRAY VLESS : '.strtoupper($row['xray_port']) .' - </b>';
         		if($row['xray_status'] == 'active'){
         		    $xray_status = '<b class="text-success">'. strtoupper($row['xray_status']).'</b></span>';
         		}else{
         		    $xray_status = '<b class="text-danger">'. strtoupper($row['xray_status']).'</b></span>';
         		}
         		
     		    //SSH STATUS
         		$ssh = '<span class="form-control" tabindex="1" readonly><b>SSH : '.strtoupper($row['ssh_port']) .' - </b>';
         		if($row['ssh_status'] == 'active'){
         		    $ssh_status = '<b class="text-success">'. strtoupper($row['ssh_status']).'</b></span>';
         		}else{
         		    $ssh_status = '<b class="text-danger">'. strtoupper($row['ssh_status']).'</b></span>';
         		}
     		    
     		    //SLOWDNS STATUS
         		$slowdns = '<span class="form-control" tabindex="1" readonly><b>SLOWDNS : '.strtoupper($row['slowdns_port']) .' - </b>';
         		if($row['slowdns_status'] == 'active'){
         		    $slowdns_status = '<b class="text-success">'. strtoupper($row['slowdns_status']).'</b></span>';
         		}else{
         		    $slowdns_status = '<b class="text-danger">'. strtoupper($row['slowdns_status']).'</b></span>';
         		}
         		
         		//DROPBEAR STATUS
         		$dropbear = '<span class="form-control" tabindex="1" readonly><b>DROPBEAR : '.strtoupper($row['dropbear_port']) .' - </b>';
         		if($row['dropbear_status'] == 'active'){
         		    $dropbear_status = '<b class="text-success">'. strtoupper($row['dropbear_status']).'</b></span>';
         		}else{
         		    $dropbear_status = '<b class="text-danger">'. strtoupper($row['dropbear_status']).'</b></span>';
         		}
         		
     		    $info = '<a href="http://'.$serverip.':5623/server.txt" class="btn btn-primary btn-block" target=blank_>SERVER INFO</a>';
     		}elseif($row['proto'] == 'openvpn'){
     		    $proto = 'openvpn';
     		    $online_ssh = 'NOT APPLICABLE';
     		    $xray_status = '';
     		    $slowdns_status = '';
     		    $ssh_status = '';
     		    $dropbear_status = '';
     		    $info = '';
     		}elseif($row['proto'] == 'openssh'){
     		    $proto = 'openssh';
     		    $online_ssh = strtoupper($row['ssh_online']);
     		    $xray_status = '';
     		    $slowdns_status = '';
     		    $ssh_status = '';
     		    $dropbear_status = '';
     		    $info = '';
     		}elseif($row['proto'] == 'openconnect'){
     		    $proto = 'openconnect';
     		    $online_ssh = '';
     		    $xray_status = '';
     		    $slowdns_status = '';
     		    $ssh_status = '';
     		    $dropbear_status = '';
     		    $info = '';
     		}elseif($row['proto'] == 'xray'){
     		    
     		    //XRAY TLS STATUS
         		$xraytls = '<span class="form-control" tabindex="1" readonly><b>XRAY TLS : '.strtoupper($row['xray_tls']) .' - </b>';
         		if($row['xray_status'] == 'active'){
         		    $xray_tls = '<b class="text-success">'. strtoupper($row['xray_status']).'</b></span>';
         		}else{
         		    $xray_tls = '<b class="text-danger">'. strtoupper($row['xray_status']).'</b></span>';
         		}
         		
         		//XRAY NTLS STATUS
         		$xrayntls = '<span class="form-control" tabindex="1" readonly><b>XRAY NON-TLS : '.strtoupper($row['xray_ntls']) .' - </b>';
         		if($row['xray_status'] == 'active'){
         		    $xray_ntls = '<b class="text-success">'. strtoupper($row['xray_status']).'</b></span>';
         		}else{
         		    $xray_ntls = '<b class="text-danger">'. strtoupper($row['xray_status']).'</b></span>';
         		}
         		
         		$vmess_link = '<a href="https://'.$serverip.':81/'.$row['xray_vmess'].'.txt" class="btn btn-primary btn-block" target=blank_>VMESS DETAILS</a>';
         		$vless_link = '<a href="https://'.$serverip.':81/'.$row['xray_vless'].'.txt" class="btn btn-primary btn-block" target=blank_>VLESS DETAILS</a>';
         		$trojan_link = '<a href="https://'.$serverip.':81/'.$row['xray_trojan'].'.txt" class="btn btn-primary btn-block" target=blank_>TROJAN DETAILS</a>';
         		$ss_link = '<a href="https://'.$serverip.':81/'.$row['xray_ss'].'.txt" class="btn btn-primary btn-block" target=blank_>SHADOWSOCKS DETAILS</a>';
         		
     		    $proto = 'xray';
     		    $online_ssh = '';
     		    $xray_status = '';
     		    $slowdns_status = '';
     		    $ssh_status = '';
     		    $dropbear_status = '';
     		    $info = '';
     		}elseif($row['proto'] == 'hysteria'){
     		    $proto = 'hysteria';
     		    $online_ssh = '';
     		    $xray_status = '';
     		    $slowdns_status = '';
     		    $ssh_status = '';
     		    $dropbear_status = '';
     		    $info = '';
     		}elseif($row['proto'] == 'socksip'){
     		    $proto = 'socksip';
     		    $online_ssh = '';
     		    $xray_status = '';
     		    $slowdns_status = '';
     		    $ssh_status = '';
     		    $dropbear_status = '';
     		    $info = '';
     		}else{
     		    $proto = 'unknown';
     		    $online_ssh = '<span class="text-danger">NOT APPLICABLE</span>';
     		    $xray_status = '';
     		    $slowdns_status = '';
     		    $ssh_status = '';
     		    $dropbear_status = '';
     		    $info = '';
     		}
     		
     		$server_service = strtoupper('service : '.$proto.' protocol');
     		$server_ip = strtoupper('ip address : '.$row['server_ip'].'');
     		$connected_ovpn = strtoupper($proto.' users : '.$row['online'].'');
     		$connected_hysteria = strtoupper('hysteria users : '.$row['hysteria_online'].'');
     		$connected_ssh = strtoupper('ssh users : '.$online_ssh.'');
     		$bandwidth = strtoupper('bandwidth : '.$row['bandwidth'].'');
     		$os = strtoupper('os : '.$row['os'].'');
     		$distro = strtoupper('distro : '.$row['distro'].'');
     		$cpu_model = strtoupper('cpu model : '.$row['cpu_model'].'');
     		$memory = strtoupper('memory : '.$row['memory'].'');
     		$disk = strtoupper('disk : '.$row['disk'].'');
     		$uptime = strtoupper('uptime : '.$row['uptime'].'');
     		
     		
     		//HYSTERIA STATUS
     		$hysteria_udp = '<span class="form-control" tabindex="1" readonly><b>HYSTERIA UDP : '.strtoupper($row['hysteria_port']) .' - </b>';
     		if($row['hysteria_status'] == 'active'){
     		    $hysteria_status = '<b class="text-success">'. strtoupper($row['hysteria_status']).'</b></span>';
     		}else{
     		    $hysteria_status = '<b class="text-danger">'. strtoupper($row['hysteria_status']).'</b></span>';
     		}
     		
     		//OVPN TCP STATUS
     		if($row['proto'] == 'openvpn'){
     		    $ovpntcp = '<span class="form-control" tabindex="1" readonly><b>OVPN TCP : '.strtoupper($row['tcp']) .' - </b>';
     		}elseif($row['proto'] == 'openconnect'){
     		    $ovpntcp = '<span class="form-control" tabindex="1" readonly><b>OCSERV TCP : '.strtoupper($row['tcp']) .' - </b>';
     		}elseif($row['proto'] == 'aio'){
     		    $ovpntcp = '<span class="form-control" tabindex="1" readonly><b>OVPN TCP : '.strtoupper($row['tcp']) .' - </b>';
     		}
     		if($row['tcp_status'] == 'active'){
     		    $ovpntcp_status = '<b class="text-success">'. strtoupper($row['tcp_status']).'</b></span>';
     		}else{
     		    $ovpntcp_status = '<b class="text-danger">'. strtoupper($row['tcp_status']).'</b></span>';
     		}
     		
     		//OVPN UDP STATUS
     		if($row['proto'] == 'openvpn'){
     		    $ovpnudp = '<span class="form-control" tabindex="1" readonly><b>OVPN UDP : '.strtoupper($row['udp']) .' - </b>';
     		}elseif($row['proto'] == 'openconnect'){
     		    $ovpnudp = '<span class="form-control" tabindex="1" readonly><b>OCSERV UDP : '.strtoupper($row['tcp']) .' - </b>';
     		}elseif($row['proto'] == 'aio'){
     		    $ovpnudp = '<span class="form-control" tabindex="1" readonly><b>OVPN UDP : '.strtoupper($row['udp']) .' - </b>';
     		}
     		if($row['udp_status'] == 'active'){
     		    $ovpnudp_status = '<b class="text-success">'. strtoupper($row['udp_status']).'</b></span>';
     		}else{
     		    $ovpnudp_status = '<b class="text-danger">'. strtoupper($row['udp_status']).'</b></span>';
     		}
     		
     		//OVPN TCP SSL STATUS
     		if($row['proto'] == 'openvpn'){
     		    $ovpntcpssl = '<span class="form-control" tabindex="1" readonly><b>OVPN TCP SSL : '.strtoupper($row['tcpssl']) .' - </b>';
     		}elseif($row['proto'] == 'openconnect'){
     		    $ovpntcpssl = '<span class="form-control" tabindex="1" readonly><b>OCSERV TCP SSL : '.strtoupper($row['tcpssl']) .' - </b>';
     		}elseif($row['proto'] == 'aio'){
     		    $ovpntcpssl = '<span class="form-control" tabindex="1" readonly><b>OVPN TCP SSL : '.strtoupper($row['tcpssl']) .' - </b>';
     		}
     		if($row['ssl_status'] == 'active'){
     		    $ovpntcpssl_status = '<b class="text-success">'. strtoupper($row['ssl_status']).'</b></span>';
     		}else{
     		    $ovpntcpssl_status = '<b class="text-danger">'. strtoupper($row['ssl_status']).'</b></span>';
     		}
     		
     		//OVPN UDP SSL STATUS
     		if($row['proto'] == 'openvpn'){
     		    $ovpnudpssl = '<span class="form-control" tabindex="1" readonly><b>OVPN UDP SSL : '.strtoupper($row['udpssl']) .' - </b>';
     		}elseif($row['proto'] == 'openconnect'){
     		    $ovpnudpssl = '<span class="form-control" tabindex="1" readonly><b>OCSERV UDP SSL : '.strtoupper($row['tcpssl']) .' - </b>';
     		}elseif($row['proto'] == 'aio'){
     		    $ovpnudpssl = '<span class="form-control" tabindex="1" readonly><b>OVPN UDP SSL : '.strtoupper($row['udpssl']) .' - </b>';
     		}
     		if($row['ssl_status'] == 'active'){
     		    $ovpnudpssl_status = '<b class="text-success">'. strtoupper($row['ssl_status']).'</b></span>';
     		}else{
     		    $ovpnudpssl_status = '<b class="text-danger">'. strtoupper($row['ssl_status']).'</b></span>';
     		}
            
            //SQUID PROXY STATUS
            $squid = '<span class="form-control" tabindex="1" readonly><b>SQUID PROXY : '.strtoupper($row['squid']) .' - </b>';
            if($row['squid_status'] == 'active'){
                $squid_status = '<b class="text-success">'. strtoupper($row['squid_status']).'</b></span>';
            }else{
                $squid_status = '<b class="text-danger">'. strtoupper($row['squid_status']).'</b></span>';
            }
            
            //SOCKET PROXY STATUS
            $socks = '<span class="form-control" tabindex="1" readonly><b>SOCKET PROXY : '.strtoupper($row['socket']) .' - </b>';
            if($row['socket_status'] == 'active'){
                $socket_status = '<b class="text-success">'. strtoupper($row['socket_status']).'</b></span>';
            }else{
                $socket_status = '<b class="text-danger">'. strtoupper($row['socket_status']).'</b></span>';
            }
            
            //SOCKSIP STATUS
            $socksip = '<span class="form-control" tabindex="1" readonly><b>SOCKSIP : '.strtoupper($row['socksip_port']) .' - </b>';
            if($row['socksip_status'] == 'active'){
                $socksip_status = '<b class="text-success">'. strtoupper($row['socksip_status']).'</b></span>';
            }else{
                $socksip_status = '<b class="text-danger">'. strtoupper($row['socksip_status']).'</b></span>';
            }
     		
     		$values['proto'] = '<div class="form-group"><span class="form-control" tabindex="1" readonly><b>'.$server_service.'</b></span></div>';
     		$values['ipaddress'] = '<div class="form-group"><span class="form-control" tabindex="1"  readonly><b>'.$server_ip.'</b></span></div>';
     		
     		if($row['proto'] == 'hysteria' || $row['proto'] == 'socksip'){
     		    $values['total_connected'] = '';
     		}else{
     		    $values['total_connected'] = '<div class="form-group"><span class="form-control" tabindex="1" readonly><b>'.$connected_ovpn.'</b></span></div>';
     		}
     		
     		if($row['proto'] == 'xray' || $row['proto'] == 'socksip'){
     		    $values['total_hysteria'] = '';
     		}else{
     		    $values['total_hysteria'] = '<div class="form-group"><span class="form-control" tabindex="1" readonly><b>'.$connected_hysteria.'</b></div>';
     		}
     		
     		if($row['proto'] == 'openvpn' || $row['proto'] == 'openconnect' || $row['proto'] == 'unknown'){
     		    $values['total_ssh'] = '';
     		    $values['ssh_status'] = '';
     		    $values['dropbear_status'] = '';
     		    $values['slowdns_status'] = '';
     		    $values['xray_status'] = '';
     		    $values['xray_tls'] = '';
     		    $values['xray_ntls'] = '';
     		    $values['svrinfo'] = '';
     		    $values['vmess_link'] = '';
     		    $values['vless_link'] = '';
     		    $values['trojan_link'] = '';
     		    $values['ss_link'] = '';
     		}elseif($row['proto'] == 'aio'){
     		    $values['total_ssh'] = '<div class="form-group"><span class="form-control" tabindex="1" readonly><b>'.$connected_ssh.'</b></span></div>';
     		    $values['ssh_status'] = '<div class="form-group">'.$ssh.$ssh_status.'</div>';
     		    $values['dropbear_status'] = '<div class="form-group">'.$dropbear.$dropbear_status.'</div>';
     		    $values['slowdns_status'] = '<div class="form-group">'.$slowdns.$slowdns_status.'</div>';
     		    $values['xray_status'] = '<div class="form-group">'.$xray.$xray_status.'</div>';
     		    $values['xray_tls'] = '';
     		    $values['xray_ntls'] = '';
     		    $values['svrinfo'] = $info;
     		    $values['vmess_link'] = '';
     		    $values['vless_link'] = '';
     		    $values['trojan_link'] = '';
     		    $values['ss_link'] = '';
     		}elseif($row['proto'] == 'xray'){
     		    $values['total_ssh'] = '';
     		    $values['ssh_status'] = '';
     		    $values['dropbear_status'] = '';
     		    $values['slowdns_status'] = '';
     		    $values['xray_status'] = '';
     		    $values['xray_tls'] = '<div class="form-group">'.$xraytls.$xray_tls.'</div>';
     		    $values['xray_ntls'] = '<div class="form-group">'.$xrayntls.$xray_ntls.'</div>';
     		    
     		    $values['svrinfo'] = '';
     		    $values['vmess_link'] = $vmess_link;
     		    $values['vless_link'] = $vless_link;
     		    $values['trojan_link'] = $trojan_link;
     		    $values['ss_link'] = $ss_link;
     		}elseif($row['proto'] == 'hysteria'){
     		    $values['total_ssh'] = '';
     		    $values['ssh_status'] = '';
     		    $values['dropbear_status'] = '';
     		    $values['slowdns_status'] = '';
     		    $values['xray_status'] = '';
     		    $values['xray_tls'] = '';
     		    $values['xray_ntls'] = '';
     		    
     		    $values['svrinfo'] = '';
     		    $values['vmess_link'] = '';
     		    $values['vless_link'] = '';
     		    $values['trojan_link'] = '';
     		    $values['ss_link'] = '';
     		}elseif($row['proto'] == 'socksip'){
     		    $values['total_ssh'] = '';
     		    $values['ssh_status'] = '';
     		    $values['dropbear_status'] = '';
     		    $values['slowdns_status'] = '';
     		    $values['xray_status'] = '';
     		    $values['xray_tls'] = '';
     		    $values['xray_ntls'] = '';
     		    
     		    $values['svrinfo'] = '';
     		    $values['vmess_link'] = '';
     		    $values['vless_link'] = '';
     		    $values['trojan_link'] = '';
     		    $values['ss_link'] = '';
     		}
     		
     		$values['bandwidth'] = '<div class="form-group"><span class="form-control" tabindex="1" readonly><b>'.$bandwidth.'</b></span></div>';
     		$values['os'] = '<div class="form-group"><span class="form-control" tabindex="1" readonly><b>'.$os.'</b></span></div>';
     		$values['distro'] = '<div class="form-group"><span class="form-control" tabindex="1" readonly><b>'.$distro.'</b></span></div>';
     		$values['cpu_model'] = '<div class="form-group"><span class="form-control" tabindex="1" readonly><b>'.$cpu_model.'</b></span></div>';
     		$values['memory'] = '<div class="form-group"><span class="form-control" tabindex="1" readonly><b>'.$memory.'</b></span></div>';
            $values['disk'] = '<div class="form-group"><span class="form-control" tabindex="1" readonly><b>'.$disk.'</b></span></div>';
            $values['uptime'] = '<div class="form-group"><span class="form-control" tabindex="1" readonly><b>'.$uptime.'</b></span></div>';
            
            if($row['proto'] == 'xray'){
                $values['tcp_status'] = '';
                $values['udp_status'] = '';
                $values['tcpssl'] = '';
                $values['udpssl'] = '';
                $values['httpstatus'] = '';
                $values['hysteria_status'] = '';
                $values['socksip_status'] = '';
                $values['squid3'] = '<div class="form-group">'.$squid.$squid_status.'</div>';
            }elseif($row['proto'] == 'socksip'){
                $values['tcp_status'] = '';
                $values['udp_status'] = '';
                $values['tcpssl'] = '';
                $values['udpssl'] = '';
                $values['httpstatus'] = '';
                $values['hysteria_status'] = '';
                $values['socksip_status'] = '<div class="form-group">'.$socksip.$socksip_status.'</div>';
                $values['squid3'] = '';
            }else{
                $values['tcp_status'] = '<div class="form-group">'.$ovpntcp.$ovpntcp_status.'</div>';
                $values['udp_status'] = '<div class="form-group">'.$ovpnudp.$ovpnudp_status.'</div>';
                $values['tcpssl'] = '<div class="form-group">'.$ovpntcpssl.$ovpntcpssl_status.'</div>';
                $values['udpssl'] = '<div class="form-group">'.$ovpnudpssl.$ovpnudpssl_status.'</div>';
                
                if($row['proto'] == 'hysteria'){
                    $values['httpstatus'] = '';
                }else{
                    $values['httpstatus'] = '<div class="form-group">'.$socks.$socket_status.'</div>';
                }
                $values['socksip_status'] = '';
                $values['hysteria_status'] = '<div class="form-group">'.$hysteria_udp.$hysteria_status.'</div>';
                $values['squid3'] = '<div class="form-group">'.$squid.$squid_status.'</div>';
            }
            
            $values['response'] = 1;
        
    }
    
    echo json_encode($values);
}

?>