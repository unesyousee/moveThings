<?php

class SendSms {

    /**
     * gets API Ultra Fast Send Url.
     *
     * @return string Indicates the Url
     */
    protected function getAPIUltraFastSendUrl() {
        return "https://ws.sms.ir/api/UltraFastSend";
    }

    /**
     * gets Api Token Url.
     *
     * @return string Indicates the Url
     */
    protected function getApiTokenUrl(){
        return "https://ws.sms.ir/api/Token";
    }

    /**
     * gets config parameters for sending request.
     *
     * @param string $APIKey API Key
     * @param string $SecretKey Secret Key
     * @return void
     */
    public function __construct($APIKey,$SecretKey){
        $this->APIKey = $APIKey;
        $this->SecretKey = $SecretKey;
    }

    /**
     * Ultra Fast Send Message.
     *
     * @param data[] $data array structure of message data
     * @return string Indicates the sent sms result
     */
    public function UltraFastSend($data) {
        $token = $this->GetToken($this->APIKey, $this->SecretKey);
        if($token != false){
            $postData = $data;
            $url = $this->getAPIUltraFastSendUrl();
            $UltraFastSend = $this->execute($postData, $url, $token);

            $object = json_decode($UltraFastSend);

            if(is_object($object)){
                $array = get_object_vars($object);
                if(is_array($array)){
                    $result = $array['Message'];
                } else {
                    $result = false;
                }
            } else {
                $result = false;
            }

        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * gets token key for all web service requests.
     *
     * @return string Indicates the token key
     */
    private function GetToken(){
        $postData = array(
            'UserApiKey' => $this->APIKey,
            'SecretKey' => $this->SecretKey,
            'System' => 'php_rest_v_1_1'
        );
        $postString = json_encode($postData);

        $ch = curl_init($this->getApiTokenUrl());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result);

        if(is_object($response)){
            $resultVars = get_object_vars($response);
            if(is_array($resultVars)){
                @$IsSuccessful = $resultVars['IsSuccessful'];
                if($IsSuccessful == true){
                    @$TokenKey = $resultVars['TokenKey'];
                    $resp = $TokenKey;
                } else {
                    $resp = false;
                }
            }
        }

        return $resp;
    }

    /**
     * executes the main method.
     *
     * @param postData[] $postData array of json data
     * @param string $url url
     * @param string $token token string
     * @return string Indicates the curl execute result
     */
    private function execute($postData, $url, $token){

        $postString = json_encode($postData);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'x-sms-ir-secure-token: '.$token
        ));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}

//send sms
/**
 * @param string $mobile
 * @param string $templateId
 * @param array $parameterArray
 */
function sendSms($mobile, $templateId, $parameterArray)
{
    try {
        date_default_timezone_set("Asia/Tehran");

        // your   panel configuration
        $APIKey = "405877e649747ad9333484f1";
        $SecretKey = "nobaar&)@&^($";

        // message data
        $data = array(
            "ParameterArray" => $parameterArray,
            "Mobile" => $mobile,
            "TemplateId" => $templateId
        );

        $SmsIR_UltraFastSend = new SendSms($APIKey, $SecretKey);
        $SmsIR_UltraFastSend->UltraFastSend($data);

    } catch (Exception $e) {
        echo 'Error UltraFastSend : ' . $e->getMessage();
    }
}

//random text numeric
function rndTextNumeric()
{
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';

    $string = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < 20; $i++) {
        $string .= $characters[mt_rand(0, $max)];
    }
    return $string;
}


function gregorian_to_jalali($gy,$gm,$gd,$mod=''){
    $g_d_m=array(0,31,59,90,120,151,181,212,243,273,304,334);
    if($gy>1600){
        $jy=979;
        $gy-=1600;
    }else{
        $jy=0;
        $gy-=621;
    }
    $gy2=($gm>2)?($gy+1):$gy;
    $days=(365*$gy) +((int)(($gy2+3)/4)) -((int)(($gy2+99)/100)) +((int)(($gy2+399)/400)) -80 +$gd +$g_d_m[$gm-1];
    $jy+=33*((int)($days/12053));
    $days%=12053;
    $jy+=4*((int)($days/1461));
    $days%=1461;
    if($days > 365){
        $jy+=(int)(($days-1)/365);
        $days=($days-1)%365;
    }
    $jm=($days < 186)?1+(int)($days/31):7+(int)(($days-186)/30);
    $jd=1+(($days < 186)?($days%31):(($days-186)%30));
    return($mod=='')?array($jy,$jm,$jd):$jy.$mod.$jm.$mod.$jd;
}


function jalali_to_gregorian($jy,$jm,$jd,$mod=''){
    if($jy>979){
        $gy=1600;
        $jy-=979;
    }else{
        $gy=621;
    }
    $days=(365*$jy) +(((int)($jy/33))*8) +((int)((($jy%33)+3)/4)) +78 +$jd +(($jm<7)?($jm-1)*31:(($jm-7)*30)+186);
    $gy+=400*((int)($days/146097));
    $days%=146097;
    if($days > 36524){
        $gy+=100*((int)(--$days/36524));
        $days%=36524;
        if($days >= 365)$days++;
    }
    $gy+=4*((int)($days/1461));
    $days%=1461;
    if($days > 365){
        $gy+=(int)(($days-1)/365);
        $days=($days-1)%365;
    }
    $gd=$days+1;
    foreach(array(0,31,(($gy%4==0 and $gy%100!=0) or ($gy%400==0))?29:28 ,31,30,31,30,31,31,30,31,30,31) as $gm=>$v){
        if($gd<=$v)break;
        $gd-=$v;
    }
    return($mod=='')?array($gy,$gm,$gd):$gy.$mod.$gm.$mod.$gd;
}
function dateTojal($date){
    if ($date){
        $time= isset(explode(' ',$date)[1])? explode(' ',$date)[1] : '00:00';
        $time = explode(':', $time);
        list($gy,$gm,$gd)=explode('-',$date);
        list($gd)=explode(' ',$gd);
        $j_date_array=gregorian_to_jalali($gy,$gm,$gd);
        return $time[0].':'.$time[1]. ' ' . $j_date_array[0]. '/'. $j_date_array[1].'/'.$j_date_array[2] ;
    }
    return 'بدون تاریخ';
}
function dayOweek($date){
    $date = strtotime($date);
    $days =[
         'یکشنبه',
         'دوشنبه',
         'سه شنبه',
         'چهار شنبه',
         'پنچ شنبه',
         'جمعه',
         'شنبه',
    ];
    return $days[ gmdate('w', $date) ];
}

class PointLocation {
    var $pointOnVertclass = true; // Check if the point sits exactly on one of the vertices?

    function pointLocation() {
    }

    function pointInPolygon($point, $polygon, $pointOnVertex = true) {
        $this->pointOnVertex = $pointOnVertex;

        // Transform string coordinates into arrays with x and y values
        $point = $this->pointStringToCoordinates($point);
        $vertices = array();
        foreach ($polygon as $vertex) {
            $vertices[] = $this->pointStringToCoordinates($vertex);
        }

        // Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
            return "vertex";
        }

        // Check if the point is inside the polygon or on the boundary
        $intersections = 0;
        $vertices_count = count($vertices);

        for ($i=1; $i < $vertices_count; $i++) {
            $vertex1 = $vertices[$i-1];
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
                return "boundary";
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) {
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++;
                }
            }
        }
        // If the number of edges we passed through is odd, then it's in the polygon.
        if ($intersections % 2 != 0) {
            return "inside";
        } else {
            return "outside";
        }
    }

    function pointOnVertex($point, $vertices) {
        foreach($vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }

    }

    function pointStringToCoordinates($pointString) {
        $coordinates = explode(" ", $pointString);
        return array("x" => $coordinates[0], "y" => $coordinates[1]);
    }

}

function checkLocation($lat, $long, $city_id)
{
    $pointLocation = new PointLocation();
    $polygonNodes = \App\PolygonNode::where('city_id', $city_id)->get();
    $first = $polygonNodes[0];
    $polygon = [];
    foreach ($polygonNodes as $polygonNode)
        array_push($polygon, $polygonNode->lat.' '.$polygonNode->long);
    array_push($polygon, $first->lat.' '.$first->long);

    return $pointLocation->pointInPolygon($lat.' '.$long, $polygon) != 'outside';
}

function persianDigit($string){
    $arr = ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',];
    foreach($arr as $key=>$value)
        $string = str_replace($value, $key, $string);
    return $string;
}