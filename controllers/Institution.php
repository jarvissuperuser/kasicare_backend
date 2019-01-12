<?php

class Institution extends Controller
{
    public $scols;
    public $tbls;

    public function __construct($db)
    {
        parent::__construct();
        $this->db = $db;
        $this->tbls = ["kasicare.institution"];
        $this->tbl = $this->tbls[0];
        $this->scols = [["name", "longitute", "latitude", "address", "province"]];
        $this->cols = $this->scols[0];
    }

    public function set()
    {
        $db = $this->db;
        $opt = $this->switchr();
        $tbl = $this->tbls[$opt];
        $cols = $this->scols[$opt];
        $vals = $this->valuate_s([], $cols, Controller::get_obj(true));
        $qry = $db->insert($tbl, $cols, $vals);
        $stmt = $db->transaction($qry);
        $stmt->execute();
        return (["msg" => $qry, "why" => $stmt->errorInfo(), "extra" => $vals]);
    }
    /**
     * @Exception Options
     * @throws Exception
    */
    public function switchr()
    {
        switch (Controller::get_var("detail")) {
            case "Institution":
                return 0;
            default:
                throw new Exception("Not an option");
        }
    }

    public function add()
    {
        $tbl = $this->tbl;
        $cols = $this->cols;
        $vals = $this->valuate_s([], $cols, Controller::get_obj(true));
        $db = $this->db;
        $qry = $db->insert($tbl, $cols, $vals);
        $stmt = $db->transaction($qry);
        $stmt->execute();
        return (["msg" => $qry, "why" => $stmt->errorInfo(), "extra" => $vals]);
    }
}