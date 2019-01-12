<?php
class User extends Controller{
    public $tbls;
    public $cols;
    public $scols;
    public function __construct($db) {
        parent::__construct();
        $this->db = $db;
        $this->tbls = ["kasicare.user_list","kasicare.user_signatures","kasicare.user_details"];
        $this->scols = [["name","surname","email","phone","unique_id","gender"],
            ["ulist_id","nationality_key","user_passcode","salt_version"],
            ["date_of_birth","id_number","occupation","address","work","ulist_id"]];
        $this->cols = $this->scols[0];
        $this->tbl = $this->tbls[0];
    }
    public function add() {
        $db = $this->db;
        $tbls=$this->tbls;
        try{
            $cols = ["name","surname","email","phone","unique_id","gender"];
            $vals = $this->valuate_s([], $cols ,Controller::get_obj(true));
            $st=$this->user_check();$st->execute();
            if ($st->rowCount()>0)throw new Exception("Already Registered");
            $qry1 = Q_ueryBuild::insert($tbls[0], $cols, $vals);$stmt = $db->transaction($qry1);
            $stmt->execute();
            $p_key = $this->get_hash_password();
            $i_d = $db->db->lastInsertId();
            $qry2 = $db->insert($tbls[1], $this->scols[1],
                            [$i_d,"ZA", $p_key,'1']);
            $stmt2 = $db->transaction($qry2);
            $stmt2->execute();
            $qry3 = $db->insert($tbls[2], [$this->scols[2][5],$this->scols[2][0]],
                [$i_d, date("Y-m-d",strtotime(Controller::get_var("date_of_birth")))]);
            $stmt2 = $db->transaction($qry3);
            $stmt2->execute();
            Controller::sendStatus(201);
            return ["data"=>$stmt->errorInfo(),"entry"=>$i_d,"status"=>"success"];
        } catch (Exception $e){
            $data = ["why"=> $e->getMessage(),"error"=>$e->getCode()];
            return ($data);
        }
    }
    public function resetPassword() {
        $tbl = "";
        $cols = [];
    }
    public function login(){
        $db=$this->db;$stmt = $this->user_check();$stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($stmt->errorCode()=="0000"&&$res['id']>0){
            $slt = "*";
            $table = $this->tbls[1];;//table to query
            $p_key = $this->get_hash_password();
            $w = "ulist_id='" . $res["id"] . "' and user_passcode='$p_key' ";
            $qry1 = $db->slct($slt, $table, $w); $stmt1 = $db->transaction($qry1); $stmt1->execute();
            $r = $stmt1->rowCount();
            if ($stmt1->errorCode()=="0000"){
                return ([$res]);
            }else{
                return (["msg"=>"error","why"=>$stmt1->errorInfo(),
                        "extra"=>"Possible Password Mismatch 1"]);
            }
        }else{
            return (["msg"=>"error","why"=>$stmt->errorInfo(),
                        "extra"=>"Possible Password Mismatch 0"]);
        }
    }
    private function user_check(){
        $db = $this->db;
        $selection = "*";
        $tbl = $this->tbl;//table to query
        $email = filter_input(INPUT_POST,"email",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $phone = filter_input(INPUT_POST,"phone",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($email==null||$email==""){$email = Controller::get_obj()->email;$phone = Controller::get_obj()->phone;}
        $what = "email='$email' or phone='$phone'";
        $qry = $db->slct($selection, $tbl, $what);
        return $db->transaction($qry);
    }
    private function get_hash_password(){
        $raw = filter_input(INPUT_POST, "user_passcode",
            FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $inter = Controller::is_n($raw)?$raw:Controller::get_obj()->user_passcode;
        return  hash("sha256", $inter);
    }
    //todo: implement search
}
