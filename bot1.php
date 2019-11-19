<?php
error_reporting(0); 
set_time_limit(0);
ignore_user_abort(1);
require_once'config.php';
 $try=new semvak; 
  class semvak
{
public $user;
public $pass;
public $devi;
public $guid;
public $ua;
public $cookie;
  public function proccess($ighost, $useragent, $url, $cookie = 0, $data = 0, $httpheader = array(), $proxy = 0){
    $url = $ighost ? 'https://i.instagram.com/api/v1/' . $url : $url;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    if($proxy):
      curl_setopt($ch, CURLOPT_PROXY, $proxy);
    endif;
    if($httpheader) curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    if($cookie) curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    if ($data):
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    endif;
       
    	/*
    	curl_setopt($ch,CURLOPT_COOKIEFILE,'cookie.txt');
		curl_setopt($ch,CURLOPT_COOKIEJAR,'cookie.txt');
    */
    
     $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch);
    if(!$httpcode) return false; else{
      $header = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
      $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
      curl_close($ch);
      return array($header, $body);
    }
  }

private function saveFile($x,$y){
   $f = fopen($x,'w');
             fwrite($f,$y);
             fclose($f);
   }
   
private function getLog($x,$y){
if(!is_dir('log')){   mkdir('log');   }
if($x){ $x=$x; }else{ $x=0; }

   if(file_exists('log/botlike_'.$x.'.txt')){
       $log=file_get_contents('log/botlike_'.$x.'.txt');
       }else{
       $log=' ';
       }

  if(ereg($y,$log)){
       return false;
       }else{
if(strlen($log) > 5000){
   $n = strlen($log) - 5000;
   }else{
  $n= 0;
   }
       $this->saveFile('log/botlike_'.$x.'.txt',substr($log,$n).' '.$y);
       return true;
      }
 }

public function hook($data) {
    return 'ig_sig_key_version=4&signed_body=' . hash_hmac('sha256', $data, '469862b7e45f078550a0db3687f51ef03005573121a3a7e8d7f43eddb3584a36') . '.' . urlencode($data);
  }


public function tes($komen){
$req = $this->proccess(1, 
                        $this->ua, 
                       'feed/timeline/',
                       $this->cookie);
$data = json_decode($req['1'],true);
if(count($data[items]) > 0){

 for($i=0;$i<1;$i++){
// current time
echo date('h:i:s') . "\n";

// sleep for 5 seconds
sleep(5);

// wake up !
echo date('h:i:s') . "\n";
  
echo'@'.$data[items][$i][user][username].': '.$data[items][$i][id];
if($this->getLog($this->user,$data[items][$i][id])){

      
mysql_query("update count set botlike=botlike+1 where id='".$komen."'"); 
$t = $this->proccess(1, $this->ua, 'media/'.$data[items][$i][id].'/like/',$this->cookie,$this->hook('{"media_id":"'.$data[items][$i][id].'"}'));
echo $t[1];

}else {echo' <b>Liked</b>'; }
echo'<br>';}
}else{
if($data[message]=='login_required'||$data[status]=='fail'){
mysql_query("delete from instagram where id='".$komen."'");
mysql_query("delete from count where id='".$komen."'");
unlink('log/botlike_'.$this->user.'.txt');
echo '<br>Not work Mati<br>';   
}
    }
} 

   }
if(isset($_GET['token'])){
$row = null;
   $resultt = mysql_query("
      SELECT
         *
      FROM
         instagram 
      WHERE
MD5(username) = '" . mysql_real_escape_string($_GET['token']) . "'
   ");
   }else{
$resultt = mysql_query("SELECT * FROM instagram ORDER BY username DESC LIMIT 50");
}


while($row = mysql_fetch_array($resultt)){
 
 $try->user=$row[username];
 $try->pass=$row[password];
 $try->ua=$row[useragent];
 $try->devi=$row[device_id];
 $trt->guid=$row[guid];  
 $try->cookie=$row[cookies];
echo ' User: '.$row[username];
echo'<hr>';
   $cdr=mysql_query("SELECT * FROM count WHERE id = '".$row[id]. "'");
    if(!mysql_num_rows($cdr) > 0){
    mysql_query("INSERT INTO count SET
         `id` = '".$row[id]."',
          `botlike` = '0',
          `likeflike` = '0',
          `create_time` = '".date("Y-m-d",time())."'
      ");
}
$try->tes($row[id]); 
  }
 


 ?>

