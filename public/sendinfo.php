<?php
 
if (isset($_POST['submit'])){
    $result = $_POST['result'];
    $suggestions = $_POST['suggestions'];
}

$sToken = "iVMgBc70auBFrQ229Pw6rXAiz6Hnh9Mg9PgcTXML5Qu";
$sMessage = "แจ้งผลการตรวจเอกสาร";
$sMessage = "ผลการตรวจ : " . $result. "\n";
$sMessage = "คำแนะนำเพิ่มเติม : " . $suggestions. "\n";


$chOne = curl_init(); 
curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify"); 
curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0); 
curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt( $chOne, CURLOPT_POST, 1); 
curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=".$sMessage); 
$headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$sToken.'', );
curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers); 
curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1); 
$result = curl_exec( $chOne ); 

//Result error 
if(curl_error($chOne)) 
{ 
    echo 'error:' . curl_error($chOne); 
} 
else { 
    $result_ = json_decode($result, true); 
    echo "status : ".$result_['status']; echo "message : ". $result_['message'];
} 
curl_close( $chOne );   
?>