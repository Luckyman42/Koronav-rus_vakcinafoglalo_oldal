<?php 
//region JsonStoregaClass
  class UserStorage {
    protected $contents;
    protected $filepath;
    
    public function get(){return $this->contents;}
    public function load() {
        $file_contents = file_get_contents($this->filepath);
        $this->contents = json_decode($file_contents, TRUE) ?: [];
      }
    
    public function save() {
        $json_content = json_encode($this->contents, JSON_PRETTY_PRINT);
        file_put_contents($this->filepath, $json_content);
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
  
    public function add($email,$password, $fullname, $taj, $address) {
        $id = 0;
        foreach($this->contents as $data)
        {
            ++$id;
        }

      $this->contents[$id] = 
      (object)[
          "id" => $id,
          "email" => $email,
          "password" => $password,
          "fullname" => $fullname,
          "taj" => $taj,
          "address" => $address,
          "appointment" => -1
      ];

      $this->save();
      return $id;
    }
  
    public function findById($id) {
      return $this->contents[$id] ?? NULL;
    }

    public function findIdByEmail($email){
        foreach($this->contents as $user)
        {
            if($user["email"] == $email)
                return $user["id"];
        }
        return -1;
    }
    
    public function haveThisEmail($email){
        foreach($this->contents as $user)
        {
            if($user["email"] == $email)
                return true;
        }
        return false;
    }

    public function validPassword($email, $pas){
        foreach($this->contents as $user)
        {
            if($user["email"] == $email){
                //return $pas == $user["password"];
                return password_verify($pas,$user["password"]);
            }
        }
    }

    public function addAppoinntmentToUser($userid, $appoiId){
        $this->contents[$userid]["appointment"] = intval($appoiId);
        $this->save();
    }
    public function removeAppointmentfromUser($userid){
        $this->contents[$userid]["appointment"] = -1;
        $this->save();
    }
    public function usersInTheSameAppoi($appoiId){
        $array = [];
        foreach($this->contents as $user)
        {
            if($user["appointment"] == intval($appoiId)){
                $array[] = $user;
            }
        }
        return $array;
    }
  }

//endregion

//$USERS = new UserStorage("users.json");




?>