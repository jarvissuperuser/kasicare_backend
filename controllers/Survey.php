<?php
 class Survey extends Controller {
    public $tbls;
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
    public function set($pointer){
        switch (filter_input(INPUT_POST,"detail")) {
            case "question":
                $tbl = $this->tbls[1];
                $cols = $this->scols[1];
                break;
            case "option":
                $tbl = $this->tbls[2];
                $cols = $this->scols[2];
                break;
            case "question":
                $tbl = $this->tbls[3];
                $cols = $this->scols[3];
                break;

            default:
                break;
        }
    }
    public function add() {
        
    }

}
