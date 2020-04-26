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
    $this->fn_run_script_auto_opportunity_probability();
  }

  function fn_run_script_auto_opportunity_probability(){

    $this->view_step=true;
    $this->bln_debug_script=false;
    $this->str_date=fn_get_sql_date();

    $str_json=$this->str_data_query;
    $obj_my=json_decode($str_json, false);
    $this->obj_my=$obj_my;

    if(!isset ($obj_my->SchemaName)){
      fn_echo("AUTO PROBABILITY : Provide Schema Nameas Per the Following Example :-<BR><BR>");
      die('{"SchemaName":"data182219"}');
    }
    if($obj_my->SchemaName!==$this->con_schema){
      die("--Error-- Connection SchemaName[$this->con_schema] Should Match Target SchemaName[$obj_my->SchemaName]");
    }

    $str_sql_update="Update Opportunity set Probability=ABS((SELECT CreatedBy FROM LookupTable WHERE Tablename='OpportunityStatus' and TableValue=OpportunityStatus)), Forecast=IF(Probability<50, 'Not Forecast','Forecast') where id={Id}";

    $str_sql="SELECT * FROM `formcontrol` where name=\"Opportunity\" ;";
    $stmt = $this->obj_pdo->pdo->query($str_sql);
    $arr_row=$stmt->fetch(PDO::FETCH_BOTH);

    $str_PostInsertSQL=$arr_row["PostInsertSQL"];
    $str_PostUpdateSQL=$arr_row["PostUpdateSQL"];
    if(!empty($str_PostInsertSQL)){
        fn_echo("PostInsertSQL Exists", $str_PostInsertSQL);
    }
    else {
      $str_sql="UPDATE `formcontrol` SET PostInsertSQL=\"$str_sql_update\" where name=\"Opportunity\" ;";
      $stmt = $this->obj_pdo->pdo->query($str_sql);
    }
    if(!empty($str_PostUpdateSQL)){
        fn_echo("PostUpdateSQL Exists", $str_PostUpdateSQL);
    }
    else {
      $str_sql="UPDATE `formcontrol` SET PostUpdateSQL=\"$str_sql_update\" where name=\"Opportunity\" ;";
      $stmt = $this->obj_pdo->pdo->query($str_sql);
    }
    if(isset ($obj_my->ApplyAuto)){
      fn_echo("ApplyAuto Default Probability");
      $str_sql="UPDATE `$obj_my->SchemaName`.`lookuptable` set createdby=0  where tablename='OpportunityStatus' and tablevalue in('Lost','No Opportunity'); ";
      fn_echo("str_sql", $str_sql);
      $stmt = $this->obj_pdo->pdo->query($str_sql);
      $str_sql="UPDATE `$obj_my->SchemaName`.`lookuptable` set createdby=10  where tablename='OpportunityStatus' and tablevalue in('New Lead'); ";
      fn_echo("str_sql", $str_sql);
      $stmt = $this->obj_pdo->pdo->query($str_sql);
      $str_sql="UPDATE `$obj_my->SchemaName`.`lookuptable` set createdby=25  where tablename='OpportunityStatus' and tablevalue in('Qualified', 'Waiting'); ";
      fn_echo("str_sql", $str_sql);
      $stmt = $this->obj_pdo->pdo->query($str_sql);
      $str_sql="UPDATE `$obj_my->SchemaName`.`lookuptable` set createdby=50  where tablename='OpportunityStatus' and tablevalue='Quoted'; ";
      fn_echo("str_sql", $str_sql);
      $stmt = $this->obj_pdo->pdo->query($str_sql);
      $str_sql="UPDATE `$obj_my->SchemaName`.`lookuptable` set createdby=75  where tablename='OpportunityStatus' and tablevalue='Waiting for PO'; ";
      fn_echo("str_sql", $str_sql);
      $stmt = $this->obj_pdo->pdo->query($str_sql);
      $str_sql="UPDATE `$obj_my->SchemaName`.`lookuptable` set createdby=100  where tablename='OpportunityStatus' and tablevalue='Won'; ";
      fn_echo("str_sql", $str_sql);
      $stmt = $this->obj_pdo->pdo->query($str_sql);
    }

    $str_sql="UPDATE `$obj_my->SchemaName`.`lookuptable` set createdby=0  where createdby>100";
    fn_echo("str_sql", $str_sql);
    $stmt = $this->obj_pdo->pdo->query($str_sql);

    $str_sql="UPDATE `$obj_my->SchemaName`.`datadictionary` set  Mandatory=1 where tablename='Opportunity' and fieldname='OpportunityStatus';";
    fn_echo("str_sql", $str_sql);
    $stmt = $this->obj_pdo->pdo->query($str_sql);

    $str_sql="UPDATE `$obj_my->SchemaName`.`datadictionary` set  writepermission=0 , lookuptable='' where tablename='Opportunity' and fieldname='Probability';";
    fn_echo("str_sql", $str_sql);
    $stmt = $this->obj_pdo->pdo->query($str_sql);

    $str_sql="UPDATE `$obj_my->SchemaName`.`datadictionary` set  writepermission=0 where tablename='Opportunity' and fieldname='forecast';";
    fn_echo("str_sql", $str_sql);
    $stmt = $this->obj_pdo->pdo->query($str_sql);

    $str_sql="UPDATE  `$obj_my->SchemaName`.`Opportunity`SET  Probability=0 WHERE  Probability='' OR Probability IS NULL; ";//Blank Probabiltiy cannot be saved
    fn_echo("str_sql", $str_sql);
    $stmt = $this->obj_pdo->pdo->query($str_sql);

    $str_sql="UPDATE  `$obj_my->SchemaName`.`Opportunity` join Lookuptable on OpportunityStatus=Tablevalue SET  Probability=Lookuptable.CreatedBy where Tablename='OpportunityStatus'; ";
    fn_echo("str_sql", $str_sql);
    $stmt = $this->obj_pdo->pdo->query($str_sql);

    fn_echo("That is all folks");
  }
  function fn_get_rss_schema_name($int_id_customer){
    $str_len=strlen($int_id_customer);
    $str_pad=str_pad($int_id_customer, 6, "0", STR_PAD_LEFT);
    return "data".$str_pad;
  }

  }//END CLASS DATA XADMN




  ?>
