<?php 
/*
	DO NOT PUT ANY BUSINESS LOGIC IN HERE! THAT GOES TO THE MODEL DAO CLASSES!
	CONTROLLERS ARE JUST FOR CONTROLLING NOT PROCESSING BUSSINESS LOGIC!
*/
//use Philo\Blade\Blade;
namespace Includes\Curlinfo;
use Loader;
use Config\Config;

class curl {
	
  public function get_info_Id( $info_id ){
       $curl['_url'] = "https://api.us.apiconnect.ibmcloud.com/ubpapi-dev/sb/api/RESTs/getBranch?id=".$info_id;
       //$curl['_url'] = "https://api.us.apiconnect.ibmcloud.com/ubpapi-dev/sb/api/RESTs/getBranch?id=".$id;
       $result = self::request_curl($curl); 
       return $result;
  } 

    public function request_curl( $curl_info ){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $curl_info['_url'],
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "content-type: application/json",
            "x-ibm-client-id: ".Config::$_client_id,
            "x-ibm-client-secret: ".Config::$_client_secret
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          return json_decode($response, true);
          //echo json_decode($response, true);
        }

    }

}
