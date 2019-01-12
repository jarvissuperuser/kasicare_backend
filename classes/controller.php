<?php
class Controller{
    public $tbl;
    public $cols;
    public $list_size;
    public static $BAD_REQ = 400;
    public static $CREATED = 201;
    public static $NO_CONTENT = 204;
    public static $BAD_AUTH = 401;
    public static $NOT_FOUND = 404;
    public static $SERVER_ERROR = 500;
    public static $TYPE_JSON = "Content-Type: application/json";
    public static $TYPE_HTML = "Content-Type: text/html; charset=iso-8859-1";
    public $silenced;
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
        return [];
    }
    public function add()
    {
        return [];
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
        $sorted = $this->valuesToString_s($cols,Controller::get_obj(true));
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

    private function switchr()
    {
        return 0;
    }

    public function valuate($vals, $cols)
    {
        foreach ($cols as $d) {
            array_push($vals, filter_input(INPUT_POST, $d,
                FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        }
        return $vals;
    }
    public function valuesToString($cols){
        $what ="";
        foreach($cols as $d){
            if (Controller::is_n(filter_input(INPUT_POST, $d,
                FILTER_SANITIZE_FULL_SPECIAL_CHARS)))
                $what .= $d . "='".filter_input(INPUT_POST, $d,
                            FILTER_SANITIZE_FULL_SPECIAL_CHARS)."',";
        }
        return substr($what,0 ,strlen($what)-1);
    }
    public  function setCols_o($cols, $obj)
    {
        $what = [];
        foreach ($cols as $d) {
            $input = $obj[$d];
            if ($input != null || $input != "")
                array_push($what, $d);
        }
        return $what;
    }

    /**
     * @param *
     * @param *
     * @return string
     */
    public function valuesToString_o($cols, $obj)
    {
        $what = "";
        foreach ($cols as $d) {
            $what .= $d . "='" . $obj[$d] . "',";
        }
        return substr($what, 0, strlen($what) - 1);
    }

    public function valuate_o($vals, $cols, $obj)
    {
        foreach ($cols as $d) {
            array_push($vals, $obj[$d] == null ? "" : $obj[$d]);
        }
        return $vals;
    }
    public function silence($newArray,$arr){
        foreach($arr as $d){
            $val = true;
            foreach($this->silenced as $m){
                if ($m == $d){
                    $val = false;
                    break;
                }
            }
            if ($val) array_push($newArray,$d);
        }
        return $newArray;
    }
    public function hash($obj){
        if ($obj["password"]!=null||$obj["password"]!="")
            $obj["password"] = hash("sha256",$obj["password"]);
        if ($obj["pass_client"] != null||$obj["pass_client"]!="")
            $obj["pass_client"] = hash("sha256",$obj["pass_client"]);
        return $obj;
    }
    public static function sendStatus($i)
    {
        switch($i){
            case 200:
                header( 'HTTP/1.0 200 OK' );
                break;
            case self::$BAD_REQ:
                $code = self::$BAD_REQ;
                header("HTTP/1.0 $code BAD REQUEST");
                break;
            case self::$BAD_AUTH:
                $code = self::$BAD_AUTH;
                header( "HTTP/1.0 $code BAD AUTH" );
                break;
            case self::$NOT_FOUND:
                $code = self::$NOT_FOUND;
                header( "HTTP/1.0 $code NOT FOUND");
                break;
            case self::$CREATED:
                $code = self::$CREATED;
                header( "HTTP/1.0 $code CREATED");
                break;
            case self::$NO_CONTENT:
                $code = self::$NO_CONTENT;
                header( "HTTP/1.0 $code NO CONTENT");
                break;
            case self::$SERVER_ERROR:
                $code = self::$SERVER_ERROR;
                header( "HTTP/1.0 $code SERVER ERROR");
                break;
        }
        flush();
    }
    public static function io($obj,$var,$default=0){
        return  ($obj[$var]!=""||$obj[$var]!=null)?$obj[$var]:($default);
    }
    public static function get_obj($assoc=false){
        $post_data = file_get_contents("php://input");
        try{
            $o_data = json_decode($post_data,$assoc);
        }catch(Exception $e){
            die(json_encode([$e->getTraceAsString()]));
        }
        return $o_data;
    }
    public static function get_var($name){
        $var = filter_input(INPUT_POST,$name,FILTER_SANITIZE_SPECIAL_CHARS);
        return Controller::is_n($var)?$var:Controller::get_obj(true)[$name];
    }

    public function valuate_s($val,$col,$obj=null){
        $submit = filter_input(INPUT_POST,"submit");
        return Controller::is_n($submit)?$this->valuate($val,$col):$this->valuate_o($val,$col,$obj);
    }

    public function valuesToString_s($cols,$obj){
        $submit = filter_input(INPUT_POST,"submit");
        return Controller::is_n($submit)?$this->valuesToString($cols):$this->valuesToString_o($cols,$obj);
    }
    public static function is_n($var){
        return $var!=""||$var!=null;
    }
}