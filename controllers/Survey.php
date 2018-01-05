<?php
 class Survey extends Controller {
    public $tbls;
    /***
     * @param 
     */
    public function __construct($db) {
        $this->db =$db;
        $this->cols = ["title","description","ulist_id"];
        $this->tbls = ["kasicare.survey","kasicare.survey_question",
            "kasicare.survey_question_option","kasicare.survey_answers"];
        $this->tbl = $this->tbls[0];
        $this->scols=[["title","description","ulist_id"],
            ["question","type_","surv_id"],
            ["surv_question_id","option"],
            ["surv_id","response","ulist_id"]];
    }
    public function set(){//inserts surveys
        $db = $this->db;
        switch (filter_input(INPUT_POST,"detail")) {
            case "question":
                $opt = 1;
                break;
            case "option":
                $opt = 2;
                break;
            case "answer":
                $opt = 3
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
    public function add() {
        
    }

}
