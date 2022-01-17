<?php
class AppointmentStorage {
    protected $content;
    protected $filepath;
  
    public function get(){return $this->content;}
    public function load() {
        $file_contents = file_get_contents($this->filepath);
        $this->content = json_decode($file_contents, TRUE) ?: [];
      }
    
    public function save() {
        $json_content = json_encode($this->content, JSON_PRETTY_PRINT);
        file_put_contents($this->filepath, $json_content);
       // $this->load();
      }
  
    public function __construct($filename) {
      if (!is_readable($filename) || !is_writable($filename)) {
        throw new Exception("Data source ${filename} is invalid.");
      }
  
      $this->filepath = realpath($filename);
      $this->load();
    }
    public function __destruct() {
      $this->save();
    }
  
    public function getAppoiById($id){
        return $this->content[$id];
    }
    public function dayInAMonthWhenExistAppoi($y,$m){
        $days = [];
        foreach($this->content as $app){
            if($app["year"] == intval($y) && $app["month"] == intval($m) && !in_array($app["day"],$days)){
                $days[] = $app["day"];
            }
        }
        return $days;
    }

    public function allAppoiInTheSameDay($y,$m,$d){
        $appoi = [];
        foreach($this->content as $app){
            if($app["year"] == intval($y) && $app["month"] == intval($m) && $app["day"] == intval($d)){
                $appoi[] =
                (object)[
                    "id" => $app["id"],
                    "hour" => $app["hour"],
                    "min" => $app["min"],
                    "limit" => $app["limit"],
                    "users" => $app["users"]
                ];
            }
        }
        return $appoi;
    }

    public function newAppoi($y,$m,$d,$hour,$min,$limit){
        $id = 0;
        foreach($this->content as $app){
            $id++;
        }
        $this->content[$id]=
        (object)[
            "id" => $id,
            "year"=> intval($y),
            "month"=> intval($m),
            "day"=> intval($d),
            "hour"=>  intval($hour), 
            "min"=>  intval($min), 
            "limit"=>  intval($limit), 
            "users"=> []
 
        ];
        $this->save();
    }

    public function userJoinAnAppoi($userId,$appoiId){
        if( $this->content[$appoiId]["limit"] >  count($this->content[$appoiId]["users"]) && !in_array($userId,$this->content[$appoiId]["users"])){
            $this->content[$appoiId]["users"][] = $userId;
            $this->save();
            return true;
        }else{
            return false;
        }
    }

    public function userRemove($userId,$appoiId){

        $array = [];        
        foreach($this->content[$appoiId]["users"] as $user){
            if($user != $userId){
                $array[] = $user;
            }
        }
        $this->content[$appoiId]["users"] = $array;
        $this->save();
    }

}

//$APPOINTMENTS = new AppointmentStorage("appointments.json");


?>