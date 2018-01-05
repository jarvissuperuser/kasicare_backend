<?php
class User extends Controller{
    public function __construct($db) {
        $this->db = $db;
        $this->tbl = "kasicare.user_list";
    }
    public function add() {
        $db = $this->db;
        $tbls=["kasicare.user_list","kasicare.user_signatures"];
        try{
            $cols = ["name","surname","email","phone","unique_id","gender"];
            $vals  = $this->valuate([], $cols);
            $qry1 = Q_ueryBuild::insert($tbls[0], $cols, $vals);
            $stmt = $db->transaction($qry1);
            $stmt->execute();
            $p_key = hash("sha256", filter_input(INPUT_POST, "user_passcode",
                                FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $qry2 = $db->insert($tbls[1], ["ulist_id","nationality_key","user_passcode","salt_version"],
                            [$db->db->lastInsertId(),"ZA", $p_key],'1');
            $stmt2 = $db->transaction($qry2);
            $stmt2->execute();
            return json_encode($stmt->errorInfo());
        } catch (Exception $e){
            $data = ["why"=> $e->getMessage(),"error"=>$e->getCode()];
            return json_encode($data);
        }
    }
    public function resetPassword() {
        $tbl = "";
        $cols = [];
    }
}
