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
                case 'view':
                    $u = new User($this->db);
                    return $u->get(filter_input(INPUT_POST,"pntr"));
                default:
                    break;
            }
        }
    }
    public function process_api(){//users
        $pointer = filter_input(INPUT_POST,"pntr",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				$u = new User($this->db);
        switch (filter_input(INPUT_POST,"submit")) {
            case 'add_user':
								return $u->add();
            case "set_user":
                return $u->set();
            case "get_user":
                return $u->get($pointer);
						case "get_users":
                return $u->get_all($pointer);
						case "update_user":
							return $u->update($pointer);
            default:
                return $this->process_survey();
        }
    }
		public function process_survey($pointer) {
			$u = new Survey($this->db);
			switch (filter_input(INPUT_POST,"submit")) {
            case 'add_survey':
								return $u->add();
            case "set_survey":
                return $u->set();
            case "get_survey":
                return $u->get($pointer);
						case "get_surveys":
                return $u->get_all($pointer);
						case "update_survey":
							return $u->update($pointer);
            default:
                return $this->process_institute($pointer);
        }
		}
		public function process_institute($pointer) {
			$u = new Institution($this->db);
			switch (filter_input(INPUT_POST,"submit")) {
            case 'add_institution':
								return $u->add();
            case "set_institution":
                return $u->set();
            case "get_institution":
                return $u->get($pointer);
						case "get_institution":
                return $u->get_all($pointer);
						case "update_institution":
							return $u->update($pointer);
            default:
                return $this->process_med_prof($pointer);
        }
		}
		public function process_med_prof($pointer) {
			$u = new Medical_professional($this->db);
			switch (filter_input(INPUT_POST,"submit")) {
            case 'add_prof':
								return $u->add();
            case "set_prof":
                return $u->set();
            case "get_prof":
                return $u->get($pointer);
						case "get_prof":
                return $u->get_all($pointer);
						case "update_prof":
							return $u->update($pointer);
            default:
                return $this->process_book($pointer);
        }
		}
		public function process_book($pointer) {
			$u = new Medical_professional($this->db);
			switch (filter_input(INPUT_POST,"submit")) {
            case 'add_booking':
								return $u->add();
            case "set_booking":
                return $u->set();
            case "get_booking":
                return $u->get($pointer);
						case "get_booking":
                return $u->get_all($pointer);
						case "update_booking":
							return $u->update($pointer);
            default:
                return ["not a valid option"];
        }
		}
    //c
    //crud plus helpers
    //create
    
    //   not implememted yet
    public  function add_article(){
        $db = $this->db;
        try {
            $fn = filter_input(INPUT_POST, 'image_1');
            $fe = filter_input(INPUT_POST, 'ext');
            $file = substr(hash('sha256',filter_input(INPUT_POST, 'image_1')),0,60) . '.' . $fe;
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
                if ($fn != 'none'){	$vals[3] = $file; }
                $qry1 = Q_ueryBuild::insert("newsroom.articles", $cols,$vals);
                $stmt = $db->transaction($qry1);
                $stmt->execute();
                return json_encode(["msg"=>$msg . $qry1, "why"=>$stmt->errorInfo(),"extra"=>$vals]);
            }
        } catch (Exception $e){
            $data = ["why"=> $e->getMessage(),"error"=>$e->getCode()];
            return json_encode($data);
        }
    }

    //read
   
    //update
   
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