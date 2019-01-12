<?php
class Medical_professional extends Controller{
    public $scols;
    public $tbls;
    public function __construct($db)
    {
        $this->db = $db;
        $this->tbls = ["kasicare.medical_professional_list",
            "kasicare.medical_signatures",
            "kasicare.medical_specialisations"];
        $this->tbl = $this->tbls[1];
        $this->scols = [["licence_number","specialisation_key","reg_id"],
        ["ulist_id","key_file","expiration","validation","nationality_key"],
        ["key","title","description"]];
        $this->cols = $this->scols[1];
    }
    public function set()
    {
        switch (Controller::get_var("detail")) {
            case "specialisation":
                $opt = 0;
                break;
            case "list":
                $opt = 2;
                break;
            default:
                throw new Exception("Not an option");
                break;
        }
        $tbl = $this->tbls[$opt];
        $cols = $this->scols[$opt];
        $vals = $this->valuate_s([],$cols,Controller::get_obj(true));
        $qry = $this->db->insert($tbl,$cols,$vals);
        $stmt = $this->db->transaction($qry);
        $stmt->execute();
        return (["msg"=> $qry, "why"=>$stmt->errorInfo(),"extra"=>$vals]);
    }
    public function add(){
        $tbl = $this->tbl;
        $cols = $this->cols;
        $vals = $this->valuate_s([], $cols,Controller::get_obj(true));
        $db = $this->db;
        $qry = $db->insert($tbl, $cols, $vals);
        $stmt = $db->transaction($qry);
        $stmt->execute();
        return (["msg"=> $qry, "why"=>$stmt->errorInfo(),"extra"=>$vals]);
    }
}