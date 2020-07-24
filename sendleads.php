<?php        
            error_reporting(0);
            function CheckDirLog()
            {
                if (file_exists(dirname( __FILE__ ).'/_logs')) {
                    return true;
                } else {	
                    return mkdir(dirname( __FILE__ ).'/_logs', 0777);	
                }	
            }
            function CheckHtaccess()
            {
                if (file_exists(dirname( __FILE__ ).'/_logs/.htaccess')) {
                    return true;
                } else {		
                    $f = fopen(dirname( __FILE__ ).'/_logs/.htaccess', 'w');
                    if (!$f) {
                        return false;
                    }
                    fwrite($f, "Order Deny,Allow\r\n");
                    fwrite($f, "Deny from all\r\n");
                    fclose($f);
                    return true;	
                }
            }
            function CheckIndex()
            {
                if (file_exists(dirname( __FILE__ ).'/_logs/index.html')) {
                    return true;
                } else {
                    $f = fopen(dirname( __FILE__ ).'/_logs/index.html', 'w');
                    if (!$f) {
                        return false;
                    }
                    fwrite($f, '<html><head><title>404</title></head><body><h1>Error 404</h1></body></html>');
                    fclose($f);
                    return true;		
                }
            }
            function ToLog($data)
            {
                if (CheckDirLog() && CheckHtaccess() && CheckIndex()) {
                    $fName = dirname( __FILE__ ).'/_logs/' . date('Y-m-d') . '.txt';		
                    $f = fopen($fName, "a");		
                    if (!$f) {
                        return false;
                    }
                    $data = date('Y-m-d H:i:s') . " " . $data . "\r\n";			
                    fwrite($f, $data);
                    fclose($f);
                    return true;
                } else {
                    return false;
                }	
            }        
            $data = [
                'tel' => $_REQUEST['tel'],
                'client' => $_REQUEST['client'],
                'ip' => $_SERVER['REMOTE_ADDR']
            ];           
            if(isset($_REQUEST["comments"]) && trim($_REQUEST["comments"]) != '') {
                $data['comments'] = $_REQUEST["comments"];
            }            
            $adr1 = ""; $adr2=""; $adr3 = "";
            if(isset($_REQUEST["adress"]) && trim($_REQUEST["adress"]) != '') {
                $adr1 = trim($_REQUEST["adress"]);
            }
            if(isset($_REQUEST["adres"]) && trim($_REQUEST["adres"]) != '') {
                $adr2 = trim($_REQUEST["adres"]);
            }
            if(isset($_REQUEST["address"]) && trim($_REQUEST["address"]) != '') {
                $adr3 = trim($_REQUEST["address"]);
            }
            $adress = trim($adr1." ".$adr2." ".$adr3);
            if(trim($adress) != '') {
                $data['adress'] = $adress;
            }                        
            $subid = '';
            for ($i=1; $i<=5; $i++) {
                if(isset($_REQUEST["subid$i"]) && trim($_REQUEST["subid$i"]) != '') {
                    $subid .= $_REQUEST["subid$i"].':';
                }
            }
            $subid = rtrim($subid, ':');
            if ($subid != '') {
                $data['subid'] = $subid;
            }
            $logInfo = "\r\n";
			$logInfo .= "Request:\r\n";
			$logInfo .= print_r($data, true)."\r\n";            
            $ch = curl_init();
            $url = 'https://api.monsterleads.pro/method/order.add?api_key=4b3683203161bf49044e40502e07e868&format=json&code=bj87h3w';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $res= curl_exec($ch);
            curl_close($ch);
            $answer = json_decode($res, true);
            $logInfo .= "Answer:\r\n";
			$logInfo .= print_r($answer, true)."\r\n";
			ToLog($logInfo);            
            if($answer['status'] == 'ok'){
            Header('Location: success.html'); die();
            }else{
                print_r ($answer);
               die();
            }