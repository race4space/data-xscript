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
    $this->fn_run_script_create_new_customfields();
  }

  function fn_run_script_create_new_customfields(){

    $this->view_step=true;
    $this->bln_debug_script=false;
    $this->str_date=$this->fn_get_sql_date();

    $str_json=$this->str_data_query;
    $obj_my=json_decode($str_json, false);
    $this->obj_my=$obj_my;

    if(!isset ($obj_my->SchemaName) OR !isset ($obj_my->ObjectName)){
      $this->fn_echo("CREATE NEW CUSTOM FIELDS : Provide Schema Name, Object Name as Per the Following Example :-<BR><BR>");
      die('{"SchemaName":"data182219","ObjectName":"Contact"}');
    }
    if($obj_my->SchemaName!==$this->con_schema){
      die("--Error-- Connection SchemaName[$this->con_schema] Should Match Target SchemaName[$obj_my->SchemaName]");
    }

    $obj_my->HowMany=5;
    //*
    $this->fn_echo("SchemaName", $obj_my->SchemaName);
    $this->fn_echo("ObjectName", $obj_my->ObjectName);
    $this->fn_echo("<br>");
    //*/

    $str_name_schema=$obj_my->SchemaName;
    $str_name_object=$obj_my->ObjectName;
    $this->fn_initialize($str_name_schema, $str_name_object);
    //die();

    $this->fn_create_data_table();
    $this->fn_create_record_datadictionary();
    $this->fn_create_record_form();

    switch($this->str_name_object_lower){
      case "task":
      $this->fn_initialize($str_name_schema, "Activity");
      $this->fn_create_record_datadictionary();
      $this->fn_create_record_form();
      break;
      case "activity":
      $this->fn_initialize($str_name_schema, "Task");
      $this->fn_create_record_datadictionary();
      $this->fn_create_record_form();
      break;
    }


    //*/
    $this->fn_echo("Script completed");
  }
  function fn_initialize($str_name_schema, $str_name_object){

    //Check SQL Schema Exists
    $str_exist=$this->fn_get_schema_exist($str_name_schema);
    if(empty($str_exist)){
      die("SchemaName $str_name_schema Not Exist");
    }
    $this->str_name_schema=$str_name_schema;

    //Check RSS Object Exists
    $this->str_sql="SELECT `NAME` FROM `$str_name_schema`.`object` where name=\"$str_name_object\";";
    $str_exist=$this->fn_fetch_column();
    if(empty($str_exist)){
      die("Object $str_name_object Not Exist");
    }
    $this->str_name_object=$str_name_object;
    $this->str_name_object_lower=strtolower($this->str_name_object);

    //Check SQL Table Exists
    $this->str_sql="SELECT `Table` FROM `$str_name_schema`.`object` where name=\"$this->str_name_object\";";
    $str_name_table=$this->fn_fetch_column();
    $str_exist=$this->fn_get_table_exist($str_name_schema, $str_name_table);
    if(empty($str_exist)){
      die("Tablename $str_name_table Not Exist");
    }
    $this->str_name_table=$str_name_table;
    $this->str_name_table_lower=strtolower($this->str_name_table);

    $this->fn_echo("Qualified SchemaName", $this->str_name_schema);
    $this->fn_echo("Qualified ObjectName", $this->str_name_object);
    $this->fn_echo("Qualified Table", $this->str_name_table);
    $this->fn_echo("<br>");
  }

  function fn_create_data_table(){
    $obj_my=$this->obj_my;
    $str_name_schema=$this->str_name_schema;
    $str_name_table=$this->str_name_table;
    $int_how_many=$obj_my->HowMany;
    $str_separator=", ";

    if(isset ($obj_my->DropAndInsertAfterExistFieldNumber)){
        $this->int_drop_and_insert_after=$obj_my->DropAndInsertAfterExistFieldNumber;
    }
    if(empty($int_drop_and_insert_after)){
      $this->str_sql="SELECT FieldName FROM `$str_name_schema`.`datadictionary` where tablename=\"$str_name_table\" and customfield order by fieldname desc LIMIT 1;";
      //$this->fn_echo("this->str_sql", $this->str_sql);
      $str_after=$this->fn_fetch_column();
      $this->int_drop_and_insert_after=$this->fn_replace("Custom", "", $str_after);
    }
    if(empty($this->int_drop_and_insert_after)){
      die("Drop And Insert After Not Set (Must Have One Exisitng Custom Field)");
    }
    if(empty($int_how_many)){
      die("Insert How Many Not Set");
    }

    $int_after=$this->int_drop_and_insert_after;
    $int_current=$int_after+1;
    $int_after=str_pad($int_after, 2, "0", STR_PAD_LEFT);
    $str_name_field_after="Custom$int_after";
    $str_name_field_current="Custom$int_current";

    $this->fn_echo("Insert Number", $obj_my->HowMany);
    $this->fn_echo("Insert After", $str_name_field_after);
    $this->fn_echo("Starting With", $str_name_field_current);
    $this->fn_echo("<br>");

    $s="";
    $str_list_column="";
    for ($i = 0; $i < $int_how_many; $i++) {

      $int_current=str_pad($int_current, 2, "0", STR_PAD_LEFT);
      $str_name_field_current="Custom$int_current";
      $str_list_column.="'$str_name_field_current'$str_separator";

      $this->str_sql="SHOW COLUMNS FROM `$str_name_schema`.`$str_name_table` LIKE '$str_name_field_current';";
      if($this->bln_debug_script){$this->fn_echo("this->str_sql", $this->str_sql);}
      $str_exist=$this->fn_fetch_column();
      if(!empty($str_exist)){
          $s.="DROP COLUMN `$str_name_field_current`$str_separator";
      }
      $int_current++;
    }
    $s=trim($s, $str_separator);
    $this->str_list_column=trim($str_list_column, $str_separator);

    if(!empty($s)){
        $s.=";";
        $str_drop=$s;
        $s="ALTER TABLE `$str_name_schema`.`$str_name_table` ";
        $s.=$str_drop;
        $this->str_sql=$s;
        if($this->bln_debug_script){
          $this->fn_echo("this->str_sql", $this->str_sql);
        }
        $this->fn_record_action();
    }

    $int_after=$this->int_drop_and_insert_after;
    $int_current=$int_after+1;
    $s="";
    $s.="ALTER TABLE `$str_name_schema`.`$str_name_table` ";
    for ($i = 0; $i < $int_how_many; $i++) {
      $int_after=str_pad($int_after, 2, "0", STR_PAD_LEFT);
      $int_current=str_pad($int_current, 2, "0", STR_PAD_LEFT);
      $str_name_field_after="Custom$int_after";
      $str_name_field_current="Custom$int_current";
      $s.="ADD COLUMN `$str_name_field_current` LONGTEXT NULL DEFAULT NULL AFTER `$str_name_field_after`$str_separator";

      $int_after++;
      $int_current++;

    }
    $s=trim($s, $str_separator);
    $s.=";";
    $this->str_sql=$s;
    if($this->bln_debug_script){
      $this->fn_echo("this->str_sql", $this->str_sql);
    }
    $this->fn_record_action();
  }


  function fn_create_record_datadictionary(){
    $obj_my=$this->obj_my;
    $str_name_schema=$this->str_name_schema;

    $int_how_many=$obj_my->HowMany;
    $int_after=$this->int_drop_and_insert_after;
    $int_current=$int_after+1;
    $str_separator=", ";

    if(!empty($this->str_list_column)){
      $this->str_sql="DELETE FROM $str_name_schema.datadictionary where TABLENAME='$this->str_name_object' AND CustomField AND FIELDNAME IN($this->str_list_column);";
      if($this->bln_debug_script){
        $this->fn_echo("this->str_sql", $this->str_sql);
      }
      $this->fn_record_action();
    }



    for ($i = 0; $i < $int_how_many; $i++) {

      $int_current=str_pad($int_current, 2, "0", STR_PAD_LEFT);
      $str_name_field_current="Custom$int_current";

      $obj_ini = new \stdClass();
      $obj_ini->str_TableName=$this->str_name_object;
      $obj_ini->str_CustomField=1;

      $obj_ini->str_FieldName=$str_name_field_current;
      $obj_ini->int_Live=0;
      $this->fn_insert_record_datadictionary($obj_ini);

      $int_current++;
    }
  }

  function fn_insert_record_datadictionary($obj_ini){

    $obj_my=$this->obj_my;
    $str_name_schema=$this->str_name_schema;
    $str_name_table="datadictionary";

    if(!isset($obj_ini->str_TableName)){die("Datadictionary TableName required");}
    if(!isset($obj_ini->str_FieldName)){die("Datadictionary FieldName required");}
    if(!isset($obj_ini->str_FieldShortDescription)){$obj_ini->str_FieldShortDescription=$obj_ini->str_FieldName;}
    if(!isset($obj_ini->str_FieldType)){$obj_ini->str_FieldType="text";}
    if(!isset($obj_ini->str_FieldDescription)){$obj_ini->str_FieldDescription=$obj_ini->str_FieldName;}
    if(!isset($obj_ini->int_Live)){$obj_ini->int_Live=1;}
    if(!isset($obj_ini->str_LookupTable)){$obj_ini->str_LookupTable="";}
    if(!isset($obj_ini->str_CustomField)){$obj_ini->str_CustomField=0;}
    if(!isset($obj_ini->str_LinkPage)){$obj_ini->str_LinkPage="";}
    if(!isset($obj_ini->str_LinkId)){$obj_ini->str_LinkId="";}
    if(!isset($obj_ini->int_Mandatory)){$obj_ini->int_Mandatory=0;}


    $this->str_sql=<<<heredoc
    INSERT INTO $str_name_schema.$str_name_table
      (
      `TableName`,
      `FieldName`,
      `FieldShortDescription`,
      `FieldType`,
      `FieldDescription`,
      `Live`,
      `DisplayGrid`,
      `Reportable`,
      `api_reportable`,
      `LookupTable`,
      `CustomField`,
      `LinkPage`,
      `LinkId`,
      `Mandatory`,
      `ModifiedDate`,
      `ModifiedBy`,
      `CreatedDate`,
      `CreatedBy`
      )
    VALUES
    (
      '$obj_ini->str_TableName',
      '$obj_ini->str_FieldName',
      '$obj_ini->str_FieldShortDescription',
      '$obj_ini->str_FieldType',
      '$obj_ini->str_FieldDescription',
      '$obj_ini->int_Live',
      '1',
      '1',
      '1',
      '$obj_ini->str_LookupTable',
      '$obj_ini->str_CustomField',
      '$obj_ini->str_LinkPage',
      '$obj_ini->str_LinkId',
      '$obj_ini->int_Mandatory',
      '$this->str_date',
      '1',
      '$this->str_date',
      '1'
    );
heredoc;

    $this->fn_record_action();
    if($this->view_step){
      $int_id_record=$this->fn_get_last_insert_id();
      //$this->fn_view_script_record($str_name_table, $int_id_record);
    }
  }

  function fn_create_record_form(){

    $obj_my=$this->obj_my;
    $str_name_schema=$this->str_name_schema;
    $int_how_many=$obj_my->HowMany;
    $int_after=$this->int_drop_and_insert_after;
    $int_current=$int_after+1;
    $str_separator=", ";

    if(!empty($this->str_list_column)){
      $this->str_sql="DELETE FROM $str_name_schema.form where NAME='$this->str_name_object_lower' AND RECORDNAME IN($this->str_list_column);";
      if($this->bln_debug_script){
        $this->fn_echo("this->str_sql", $this->str_sql);
      }
      $this->fn_record_action();
    }

    for ($i = 0; $i < $int_how_many; $i++) {

      $int_current=str_pad($int_current, 2, "0", STR_PAD_LEFT);
      $str_name_field_current="Custom$int_current";

      $obj_ini = new \stdClass();
      $obj_ini->str_Name=$this->str_name_object_lower;
      $obj_ini->str_RecordName=$str_name_field_current;
      $obj_ini->str_Tab=3000;
      $this->fn_insert_record_form($obj_ini);

      $int_current++;

    }
  }

  function fn_insert_record_form($obj_ini){

    $obj_my=$this->obj_my;
    $str_name_schema=$this->str_name_schema;
    $str_name_table="form";

    if(!isset($obj_ini->str_Name)){die("Form Name required");}
    if(!isset($obj_ini->str_Tab)){die("Form Tab required");}
    if(!isset($obj_ini->str_RecordName)){die("Form RecordName required");}


    $this->str_sql=<<<heredoc
    INSERT INTO $str_name_schema.$str_name_table
      (
      `Name`,
      `Tab`,
      `RecordName`,
      `Title`,
      `ModifiedDate`,
      `ModifiedBy`,
      `CreatedDate`,
      `CreatedBy`
      )
    VALUES
    (
      '$obj_ini->str_Name',
      '$obj_ini->str_Tab',
      '$obj_ini->str_RecordName',
      '',
      '$this->str_date',
      '1',
      '$this->str_date',
      '1'
    );
heredoc;
    $this->fn_record_action();
    if($this->view_step){
      $int_id_record=$this->fn_get_last_insert_id();
      $this->fn_view_script_record($str_name_table, $int_id_record);
    }
  }
}

?>
