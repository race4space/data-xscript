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
    $this->fn_run_script_ownerid();
  }

  function fn_run_script_ownerid(){

    $this->view_step=true;
    $this->bln_debug_script=true;
    $this->str_date=$this->fn_get_sql_date();

    $str_json=$this->str_data_query;
    $obj_my=json_decode($str_json, false);
    $this->obj_my=$obj_my;

    if(!isset ($obj_my->SchemaName) OR !isset ($obj_my->ObjectName)){
      $this->fn_echo("Provide Schema Nameas Per the Following Example :-<BR><BR>");
      die('{"SchemaName":"data182219", "ObjectName":"Opportunity"}');
    }
    if($obj_my->SchemaName!==$this->con_schema){
      die("--Error-- Connection SchemaName[$this->con_schema] Should Match Target SchemaName[$obj_my->SchemaName]");
    }

    $str_name_schema=$obj_my->SchemaName;
    $str_name_object=$obj_my->ObjectName;
    $this->fn_initialize($str_name_schema, $str_name_object);

    $this->fn_create_data_table();
    $this->fn_create_record_datadictionary();
    $this->fn_create_record_form();
    $this->fn_affect_object_permission_ownerid();
    $this->fn_affect_grid_control();
    $this->fn_update_existing_data();

    /*
    1. Create Column  Owner
    2. Add to DD , driven by user list, default logged in user, blank but mandatory
    3. Add to Form
    4. Alter Object PermissionOwnerId
    5. Alter GridControl
    6 .Update existing opps to acocunt owner.
    //*/
    $this->fn_echo("Script completed");
  }
  function fn_get_rss_schema_name($int_id_customer){
    $str_len=strlen($int_id_customer);
    $str_pad=str_pad($int_id_customer, 6, "0", STR_PAD_LEFT);
    return "data".$str_pad;
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

    $this->str_sql="SELECT `ObjectNames` FROM `$str_name_schema`.`object` where name=\"$str_name_object\";";
    $str_value=$this->fn_fetch_column();
    if(empty($str_value)){
      die("Object $str_name_object Not Exist");
    }
    $this->str_names_object=$str_value;

    $this->str_sql="SELECT `ParentObject` FROM `$str_name_schema`.`object` where name=\"$str_name_object\";";
    $str_value=$this->fn_fetch_column();
    if(empty($str_value)){
      die("Object $str_name_object Not Exist");
    }
    $this->str_name_parentobject=$str_value;

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
    $str_separator=", ";

    $s="";
    $str_list_column="";
    $str_name_field_current="OwnerId";
    $str_list_column.="'$str_name_field_current'$str_separator";
    $this->str_sql="SHOW COLUMNS FROM `$str_name_schema`.`$str_name_table` LIKE 'OwnerId';";
    if($this->bln_debug_script){$this->fn_echo("this->str_sql", $this->str_sql);}
    $str_exist=$this->fn_fetch_column();
    if(!empty($str_exist)){
        $s.="DROP COLUMN `$str_name_field_current`$str_separator";
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


    $s="";
    $s.="ALTER TABLE `$str_name_schema`.`$str_name_table` ";
    $s.="ADD COLUMN `$str_name_field_current` int(10) unsigned DEFAULT '0' AFTER `AccountId`$str_separator";
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
    $str_separator=", ";

    $this->str_sql="DELETE FROM $str_name_schema.datadictionary where TABLENAME='$this->str_name_object' AND FIELDNAME IN('OwnerId');";
    if($this->bln_debug_script){
      $this->fn_echo("this->str_sql", $this->str_sql);
    }
    $this->fn_record_action();



    $obj_ini = new \stdClass();
    $obj_ini->str_TableName=$this->str_name_object;
    $obj_ini->str_FieldOrder="26";
    $obj_ini->str_FieldName="OwnerId";
    $obj_ini->str_FieldDescription="Owner";
    $obj_ini->str_FieldType="number";
    $obj_ini->int_Live=1;
    $obj_ini->str_LookupTable="User.Initials";
    $obj_ini->str_FieldDefault="UserInitials";

    $this->fn_insert_record_datadictionary($obj_ini);
  }

  function fn_insert_record_datadictionary($obj_ini){

    $obj_my=$this->obj_my;
    $str_name_schema=$this->str_name_schema;
    $str_name_table="datadictionary";

    if(!isset($obj_ini->str_TableName)){die("Datadictionary TableName required");}
    if(!isset($obj_ini->str_FieldOrder)){$obj_ini->str_FieldOrder="100";}
    if(!isset($obj_ini->str_FieldName)){die("Datadictionary FieldName required");}
    if(!isset($obj_ini->str_FieldShortDescription)){$obj_ini->str_FieldShortDescription=$obj_ini->str_FieldName;}
    if(!isset($obj_ini->str_FieldType)){$obj_ini->str_FieldType="text";}
    if(!isset($obj_ini->str_FieldDescription)){$obj_ini->str_FieldDescription=$obj_ini->str_FieldName;}
    if(!isset($obj_ini->int_Live)){$obj_ini->int_Live=1;}
    if(!isset($obj_ini->str_LookupTable)){$obj_ini->str_LookupTable="";}
    if(!isset($obj_ini->str_CustomField)){$obj_ini->str_CustomField=0;}
    if(!isset($obj_ini->str_LinkPage)){$obj_ini->str_LinkPage="";}
    if(!isset($obj_ini->str_LinkId)){$obj_ini->str_LinkId="";}
    if(!isset($obj_ini->str_FieldDefault)){$obj_ini->str_FieldDefault="";}
    if(!isset($obj_ini->int_Mandatory)){$obj_ini->int_Mandatory=0;}


    $this->str_sql=<<<heredoc
    INSERT INTO $str_name_schema.$str_name_table
      (
      `TableName`,
      `FieldOrder`,
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
      `FieldDefault`,
      `Mandatory`,
      `ModifiedDate`,
      `ModifiedBy`,
      `CreatedDate`,
      `CreatedBy`
      )
    VALUES
    (
      '$obj_ini->str_TableName',
      '$obj_ini->str_FieldOrder',
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
      '$obj_ini->str_FieldDefault',
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
    $str_separator=", ";

    $this->str_sql="DELETE FROM $str_name_schema.form where NAME='$this->str_name_object_lower' AND RECORDNAME IN('OwnerId');";
    if($this->bln_debug_script){
      $this->fn_echo("this->str_sql", $this->str_sql);
    }
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $obj_ini->str_Name=$this->str_name_object_lower;
    $obj_ini->str_Tab=3000;
    $obj_ini->str_RecordName="OwnerId";
    $obj_ini->int_Mandatory=1;
    $obj_ini->int_ButtonType=0;
    $this->fn_insert_record_form($obj_ini);
  }

  function fn_insert_record_form($obj_ini){

    $obj_my=$this->obj_my;
    $str_name_schema=$this->str_name_schema;
    $str_name_table="form";

    if(!isset($obj_ini->str_Name)){die("Form Name required");}
    if(!isset($obj_ini->str_Tab)){die("Form Tab required");}
    if(!isset($obj_ini->str_RecordName)){die("Form RecordName required");}
    if(!isset($obj_ini->int_Mandatory)){$obj_ini->int_Mandatory=0;}
    if(!isset($obj_ini->int_ButtonType)){$obj_ini->int_ButtonType=0;}

    $this->str_sql=<<<heredoc
    INSERT INTO $str_name_schema.$str_name_table
      (
      `Name`,
      `Tab`,
      `RecordName`,
      `Mandatory`,
      `Title`,
      `ButtonType`,
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
      '$obj_ini->int_Mandatory',
      '',
      '$obj_ini->int_ButtonType',
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

  function fn_affect_object_permission_ownerid(){

    $obj_my=$this->obj_my;
    $str_name_schema=$this->str_name_schema;

    $this->str_sql="UPDATE  $str_name_schema.object set PermissionOwnerId=\"$this->str_name_table.OwnerId\" where NAME=\"$this->str_name_object\";";
    if($this->bln_debug_script){
      $this->fn_echo("this->str_sql", $this->str_sql);
    }
    $this->fn_record_action();
  }
  function fn_affect_grid_control(){
    $obj_my=$this->obj_my;
    $str_name_schema=$this->str_name_schema;

    $this->str_sql="UPDATE  $str_name_schema.gridcontrol set SQLFROM=\"$this->str_name_table LEFT JOIN Account ON $this->str_name_table.AccountId=Account.Id LEFT JOIN User ON $this->str_name_table.OwnerId=User.Id\" where NAME=\"$this->str_names_object\";";
    if($this->bln_debug_script){
      $this->fn_echo("this->str_sql", $this->str_sql);
    }
    $this->fn_record_action();

    $this->str_sql="UPDATE  $str_name_schema.gridcontrol set SQLFROM=\"$this->str_name_table INNER JOIN Account ON $this->str_name_table.AccountId=Account.Id LEFT JOIN User ON $this->str_name_table.OwnerId=User.Id\" where NAME=\"$this->str_name_parentobject$this->str_names_object\";";
    if($this->bln_debug_script){
      $this->fn_echo("this->str_sql", $this->str_sql);
    }
    $this->fn_record_action();
  }
  function fn_update_existing_data(){
    $obj_my=$this->obj_my;
    $str_name_schema=$this->str_name_schema;

    $this->str_sql="UPDATE  $str_name_schema.$this->str_name_table join $str_name_schema.account on Accountid=Account.Id set $this->str_name_table.OwnerId=Account.OwnerId;";
    if($this->bln_debug_script){
      $this->fn_echo("this->str_sql", $this->str_sql);
    }
    $this->fn_record_action();
  }
}


?>
