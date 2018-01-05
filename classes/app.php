<?php
class App{
    protected $index = "home";
    protected $db=null;
    public function __construct()
    { 
        $this->db = new Q_ueryBuild();
        $pntr = $this->parseUrl();
        $data = $this->data_process($pntr);
        echo json_encode($data);
    }
    public function parseUrl()
    {
        if (strlen(filter_input(INPUT_GET,"url"))>0){
            return explode('/',rtrim(filter_input(INPUT_GET,"url")));
        }
    }
    public function data_process($p){
        if (is_array($p)){
            switch ($p[0]) {
                case 'api':
                    return $this->process_api();
                    break;
                case 'view':
                    break;
                default:
                    # code...
                    break;
            }
        }
    }
    public function process_api(){
        $pointer = filter_input(INPUT_POST,"pntr",
                FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        switch (filter_input(INPUT_POST,"submit")) {
            case 'add_user':
                return $this->add_user();
            case "get_users":
                return $this->get_users();
            case "get_user":
                return $this->get_user($pointer);
            case "add_article":
                return $this->add_article();
            case "get_article":
                return $this->get_article($pointer);
            case "get_articles":
                return $this->get_articles($pointer);
            case "update":
                return $this->update($pointer);
            case "delete":
                return ["Not Implemented"];// $this->delete($pointer);
            case "login":
                return $this->login();   
            default:
                return ["Empty"];
        }
    }
    //crud plus helpers
    //create
    public function add_user(){
        $db = $this->db;
        $tbl=["kasicare.user_list","kasicare.user_signatures"];
        try{
            $cols = ["name","surname","email","phone","unique_id","gender"];
            $vals  = $this->valuate([], $cols);
            $qry1 = Q_ueryBuild::insert($tbl[0], $cols, $vals);
            $stmt = $db->transaction($qry1);
            $stmt->execute();
            $p_key = hash("sha256", filter_input(INPUT_POST, "user_passcode",
                                FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $qry2 = $db->insert($tbl[1], ["ulist_id","nationality_key","user_passcode","salt_version"],
                            [$db->db->lastInsertId(),"ZA", $p_key],'1');
            $stmt2 = $db->transaction($qry2);
            $stmt2->execute();
            return json_encode($stmt->errorInfo());
        } catch (Exception $e){
            $data = ["why"=> $e->getMessage(),"error"=>$e->getCode()];
            return json_encode($data);
        }
    }
    //   not implememted yet
    public  function add_article(){
        $db = $this->db;
        try {
            $fn = filter_input(INPUT_POST, 'image_1');
            $fe = filter_input(INPUT_POST, 'ext');
            $file = substr(hash('sha256',
                            filter_input(INPUT_POST, 'image_1')),0,60) . '.' . $fe;
            $exp = filter_input(INPUT_POST, "exp");
            $updata = filter_input(INPUT_POST, "data");
            $cs = filter_input(INPUT_POST, "chcksm");
            if ($exp < $cs && $fn != 'none'){
                return "{\"". $fe .'":"'.$exp . "\",\"" . file_put_contents($file , 
                                base64_decode($updata), FILE_APPEND) . "\":\"" . $cs . "\"}";
            }
            if ($exp >= $cs || $fn == 'none'){
                $msg = 'saving started...';
                $cols = ["title","main","creator","image_1","image_2","title_description"];
                $vals  = $this->valuate([], $cols);
                $msg .= $file . "<+= " . $fn . " names" ;
                if ($fn != 'none'){	$vals[3] = $file;}
                $qry1 = Q_ueryBuild::insert("newsroom.articles", $cols,$vals);
                $stmt = $db->transaction($qry1);
                $stmt->execute();
                return json_encode(["msg"=>$msg . $qry1, "why"=>$stmt->errorInfo(),"extra"=>$val]);
            }
        } catch (Exception $e){
            $data = ["why"=> $e->getMessage(),"error"=>$e->getCode()];
            return json_encode($data);
        }
    }

    //read
    public function get_users(){
        $db = $this->db;
        $tbl=["kasicare.user_list","kasicare.user_signatures"];
        try {
            $qry1 = $db->slct("*", $tbl[0]);
            $stmt = $db->transaction($qry1);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            return ['exception'=>$exc->getTraceAsString()];
        }
    }
    public function get_articles($pointer){//ni
        $db = $this->db;
        $res = [];
        $cols = ["title","img_2","id"];
        $qry = $db->slct($cols, "newsroom.news", "1=1 limit $pointer,5");
        $setup = $db->transaction($qry);
        $setup->execute();
        if ($setup->errorCode() == "0000"){
            $res = $setup->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }else{
            return (["msg"=>$setup->errorInfo()]);
        }
    }
    public function get_article($pointer)//ni
    {
        $db =  $this->db;
        $res = [];
        $qry = $db->slct('*', "newsroom.news", "id='$pointer'");
        $setup = $db->transaction($qry);
        $setup->execute();
        if ($setup->errorCode() == "0000"){
            $res = $setup->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }else{
            return ["msg"=>$setup->errorInfo(),"sql"=>$qry];
        }
    }
    public function get_user($pointer)//not implemented
    {
        $db =  $this->db;
        $tbl=["kasicare.user_list","kasicare.user_signatures"];
        $res = [];
        $qry = $db->slct('*', $tbl[0], "id='$pointer'");
        $setup = $db->transaction($qry);
        $setup->execute();
        if ($setup->errorCode() == "0000"){
            $res = $setup->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }else{
            return ["msg"=>$setup->errorInfo(),"sql"=>$qry];
        }
    }
    //update
    public function update($user_id)
    {
        $db = $this->db;
        if (strlen(filter_input(INPUT_POST, "email"))>0){
            $cols = ["first_name","middle_name","last_name","nick_name"];
            $tbl = "newsroom.content_creators";
        }else if (strlen(filter_input(INPUT_POST, "article"))>0){
            $cols = ["title","main","title_description"];
            $tbl = "newsroom.articles";
        }else if (strlen(filter_input(INPUT_POST, "aritcle_img"))>0){
            $cols = ["image_1","image_2"];
            $tbl = "newsroom.articles";
        }
        $sorted = valuesToString($cols);
        $qry = $db->update($tbl, $sorted, "id='$user_id'");
        $stmt = $db->transaction($qry);
        $stmt->execute();
        echo json_encode(["msg"=>$stmt->errorInfo(),
                        "why"=>$stmt->errorCode()]);
    }
    //helper

    public function login()
    {
        $db = $this->db;
        $selection = "*";
        $tbl = "newsroom.content_creators";//table to query
        $email = filter_input(INPUT_POST,"email",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $what = "email='$email'";
        $qry = $db->slct($selection, $tbl, $what);
        $stmt = $db->transaction($qry);
        $stmt->execute();
        if ($stmt->errorCode()=="0000"){
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $slt = "count(p_key) as password";
            $table = "newsroom.security";//table to query
            $p_key = hash("sha256",filter_input(INPUT_POST,"p_key",
                            FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $w = "cc_id='" . $res["id"] . "' and p_key='$p_key' ";
            $qry1 = $db->slct($slt, $table, $w);
            $stmt1 = $db->transaction($qry);
            $stmt1->execute();
            $r = $stmt1->rowCount();
            if ($stmt1->errorCode()=="0000"&&$r == 1){
                return ($res);
            }else{
                return (["msg"=>"error","why"=>$stmt1->errorInfo(),
                        "extra"=>"Possible Password Mismatch 1"]);
            }
        }else{
            return (["msg"=>"error","why"=>$stmt->errorInfo(),
                        "extra"=>"Possible Password Mismatch 0"]);
        }
    }
    public function valuate($vals,$cols){
        foreach($cols as $d){
                array_push($vals,filter_input(INPUT_POST, $d,
                                FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            }
            return $vals;
    }
    public function valuesToString($cols){
        $what ="";
        foreach($cols as $d){
            $what .= $d . "='".filter_input(INPUT_POST, $d,
                            FILTER_SANITIZE_FULL_SPECIAL_CHARS)."',";
        }
        return substr($what,0 ,strlen($what)-1);
    }
}