<?php
namespace ns_mycodebuzz\data_xscript;
set_time_limit(500);

class cls_data_xscript extends \phpcrud\Data{
  function __construct() {
    parent::__construct();
    $this->obj_theme=new \phpcrud\ThemeData();
    $this->bln_record_console=false;
  }
  function fn_execute(){
    $this->str_action="run-script";
    parent::fn_execute();
  }
  function fn_run_script(){
    $this->fn_run_script_owner();
  }

  function fn_run_script_owner(){
    $this->bln_debug=false;

    $this->str_id_owner="58479,65396,65489,65493,57252,65474";
    $this->int_id_customer=85575;
    $this->str_id_ignore="188496,187271";
    $this->int_interval=10000;

    $str_sql="SELECT id FROM `control`.`support-check` where int_check=0 and id<>$this->int_id_customer and id not in($this->str_id_ignore) order by id desc;";
    $stmt = $this->obj_pdo->pdo->query($str_sql);
    $row = $stmt->fetch(PDO::FETCH_BOTH);
    if(!$row){
      fn_echo("Zero Rows To Check");
      die();
    }
    $int_global_id_start=$row["id"];

    $str_sql="SELECT FOUND_ROWS();";
    $stmt = $this->obj_pdo->pdo->query($str_sql);
    $row = $stmt->fetch(PDO::FETCH_BOTH);
    $int_global_count=$row[0];

    $int_interval=$this->int_interval;
    $this->int_total_num_customer=0;
    $int_id_end=$int_global_id_start;
    $str_start=fn_get_sql_date();
    for ($x = 0; $x <= 10; $x++) {
      $int_id_start=$int_id_end;
      $int_id_end=$int_id_end-$int_interval;
      if ($this->bln_debug){
      fn_echo("int_id_start: ".$int_id_start);
      fn_echo("int_id_end: ".$int_id_end);
      }
      $str_sql=$this->fn_get_sql($int_id_start, $int_id_end);
      if ($this->bln_debug){fn_echo("str_sql: ".$str_sql);}
      $stmt = $this->obj_pdo->pdo->query($str_sql);
      $this->fn_loop_customer($stmt);
      $stmt=null;
    }
    $str_end=fn_get_sql_date();
    $int_global_id_end=$int_id_end;
    $int_total_id_batch=$int_global_id_start-$int_global_id_end;

    fn_echo("Global Count: ".$int_global_count);
    fn_echo("Global Time Start: ".$str_start);
    fn_echo("Global Time End: ".$str_end);
    fn_echo("Global Id Start: ".$int_global_id_start);
    fn_echo("Global Id End: ".$int_global_id_end);
    fn_echo("Total Id Batch: ".$int_total_id_batch);
    fn_echo("Total Num Customer Batch: ".    $this->int_total_num_customer);
  }
  function  fn_get_sql($int_id_start, $int_id_end){
    $str_sql="SELECT id, companyname, int_check FROM `control`.`support-check` where int_check=0 and (id>=".$int_id_end." and id<=".$int_id_start.")  order by id desc;";
    return $str_sql;
  }
  function fn_loop_customer($stmt){
    $this->int_column_count=$stmt->columnCount();
    while($row=$stmt->fetch(PDO::FETCH_BOTH)){
      $this->fn_action_customer($row);
    }
  }
  function fn_action_customer($row){
    $this->int_total_num_customer++;
    $int_id_customer=$row["id"];
    $int_status=0;
    if ($this->bln_debug){
      fn_echo("int_id_customer", $int_id_customer);
    }
    $companyname=$row["companyname"];
    $str_name_schema=$this->fn_get_rss_schema_name($int_id_customer);
    $bln_exist=$this->fn_schema_exists($str_name_schema);
    $int_status=1;
    if($bln_exist){
      $str_sql="SELECT ownerid FROM `".$str_name_schema."`.`account` where ownerid in($this->str_id_owner);";
      $stmt = $this->obj_pdo->pdo->query($str_sql);
      $row = $stmt->fetch(PDO::FETCH_NUM);
      if($row){
        fn_echo_highlight("int_id_customer", $int_id_customer);
        fn_echo("companyname", $companyname);
        fn_echo("str_sql", $str_sql);
        $int_status=2;
      }
    }
    $str_sql_update="UPDATE `control`.`support-check` SET  int_check=".$int_status." where id=".$int_id_customer.";";
    if ($this->bln_debug){fn_echo("str_sql_update", $str_sql_update);}
    $this->obj_pdo->pdo->query($str_sql_update);
  }
  function fn_get_rss_schema_name($int_id_customer){
    $str_len=strlen($int_id_customer);
    $str_pad=str_pad($int_id_customer, 6, "0", STR_PAD_LEFT);
    return "data".$str_pad;
  }
  function fn_schema_exists($str_name_schema) {
    $stmt = $this->obj_pdo->pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$str_name_schema'");
    return (bool) $stmt->fetchColumn();
}



}


?>
