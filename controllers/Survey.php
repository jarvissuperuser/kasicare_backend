<?php
 class Survey extends Controller {
    public $tbls;
    public $scols;
    /**
     * @param Q_ueryBuild $db  
     */
    public function __construct($db) {
        parent::__construct();
        $this->list_size = 20;
        $this->db = $db==null?new Q_ueryBuild():$db;
        $this->cols = ["title","description","ulist_id"];
        $this->tbls = ["kasicare.survey","kasicare.survey_question",
            "kasicare.survey_question_options","kasicare.survey_answers"];
        $this->tbl = $this->tbls[0];
        $this->scols=[["title","description","ulist_id"],
            ["question","type_","surv_id"],
            ["surv_question_id","option"],
            ["surv_id","response","ulist_id"]];
    }
    public function set(){//inserts survey extras
        $db = $this->db;
        $opt = $this->switchr();
        $tbl = $this->tbls[$opt];
        $cols = $this->scols[$opt];
        $vals = $this->valuate_s([],$cols,Controller::get_obj(true));
        $qry = $db->insert($tbl,$cols,$vals);
        $stmt = $db->transaction($qry);
        $stmt->execute();
        return (["msg"=> $db->db->lastInsertId(), "why"=>$stmt->errorInfo(),"extra"=>$vals]);
    }
		private function switchr(){
			switch (Controller::get_var("detail")) {
            case "question":
							return 1;
            case "option":
                return 2;
            case "answer":
                return 3;
            default:
                if (Controller::get_var("submit") == "set_survey")
                            throw new Exception("Option Not Valid");
                return 0;
        }
		}

		public function add() {//declares a survey
        $tbl = $this->tbl;
        $cols = $this->scols[0];
        $vals = $this->valuate_s([],$cols,Controller::get_obj(true));
        $db = $this->db;
        $qry = $db->insert($tbl, $cols, $vals);
        $stmt = $db->transaction($qry);
        $stmt->execute();
        return (["msg"=> $db->db->lastInsertId(), "why"=>$stmt->errorInfo(),"extra"=>$vals]);
    }
    public function get($pointer)
    {
        $db =  $this->db;
        $opt = $this->switchr();
        $tbl = $this->tbls[$opt];
        $res = [];
        $qry = $db->slct('*', $tbl, "id='$pointer'");
        $setup = $db->transaction($qry);
        $setup->execute();
        if ($setup->errorCode() == "0000"){
            $res = $setup->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }else{
            return ["msg"=>$setup->errorInfo(),"sql"=>$qry];
        }
    }

}
