<?php
namespace DataXscript;
set_time_limit(500);

class DataXscript extends \phpcrud\Data{
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
    $this->bln_debug=true;

    $this->str_id_owner="70772,70770,70793, 70824, 70830, 70831";
    $this->int_id_customer=193202;
    $this->str_id_ignore="200430, 200429";
    $this->int_interval=100000;

    $str_sql="SELECT id FROM `control`.`support-check` where int_check=2 and id not in($this->str_id_ignore) order by id desc;";
    $this->fn_echo($str_sql);

    $stmt = $this->obj_pdo->pdo->query($str_sql);
    $row = $stmt->fetch(\PDO::FETCH_BOTH);
    if(!$row){
      $this->fn_echo("Zero Rows To Check");
      $this->fn_echo($str_sql);
      die();
    }



    $int_global_id_start=$row["id"];

    $str_sql="SELECT FOUND_ROWS();";
    $this->fn_echo($str_sql);

    $stmt = $this->obj_pdo->pdo->query($str_sql);
    $row = $stmt->fetch(\PDO::FETCH_BOTH);
    $int_global_count=$row[0];

    $this->fn_echo("int_global_count: ".$int_global_count);


    $int_interval=$this->int_interval;
    $this->int_total_num_customer=0;
    $int_id_end=$int_global_id_start;
    $str_start=$this->fn_get_sql_date();
    for ($x = 0; $x <= 10; $x++) {
      $int_id_start=$int_id_end;
      $int_id_end=$int_id_end-$int_interval;
      if ($this->bln_debug){
      $this->fn_echo("int_id_start: ".$int_id_start);
      $this->fn_echo("int_id_end: ".$int_id_end);
      }
      $str_sql=$this->fn_get_sql($int_id_start, $int_id_end);
      if ($this->bln_debug){$this->fn_echo("str_sql: ".$str_sql);}
      $stmt = $this->obj_pdo->pdo->query($str_sql);
      $this->fn_loop_customer($stmt);
      $stmt=null;
    }



    $str_end=$this->fn_get_sql_date();
    $int_global_id_end=$int_id_end;
    $int_total_id_batch=$int_global_id_start-$int_global_id_end;

    $this->fn_echo("Global Count: ".$int_global_count);
    $this->fn_echo("Global Time Start: ".$str_start);
    $this->fn_echo("Global Time End: ".$str_end);
    $this->fn_echo("Global Id Start: ".$int_global_id_start);
    $this->fn_echo("Global Id End: ".$int_global_id_end);
    $this->fn_echo("Total Id Batch: ".$int_total_id_batch);
    $this->fn_echo("Total Num Customer Batch: ".    $this->int_total_num_customer);
  }
  function  fn_get_sql($int_id_start, $int_id_end){
    $str_sql="SELECT id, companyname, int_check FROM `control`.`support-check` where int_check=2 and (id>=".$int_id_end." and id<=".$int_id_start.")  and id not in($this->str_id_ignore) order by id desc;";
    return $str_sql;
  }
  function fn_loop_customer($stmt){
    $this->int_column_count=$stmt->columnCount();
    while($row=$stmt->fetch(\PDO::FETCH_BOTH)){
      $this->fn_action_customer($row);
    }
  }
  function fn_action_customer($row){
    $this->int_total_num_customer++;
    $int_id_customer=$row["id"];
    $int_status=0;
    if ($this->bln_debug){
      $this->fn_echo("int_id_customer", $int_id_customer);
    }
    $companyname=$row["companyname"];
    $str_name_schema=$this->fn_get_rss_schema_name($int_id_customer);
    $bln_exist=$this->fn_schema_exists($str_name_schema);
    $int_status=1;
    if($bln_exist){

      $this->fn_echo_highlight("int_id_customer", $int_id_customer);
      $this->fn_echo("companyname", $companyname);


      $int_status=2;

      $str_sql_update="UPDATE  `".$str_name_schema."`.datadictionary SET Live=1 where tablename ='campaign' and fieldname='Name' and !live;";
      if ($this->bln_debug){$this->fn_echo("str_sql_update", $str_sql_update);}
      $this->obj_pdo->pdo->query($str_sql_update);

      $str_sql_update="UPDATE  `".$str_name_schema."`.datadictionary SET Live=1 where tablename ='campaignstage' and fieldname='Name' and !live;";
      if ($this->bln_debug){$this->fn_echo("str_sql_update", $str_sql_update);}
      $this->obj_pdo->pdo->query($str_sql_update);

    }

    /*
    $str_sql_update="UPDATE `control`.`support-check` SET  int_check=".$int_status." where id=".$int_id_customer.";";
    if ($this->bln_debug){$this->fn_echo("str_sql_update", $str_sql_update);}
    $this->obj_pdo->pdo->query($str_sql_update);
    //*/

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


  }//END CLASS DATA XADMN




  ?>
