<?php
 class Survey extends Controller {
    public $tbls;
    public $scols;
    /**
     * @param Q_ueryBuild $db  
     */
    public function __construct($db) {
        parent::__construct();
        $this->db = $db==null?new Q_ueryBuild():$db;
        $this->cols = ["title","description","ulist_id"];
        $this->tbls = ["kasicare.survey","kasicare.survey_question",
            "kasicare.survey_question_option","kasicare.survey_answers"];
        $this->tbl = $this->tbls[0];
        $this->scols=[["title","description","ulist_id"],
            ["question","type_","surv_id"],
            ["surv_question_id","option"],
            ["surv_id","response","ulist_id"]];
    }
    public function set(){//inserts survey extras
        $db = $this->db;
        switch (filter_input(INPUT_POST,"detail")) {
            case "question":
                $opt = 1;
                break;
            case "option":
                $opt = 2;
                break;
            case "answer":
                $opt = 3;
                break;
            default:
                break;
        }
        $tbl = $this->tbls[$opt];
        $cols = $this->scols[$opt];
        $vals = $this->valuate([],$cols);
        $qry = $db->insert($tbl,$cols,$vals);
        $stmt = $db->transaction($qry);
        $stmt->execute();
        return (["msg"=> $qry, "why"=>$stmt->errorInfo(),"extra"=>$vals]);
    }
    public function add() {//declares a survey
        $tbl = $this->tbl;
        $cols = $this->scols[0];
        $vals = $this->valuate([], $cols);
        $db = $this->db;
        $qry = $db->insert($tbl, $cols, $vals);
        $stmt = $db->transaction($qry);
        $stmt->execute();
        return (["msg"=> $qry, "why"=>$stmt->errorInfo(),"extra"=>$vals]);
    }

}
