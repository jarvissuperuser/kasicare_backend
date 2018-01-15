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
            case "login":
            case "sign_in":
                return $u->login();
            default:
                return $this->process_survey($pointer);
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
    

    //read
   
    //update
   
    //helper
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