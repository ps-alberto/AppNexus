<?php

class Appnexus
{

  //WIKI AUTH - https://wiki.appnexus.com/display/api/Authentication+Service

  public $_authUrl;
  public $_oauthToken;

  /**
  * Class constructor.
  *
  */
  public function __construct()
  {
    //echo "Appnexus API Integration Constructor\n\n";
    $this->_authUrl = 'https://api.appnexus.com/auth';
    
  }

  /**
  * Appnexus Authentication, using default credentials.
  * Set a 2 hour token.
  * 
  */
  public function Authentication()
  {

    if (!isset($_COOKIE["authToken"])) {
      $_objResponse = array();

      $_credentials = '{
        "auth": {
          "username" : "xxxxxx",
          "password" : "xxxxxx"
        }
      }';

      $_headers = array();

      $_headers[] = 'Content-Type: application/x-www-form-urlencoded';

      $_ch = curl_init();

      curl_setopt($_ch, CURLOPT_URL, $this->_authUrl);
      curl_setopt($_ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($_ch, CURLOPT_POSTFIELDS, $_credentials);
      curl_setopt($_ch, CURLOPT_POST, 1);
      curl_setopt($_ch, CURLOPT_HTTPHEADER, $_headers);

      $_result = curl_exec($_ch);

      if (curl_errno($_ch)) {

        $_objResponse['status']   = 'ko';
        $_objResponse['json_obj'] = '';
        $_objResponse['error']    = curl_error($_ch);


      }else{

        $_objDecode = json_decode($_result, true);

        $_objResponse['status']    = 'ok';
        $_objResponse['json_obj']  = json_decode($_result, true);
        $_objResponse['error']     = false;

        setrawcookie("authToken",  $_objDecode["response"]["token"], time()+7200);
        $this->_oauthToken = $_objDecode["response"]["token"];

      }

      curl_close($_ch);

      return $_objResponse;  
    } else {
      $this->_oauthToken = $_COOKIE["authToken"];
    }


  }

  /**
  * Member
  * 
  */
  public function Member()
  {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.appnexus.com/member');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


    $headers = array();
    $headers[] = 'Authorization: '. $this->_oauthToken;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $_result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }else{

       $_objDecode = json_decode($_result, true);
       return $_objDecode;
       //$this->_memberID = $_objDecode["response"]["member"]["id"];

    }

    curl_close($ch);

    return false;
  }


  /**
  * Advertisers
  * 
  */
  public function Advertisers()
  {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.appnexus.com/advertiser');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    $headers = array();
    $headers[] = 'Authorization: '.$this->_oauthToken;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $_result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    return $_result;
  }


  /**
  * Campaign
  * 
  */
  public function Campaign()
  {

    $ch = curl_init();

    //curl_setopt($ch, CURLOPT_URL, 'https://api.appnexus.com/campaign?advertiser_id=XXXXXXX'); - from specific advertiser
    curl_setopt($ch, CURLOPT_URL, 'https://api.appnexus.com/campaign');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


    $headers = array();
    $headers[] = 'Authorization: '.$this->_oauthToken;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $_result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }else{

       //$_objDecode = json_decode($_result, true);
       //$this->_memberID = $_objDecode["response"]["member"]["id"];

    }


    return $_result;

    curl_close($ch);

  }


  /**
  * LineItem
  * 
  */
  public function LineItem()
  {

    $ch = curl_init();

    //curl_setopt($ch, CURLOPT_URL, 'https://api.appnexus.com/line-item?advertiser_id=XXXXXXX'); - from specific advertiser
    curl_setopt($ch, CURLOPT_URL, 'https://api.appnexus.com/line-item');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


    $headers = array();
    $headers[] = 'Authorization: '.$this->_oauthToken;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $_result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }else{

    }

    $_objDecode = json_decode($_result, true);

    $_objResponse = array();
    $_tempObj = array();

  
    foreach($_objDecode["response"]["line-items"] as $lineitems)
    {

      if($_objResponse[$lineitems['advertiser_id']]){

        $_tempObj['id'] = $lineitems['id'];
        $_tempObj['id-api'] = $lineitems['advertiser_id'].'-'.$lineitems['id'];
        $_tempObj['name'] = $lineitems['name'];
        $_tempObj['state'] = $lineitems['state'];

        $_objResponse[$lineitems['advertiser_id']]['line-items'][$lineitems['id']] = $_tempObj;

      }
      else{

        $_tempObj['id'] = $lineitems['advertiser']['id'];
        $_tempObj['name'] = $lineitems['advertiser']['name'];

        $_objResponse[$lineitems['advertiser_id']] = $_tempObj;

        $_tempObj['id'] = $lineitems['id'];
        $_tempObj['id-api'] = $lineitems['advertiser_id'].'-'.$lineitems['id'];
        $_tempObj['name'] = $lineitems['name'];
        $_tempObj['state'] = $lineitems['state'];

        $_objResponse[$lineitems['advertiser_id']]['line-items'][$lineitems['id']] = $_tempObj;

      }

    }  

    return $_objResponse;

    curl_close($ch);

 }


  /**
  * ManageLine by action and advertiser
  * 
  */
  public function ManageLine($action, $lineItemID, $advertiserID)
  {

    //ACTIVE, INACTIVE

    $_lineParams = '{
        "line-item": {
            "state": "'.$action.'"
        }
    }';


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.appnexus.com/line-item?id='.$lineItemID.'&advertiser_id='.$advertiserID);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_lineParams);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

    $headers = array();
    $headers[] = 'Authorization: '.$this->_oauthToken;
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);


    return $result;

    
  }

  /**
  * GenerateReport
  * 
  */
  public function GenerateReport(){

    // RECEIVED PARAMETERS 

    if (isset($_GET["report_type"]))
      $report_type= $_GET["report_type"];

    if (isset($_GET["start_date"])){
      $start_date = $_GET["start_date"];
      $start_date = date("Y-m-d 00:00:00", strtotime($start_date));
    }

    if (isset($_GET["end_date"])){
      $end_date = $_GET["end_date"];
      $end_date = date("Y-m-d 00:00:00", strtotime($end_date));
    }


    // REPORT TYPES - ADD MORE ...
    // REPORT API : https://wiki.xandr.com/display/api/Report+Service

    switch ($_GET["report_type"]) {
      case 'geo_analytics':
      $columns  = '["imps", "clicks", "geo_city_name", "geo_region_name", "insertion_order_name", "line_item_name"]';
      break;
      case 'network_site_domain_performance':
      $columns  = '["imps", "clicks", "top_level_category_name", "insertion_order_name", "line_item_name"]';
      break;
      case 'network_device_analytics':
      $columns  = '["imps", "clicks", "device_type", "connection_type", "operating_system_name", "insertion_order_name", "line_item_name"]';
      break;
      default:
      die;
      break;
    }



    // OBJECT CONFIGURATION - NO NEED TO CHANGE FOR OTHER REPORT TYPES
    $_reportParams = '{
      "report": {
        "report_type": "'.$report_type.'",
        "start_date": "'.$start_date.'", 
        "end_date":"'.$end_date.'",
        "columns": '.$columns.',
        "format": "csv"
      }
    }';



    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.appnexus.com/report');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_reportParams);
    //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POST, 1);

    $headers = array();
    $headers[] = 'Authorization: '.$this->_oauthToken;
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    $_objDecode = json_decode($result, true);

    //print_r($_objDecode); exit;


    // the next lines could be better but could be worse too ¯\_(ツ)_/¯

    if($_objDecode){

      $ReportID = $_objDecode["response"]["report_id"];

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, 'https://api.appnexus.com/report-download?id=' . $ReportID);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $headers = array();
      $headers[] = 'Authorization: '.$this->_oauthToken;
      $headers[] = 'Content-Type: application/x-www-form-urlencoded';
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


      $result = curl_exec($ch);
      if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
      }
      curl_close($ch);

      $Data = str_getcsv($result, "\n"); //parse the rows
      $Headers = explode(",", $Data[0]);
      $Data = array_splice($Data,1);

      $Res = array();
      $max = sizeof($Data);
      foreach ($Data as $key => $value) {
        array_push($Res, array_combine($Headers, explode(",",$value)));
      }
      return json_encode($Res);

    }

    //return $_objDecode["response"]["report_id"];

  }


  /**
  * Class destructor.
  *
  */
  public function __destruct()
  {

    print "\n\nDestroying " . __CLASS__ . "\n\n";

  }



}

?>