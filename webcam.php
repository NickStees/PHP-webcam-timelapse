<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>

<?php 
echo'starting';

ini_set('max_execution_time', 60);
error_reporting(E_ALL);
ini_set("display_errors", 1);



/* gets content from a URL via curl */
function get_image() {
	
$curl_url = 'http://10.67.47.3/axis-cgi/jpg/image.cgi';
$curl_options = array( 
        CURLOPT_RETURNTRANSFER => true,         // return web page 
        CURLOPT_HEADER         => false,        // don't return headers 
        CURLOPT_FOLLOWLOCATION => true,         // follow redirects 
        //CURLOPT_ENCODING       => "",           // handle all encodings 
        //CURLOPT_USERAGENT      => "spider",     // who am i 
        CURLOPT_AUTOREFERER    => true,         // set referer on redirect 
        CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect 
        CURLOPT_TIMEOUT        => 120,          // timeout on response 
        CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects 
        //CURLOPT_POST            => 1,           // i am sending post data 
       // CURLOPT_POSTFIELDS     => $curl_post,   // this are my post vars 
        CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl 
        CURLOPT_SSL_VERIFYPEER => false,        // 
        //CURLOPT_VERBOSE        => 1             // 
    ); 
$curl_post = "resolution=320x240"; 
	
	$ch = curl_init($curl_url);
	
	curl_setopt_array($ch,$curl_options); 
	
	$content = curl_exec($ch); 
    $err     = curl_errno($ch); 
    $errmsg  = curl_error($ch) ; 
    $header  = curl_getinfo($ch); 
	
	curl_close($ch);
	if($err){
		return $err;
	}


	if($content){
		return $content;
	}else{
		return false;
	}
}

//Move camera position
function move_cam($pos) {
	
$cam_position = array( 
        1 => 'pier_close',        
        2 => 'pier_mid',       
        3 => 'pier_wide',       
    ); 
	
$curl_url = 'http://10.67.47.3/axis-cgi/com/ptz.cgi';
$curl_options = array( 
        //CURLOPT_RETURNTRANSFER => true,         // return web page 
        CURLOPT_HEADER         => false,        // don't return headers 
        CURLOPT_FOLLOWLOCATION => true,         // follow redirects 
        //CURLOPT_ENCODING       => "",           // handle all encodings 
        //CURLOPT_USERAGENT      => "spider",     // who am i 
        CURLOPT_AUTOREFERER    => true,         // set referer on redirect 
        CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect 
        CURLOPT_TIMEOUT        => 120,          // timeout on response 
        CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects 
        CURLOPT_USERPWD      => "nsstees:PASSWORD",           // login info 
        CURLOPT_HTTPAUTH      => CURLAUTH_BASIC,           //  
        CURLOPT_POST            => 1,           // i am sending post data 
       CURLOPT_POSTFIELDS     => "camera=1&gotoserverpresetname=".$cam_position[$pos],   // this are my post vars 
        CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl 
        CURLOPT_SSL_VERIFYPEER => false,        // 
        //CURLOPT_VERBOSE        => 1             // 
    ); 
	
	$ch = curl_init($curl_url);
	
	curl_setopt_array($ch,$curl_options); 
	
	$content = curl_exec($ch); 
    $err     = curl_errno($ch); 
    $errmsg  = curl_error($ch) ; 
    $header  = curl_getinfo($ch); 
	
	curl_close($ch);
	if($err){
		return $err;
	}

	sleep(5);

	if($content){
		return $content;
	}else{
		return false;
	}
}

//write current postion to file
function record_position($pos) {
	$file = 'pos.txt';
	$current = $pos;
	file_put_contents($file, $current);
}

//get last position
function get_position() {
	$file = 'pos.txt';
	touch($file);
	$contents = file_get_contents($file);	
	return $contents;
}

//get last position
function next_position($pos) {
	if($pos < 3){
		$pos = $pos+1;
	}else if ($pos >= 3){
		$pos = 1;
		}
	record_position($pos);
	return $pos;
}


// Save image to system
function save_image($data) {
	$filename = str_pad(time(),15,"0",STR_PAD_LEFT);
	$localpath = 'frames/';
	
	
	$cam_pos = array( 
        1 => 'close/',        
        2 => 'mid/',       
        3 => 'wide/',       
    ); 
	
    $fp = fopen($localpath.$cam_pos[get_position()].$filename.'.jpg','x');
    if(fwrite($fp, $data)){
		return true;
		};
    fclose($fp);
}

function capture() {
	
	if(date("H") > 18 && date("H") < 08 ){ 
		//is between 6pm and 8am dont capture images
		return;
	}
	
	echo 'starting</br>';
	$imgdata = get_image(); 
	if($imgdata){
		$saved = save_image($imgdata);
		if($saved){
			echo 'saved </br>';
			}
		}else{
			echo 'image not saved </br>';
			}

	$cameraposition = get_position();
	echo $cameraposition;
	next_position($cameraposition);
	move_cam($cameraposition);
	
} 


capture();

//webcam URL
//wget --http-user=webco --http-password=p13R2o13 --post-data "action=update&GuardTour.G6.Running=no"  -O - http://10.67.47.3/axis-cgi/param.cgi  >/dev/null 2>&1   


?>
</body>
</html>