<?php
class Controller{
    public $tbl;
    public $cols;
    public $list_size;
    /**
     * @var Q_ueryBuild 
     */
    public $db;
    public function __construct()
    {
        $this->list_size = 5;
    }
    public function set()
    {
        
    }
    public function add()
    {

    }
    public function get_all($pointer = 0)
    {
        $db = $this->db;
        $tbl=$this->tbl;
        try {
            $qry1 = $db->slct("*", $tbl," 1=1 LIMIT $pointer,$this->list_size");
            $stmt = $db->transaction($qry1);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            return ['exception'=>$exc->getTraceAsString()];
        }
    }
    public function get($pointer)
    {
        $db =  $this->db;
        $tbl = $this->tbl;
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
    public function update($pointer,$t = null,$col = null)
    {
        $db = $this->db;
        $tbl = $t==null?$this->tbl:$t;
        $cols = $col==null?$this->cols:$col;
        $sorted = $this->valuesToString($cols);
        $qry = $db->update($tbl, $sorted, "id='$pointer'");
        $stmt = $db->transaction($qry);
        $stmt->execute();
        return (["msg"=>$stmt->errorCode(),
                        "why"=>$stmt->errorInfo()]);
    }
    public function delete(Type $var = null)
    {
        # code...
    }
		private function switchr(){
			return 0;
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