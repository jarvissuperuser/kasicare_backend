<?php

/**
 * @author Timothy Tavonga Mugadza
 */
class Q_ueryBuild
{
  /**
   *
   *//**
   * @var PDO connect
   */
  public $db;
  private $dsn0;
  private $dsn1;
  private $user;
  private $pwd;

  public function __construct()
  {
    try {
      $this->init();
    } catch (Exception $e) {
      $this->db = null;
      echo json_encode([$e,$e->getTraceAsString()]);
      //error_log($e);
    }
  }

  public function setdsn($con = 'mysql', $url = 'localhost') {
    $this->user = "cm9vdA=="; //base64 obfuscicated
    $this->pwd = "cm9vdDEyMzQ=";
    $this->dsn0 = "$con:host=$url;";
    $this->dsn1 = $this->dsn0 . 'name=newsroom;';
  }

  /**
   * @param void $selection
   * @param void $table
   * @param void $what
   * @return string
   */
  public static function slct($selection, $table, $what) {
    // TODO: implement here
    $str = "SELECT ";
    $str .= Q_ueryBuild::arrayJustify($selection,0) . " FROM " . $table;
    if ($what != null)
      $str .= " WHERE $what";
    return $str;
  }

  /**
   * @param void $table string
   * @param void $what string
   * @param void $id string
   */
  public static function update($table, $what, $id) {
    // TODO: implement here
    $str = "UPDATE $table SET ";
    $str .= "$what WHERE $id";
    return $str;
  }

  /**
   * support function for constructor
   * loads db and initiates 
   */
  public function init() {
    // TODO: implement here
    $this->setdsn();
    if (!file_exists("ready.x")) {
      $this->db = new PDO($this->dsn0, base64_decode($this->user), base64_decode($this->pwd));
      $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sql = file_get_contents('classes/sql.sql');
      $this->db->exec($sql);
      $fil = fopen("ready.x", 'w');
      fwrite($fil, $this->db->errorcode());
      fclose($fil);
    }
    $this->db = new PDO($this->dsn1, base64_decode($this->user), base64_decode($this->pwd));
		$this->db->setAttribute(PDO::ERRMODE_EXCEPTION, 1);
  }

  /**
   * @param void $tble string
   * @param void $cols not array...
   * @param void $vals can be array or string
   */
  public static function insert($tble, $cols, $vals) {
    // TODO: implement here
    $str = "INSERT INTO ";
    $str .= $tble . " (";
    $str .= Q_ueryBuild::arrayJustify($cols,0) . " ) VALUES ( ";
    $str .= Q_ueryBuild::arrayJustify($vals) . ")";
    return $str;
  }

  /**
   * @param void $obj depricate
   */
  public static function arrayJustify($obj,$mode = 1) {
    // TODO: implement here
    $res = "";
    if (is_array($obj)) {
      $res = " `{$obj[0]}` ";
      if ($mode == 1){
        $res = "'{$obj[0]}'";
      }
      $res .= Q_ueryBuild::Justify($obj,$mode);
    } else {
      $res = $obj;
    }
    return $res;
  }

  /**
   * support function to arrayJustify
   * @param void $obj deprecate
   */
  public static function Justify($obj,$mode) {
    // TODO: implement here
    $res = "";
    $len = sizeof($obj);
    if ($obj[1] !== NULL) {
      for ($a = 1; $a < $len; $a++) {
        $res .= ($mode == 1)?",'{$obj[$a]}'":",`{$obj[$a]}`";
      }
    }
    return "$res";
  }

  /**
   *@param string $qry Query string
   *@return PDOStatement Description
   */
  public function transaction($qry) {
    // TODO: implement here
    return $this->db->prepare($qry);
  }
}
