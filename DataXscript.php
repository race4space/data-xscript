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
    $this->fn_run_script_create_new_object();
  }

  function fn_run_script_create_new_object(){

    $this->view_step=false;

    $str_json=$this->str_data_query;
    $obj_clone=json_decode($str_json, false);
    $this->obj_clone=$obj_clone;
    if(!isset ($obj_clone->SchemaName) OR !isset ($obj_clone->ObjectName) OR !isset ($obj_clone->ObjectNames) OR !isset ($obj_clone->ParentObjectName)){
      $this->fn_echo("CREATE OBJECT : Provide Schema Name, Object Name, Object Names, ParentObjectName as Per the Following Example :-<BR><BR>");
      die('{"SchemaName":"data182219","ObjectName":"Finance","ObjectNames":"Finances","ParentObjectName":"Account"}');
    }
    else{
    $this->fn_echo("SchemaName", $obj_clone->SchemaName);
    $this->fn_echo("ObjectName", $obj_clone->ObjectName);
    $this->fn_echo("ObjectNames", $obj_clone->ObjectNames);
    $this->fn_echo("ParentObjectName", $obj_clone->ParentObjectName);
    }
    $this->fn_echo("<br>");

    if($obj_clone->SchemaName!==$this->con_schema){
      die("--Error-- Connection SchemaName[$this->con_schema] Should Match Target SchemaName[$obj_clone->SchemaName]");
    }

    $obj_clone->ObjectNameLower=strtolower($obj_clone->ObjectName);
    $str_name_schema=$obj_clone->SchemaName;

    $this->str_date=$this->fn_get_sql_date();

    //*
    $this->fn_create_data_table();
    $this->fn_create_record_object();
    $this->fn_create_record_datadictionary();
    $this->fn_create_record_page();
    $this->fn_create_record_form();
    $this->fn_create_record_form_control();
    $this->fn_create_record_grid();
    $this->fn_create_record_grid_control();
    $this->fn_create_record_menutab();
    //*/
    $this->fn_echo("RSS Object Creation completed");


  }

  function fn_create_data_table(){
    $obj_clone=$this->obj_clone;
    $str_name_table=$obj_clone->ObjectNameLower;
    $str_name_schema=$obj_clone->SchemaName;

    $this->str_sql="DROP TABLE IF EXISTS `$str_name_schema`.`$str_name_table`;";
    $this->fn_record_action();

    $this->str_sql=<<<heredoc
    CREATE TABLE `$str_name_schema`.`$str_name_table` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `AccountId` int(10) unsigned DEFAULT '0',
    `Name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `Notes` longtext COLLATE utf8mb4_unicode_ci,
    `Custom01` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom02` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom03` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom04` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom05` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom06` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom07` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom08` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom09` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom10` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom11` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom12` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom13` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom14` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom15` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom16` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom17` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom18` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom19` mediumtext COLLATE utf8mb4_unicode_ci,
    `Custom20` mediumtext COLLATE utf8mb4_unicode_ci,
    `ModifiedDate` datetime DEFAULT NULL,
    `ModifiedBy` int(10) unsigned DEFAULT '0',
    `CreatedDate` datetime DEFAULT NULL,
    `CreatedBy` int(10) unsigned DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `AccountId` (`AccountId`)
  ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
heredoc;
    $this->fn_record_action();
    if($this->view_step){
      $this->str_sql="SELECT * FROM $str_name_schema.$str_name_table;";
      $this->fn_records_view();
    }
  }
  function fn_create_record_object(){
    $obj_clone=$this->obj_clone;
    $str_name_table="object";
    $str_name_schema=$obj_clone->SchemaName;

    $this->str_sql="DELETE FROM $str_name_schema.$str_name_table where NAME='$obj_clone->ObjectName';";
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $this->fn_insert_table_object($obj_ini);
  }
  function fn_insert_table_object($obj_ini){
    $obj_clone=$this->obj_clone;
    $str_name_table="object";
    $str_name_schema=$obj_clone->SchemaName;

    $this->str_sql=<<<heredoc
    INSERT INTO $str_name_schema.$str_name_table
    (
      `Name`,
      `ObjectName`,
      `ObjectNames`,
      `Table`,
      `ParentObject`,
      `ParentLinkField`,
      `Page`,
      `PermissionOwnerId`,
      `Exportable`,
      `ModifiedDate`,
      `ModifiedBy`,
      `CreatedDate`,
      `CreatedBy`)
    VALUES
      (
      '$obj_clone->ObjectName',
      '$obj_clone->ObjectName',
      '$obj_clone->ObjectNames',
      '$obj_clone->ObjectName',
      '$obj_clone->ParentObjectName',
      '{$obj_clone->ParentObjectName}Id',
      'page.asp?p=$obj_clone->ObjectNameLower&id=id',
      '$obj_clone->ParentObjectName.OwnerId',
      '1',
      '$this->str_date',
      '1',
      '$this->str_date',
      '1'
    );
heredoc;
    $this->fn_record_action();
    if($this->view_step){
      $int_id_record=$this->fn_get_last_insert_id();
      $this->fn_view_cloned_record($str_name_table, $int_id_record);
    }
  }

  function fn_create_record_datadictionary(){
    $obj_clone=$this->obj_clone;

    $this->str_sql="DELETE FROM $obj_clone->SchemaName.datadictionary where TABLENAME='$obj_clone->ObjectName';";
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $obj_ini->str_TableName=$obj_clone->ObjectName;
    $obj_ini->str_FieldName="Id";
    $obj_ini->str_FieldType="number";
    $this->fn_insert_record_datadictionary($obj_ini);

    $obj_ini = new \stdClass();
    $obj_ini->str_TableName=$obj_clone->ObjectName;
    $obj_ini->str_FieldName="Name";
    $obj_ini->str_LinkPage="page.asp?p=$obj_clone->ObjectNameLower&id={Id}";
    $obj_ini->str_LinkId="$obj_clone->ObjectNameLower.id";
    $obj_ini->int_Mandatory=1;
    $this->fn_insert_record_datadictionary($obj_ini);

    $obj_ini = new \stdClass();
    $obj_ini->str_TableName=$obj_clone->ObjectName;
    $obj_ini->str_FieldName="Notes";
    $obj_ini->str_FieldType="memo";
    $this->fn_insert_record_datadictionary($obj_ini);

    for ($i = 1; $i <= 20; $i++) {
      $obj_ini = new \stdClass();
      $obj_ini->str_TableName=$obj_clone->ObjectName;
      $obj_ini->str_CustomField=1;
      $str_pad=str_pad($i, 2, "0", STR_PAD_LEFT);
      $obj_ini->str_FieldName="Custom".$str_pad;
      $obj_ini->int_Live=0;
      $this->fn_insert_record_datadictionary($obj_ini);
    }
  }

  function fn_insert_record_datadictionary($obj_ini){

    $obj_clone=$this->obj_clone;
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
    INSERT INTO $obj_clone->SchemaName.$str_name_table
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
      $this->fn_view_cloned_record($str_name_table, $int_id_record);
    }
  }

  function fn_create_record_page(){
    $obj_clone=$this->obj_clone;

    $this->str_sql="DELETE FROM $obj_clone->SchemaName.page where NAME='$obj_clone->ObjectNames';";
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $obj_ini->str_Name=$obj_clone->ObjectNames;
    $int_id_record=$this->fn_insert_record_page($obj_ini);

    $obj_ini = new \stdClass();
    $obj_ini->int_PageId=$int_id_record;
    $obj_ini->str_PageDetailType="grid";
    $obj_ini->str_Name=$obj_clone->ObjectNames;
    $this->fn_create_record_page_detail($obj_ini);

    $this->str_sql="DELETE FROM $obj_clone->SchemaName.page where NAME='$obj_clone->ObjectName';";
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $obj_ini->str_Name=$obj_clone->ObjectName;
    $obj_ini->str_PostEditAction=1;
    $int_id_record=$this->fn_insert_record_page($obj_ini);

    $obj_ini = new \stdClass();
    $obj_ini->int_PageId=$int_id_record;
    $obj_ini->str_PageDetailType="form";
    $obj_ini->str_Name=$obj_clone->ObjectName;
    $this->fn_create_record_page_detail($obj_ini);

    $this->str_sql="SELECT Id FROM PAGE WHERE NAME='$obj_clone->ParentObjectName';";
    $int_id_record=$this->fn_fetch_column();

    $obj_ini = new \stdClass();
    $obj_ini->int_PageId=$int_id_record;
    $obj_ini->int_ObjectOrder=1;
    $obj_ini->str_PageDetailType="grid";
    $obj_ini->str_Name="$obj_clone->ParentObjectName$obj_clone->ObjectNames";
    $this->fn_create_record_page_detail($obj_ini);
  }

  function fn_insert_record_page($obj_ini){

    $obj_clone=$this->obj_clone;
    $str_name_table="page";

    if(!isset($obj_ini->str_Name)){die("Page Name required");}
    if(!isset($obj_ini->str_PostEditAction)){$obj_ini->str_PostEditAction=0;}

    $this->str_sql=<<<heredoc
    INSERT INTO $obj_clone->SchemaName.$str_name_table
      (
      `Name`,
      `Title`,
      `Module`,
      `MenuTab`,
      `Standard`,
      `PostEditAction`,
      `ModifiedDate`,
      `ModifiedBy`,
      `CreatedDate`,
      `CreatedBy`
      )
    VALUES
    (
      '$obj_ini->str_Name',
      '$obj_ini->str_Name',
      'Sales',
      '$obj_ini->str_Name',
      '1',
      '$obj_ini->str_PostEditAction',
      '$this->str_date',
      '1',
      '$this->str_date',
      '1'
    );
heredoc;
    $this->fn_record_action();
    $int_id_record=$this->fn_get_last_insert_id();
    if($this->view_step){
      $this->fn_view_cloned_record($str_name_table, $int_id_record);
    }
    return $int_id_record;
  }

  function fn_create_record_page_detail($obj_ini){

    $obj_clone=$this->obj_clone;
    if(!isset($obj_ini->int_PageId)){die("Page Detail PageId required");}

    $this->str_sql="DELETE FROM $obj_clone->SchemaName.pagedetail where NAME='$obj_ini->str_Name';";
    $this->fn_record_action();

    $this->fn_insert_record_page_detail($obj_ini);
  }

  function fn_insert_record_page_detail($obj_ini){

    $obj_clone=$this->obj_clone;
    $str_name_table="pagedetail";

    if(!isset($obj_ini->int_PageId)){die("Page Detail PageId required");}
    if(!isset($obj_ini->str_PageDetailType)){die("Page Detail PageDetailType required");}
    if(!isset($obj_ini->int_ObjectOrder)){$obj_ini->int_ObjectOrder=0;}
    if(!isset($obj_ini->str_Name)){die("Page Detail Name required");}

    $this->str_sql=<<<heredoc
    INSERT INTO $obj_clone->SchemaName.$str_name_table
      (
      `PageId`,
      `ObjectOrder`,
      `PageDetailType`,
      `Name`,
      `ModifiedDate`,
      `ModifiedBy`,
      `CreatedDate`,
      `CreatedBy`
      )
    VALUES
    (
      '$obj_ini->int_PageId',
      '$obj_ini->int_ObjectOrder',
      '$obj_ini->str_PageDetailType',
      '$obj_ini->str_Name',
      '$this->str_date',
      '1',
      '$this->str_date',
      '1'
    );
heredoc;
    $this->fn_record_action();
    if($this->view_step){
      $int_id_record=$this->fn_get_last_insert_id();
      $this->fn_view_cloned_record($str_name_table, $int_id_record);
    }
  }

  function fn_create_record_form(){

    $obj_clone=$this->obj_clone;

    $this->str_sql="DELETE FROM $obj_clone->SchemaName.form where NAME='$obj_clone->ObjectName';";
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $obj_ini->str_Name=$obj_clone->ObjectName;
    $obj_ini->str_Tab=1;
    $obj_ini->str_RecordName="Name";
    $this->fn_insert_record_form($obj_ini);

    for ($i = 1; $i <= 20; $i++) {
      $obj_ini = new \stdClass();
      $obj_ini->str_Name=$obj_clone->ObjectName;
      $obj_ini->str_Tab=$i+1;
      $str_pad=str_pad($i, 2, "0", STR_PAD_LEFT);
      $obj_ini->str_RecordName="Custom".$str_pad;
      $this->fn_insert_record_form($obj_ini);
    }

    $obj_ini = new \stdClass();
    $obj_ini->str_Name=$obj_clone->ObjectName;
    $obj_ini->str_Tab=25;
    $obj_ini->str_RecordName="nextcolumn";
    $this->fn_insert_record_form($obj_ini);

    $obj_ini = new \stdClass();
    $obj_ini->str_Name=$obj_clone->ObjectName;
    $obj_ini->str_Tab=100;
    $obj_ini->str_RecordName="Notes";
    $this->fn_insert_record_form($obj_ini);
  }

  function fn_insert_record_form($obj_ini){

    $obj_clone=$this->obj_clone;
    $str_name_table="form";

    if(!isset($obj_ini->str_Name)){die("Form Name required");}
    if(!isset($obj_ini->str_Tab)){die("Form Tab required");}
    if(!isset($obj_ini->str_RecordName)){die("Form RecordName required");}


    $this->str_sql=<<<heredoc
    INSERT INTO $obj_clone->SchemaName.$str_name_table
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
      $this->fn_view_cloned_record($str_name_table, $int_id_record);
    }
  }

  function fn_create_record_form_control(){

    $obj_clone=$this->obj_clone;

    $this->str_sql="DELETE FROM $obj_clone->SchemaName.formcontrol where NAME='$obj_clone->ObjectName';";
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $obj_ini->str_Name=$obj_clone->ObjectName;
    $obj_ini->str_Object=$obj_clone->ObjectName;
    $obj_ini->str_Rows=22;
    $obj_ini->str_Columns=1;
    $this->fn_insert_record_form_control($obj_ini);
  }

  function fn_insert_record_form_control($obj_ini){

    $obj_clone=$this->obj_clone;
    $str_name_table="formcontrol";

    if(!isset($obj_ini->str_Name)){die("FormControl Name required");}
    if(!isset($obj_ini->str_Object)){die("FormControl Object required");}
    if(!isset($obj_ini->str_Rows)){die("FormControl Rows required");}
    if(!isset($obj_ini->str_Columns)){die("FormControl Columns required");}

    $this->str_sql=<<<heredoc
    INSERT INTO $obj_clone->SchemaName.$str_name_table
      (
      `Name`,
      `Object`,
      `Rows`,
      `Columns`,
      `ModifiedDate`,
      `ModifiedBy`,
      `CreatedDate`,
      `CreatedBy`
      )
    VALUES
    (
      '$obj_ini->str_Name',
      '$obj_ini->str_Object',
      '$obj_ini->str_Rows',
      '$obj_ini->str_Columns',
      '$this->str_date',
      '1',
      '$this->str_date',
      '1'
    );
heredoc;
    $this->fn_record_action();
    if($this->view_step){
      $int_id_record=$this->fn_get_last_insert_id();
      $this->fn_view_cloned_record($str_name_table, $int_id_record);
    }
  }

  function fn_create_record_grid(){

    $obj_clone=$this->obj_clone;

    $this->str_sql="DELETE FROM $obj_clone->SchemaName.griddefinition where GRIDNAME='$obj_clone->ObjectNames';";
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $obj_ini->str_GridName=$obj_clone->ObjectNames;
    $obj_ini->str_GridField="$obj_clone->ObjectName.Name";
    $obj_ini->int_ColumnOrder=0;
    $obj_ini->int_LinkEdit=2;
    $this->fn_insert_record_grid($obj_ini);

    $obj_ini = new \stdClass();
    $obj_ini->str_GridName=$obj_clone->ObjectNames;
    $obj_ini->str_GridField="$obj_clone->ParentObjectName.Name";
    $obj_ini->int_ColumnOrder=1;
    $obj_ini->int_LinkEdit=1;
    $this->fn_insert_record_grid($obj_ini);

    for ($i = 1; $i <= 10; $i++) {
      $obj_ini = new \stdClass();
      $obj_ini->str_GridName=$obj_clone->ObjectNames;
      $str_pad=str_pad($i, 2, "0", STR_PAD_LEFT);
      $obj_ini->str_GridField="$obj_clone->ObjectName.Custom$str_pad";
      $obj_ini->int_ColumnOrder=$i+1;
      $obj_ini->int_LinkEdit=0;
      $this->fn_insert_record_grid($obj_ini);
    }
    /////////////////////CREATE ParentObjectChild Grid Definition
    $this->str_sql="DELETE FROM $obj_clone->SchemaName.griddefinition where GRIDNAME='$obj_clone->ParentObjectName"."$obj_clone->ObjectNames';";
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $obj_ini->str_GridName=$obj_clone->ParentObjectName.$obj_clone->ObjectNames;
    $obj_ini->str_GridField="$obj_clone->ObjectName.Name";
    $obj_ini->int_ColumnOrder=0;
    $obj_ini->int_LinkEdit=2;
    $this->fn_insert_record_grid($obj_ini);

    for ($i = 1; $i <= 10; $i++) {
      $obj_ini = new \stdClass();
      $obj_ini->str_GridName=$obj_clone->ParentObjectName.$obj_clone->ObjectNames;
      $str_pad=str_pad($i, 2, "0", STR_PAD_LEFT);
      $obj_ini->str_GridField="$obj_clone->ObjectName.Custom$str_pad";
      $obj_ini->int_ColumnOrder=$i;
      $obj_ini->int_LinkEdit=0;
      $this->fn_insert_record_grid($obj_ini);
    }
  }

  function fn_insert_record_grid($obj_ini){

    $obj_clone=$this->obj_clone;
    $str_name_table="griddefinition";

    if(!isset($obj_ini->str_GridName)){die("Grid GridName required");}
    if(!isset($obj_ini->str_GridField)){die("Grid GridField required");}
    if(!isset($obj_ini->int_ColumnOrder)){die("Grid ColumnOrder required");}
    if(!isset($obj_ini->int_LinkEdit)){die("Grid LinkEdit required");}

    $this->str_sql=<<<heredoc
    INSERT INTO $obj_clone->SchemaName.$str_name_table
      (
      `GridName`,
      `GridField`,
      `ColumnOrder`,
      `LinkEdit`,
      `ModifiedDate`,
      `ModifiedBy`,
      `CreatedDate`,
      `CreatedBy`
      )
    VALUES
    (
      '$obj_ini->str_GridName',
      '$obj_ini->str_GridField',
      '$obj_ini->int_ColumnOrder',
      '$obj_ini->int_LinkEdit',
      '$this->str_date',
      '1',
      '$this->str_date',
      '1'
    );
heredoc;
    $this->fn_record_action();
    if($this->view_step){
      $int_id_record=$this->fn_get_last_insert_id();
      $this->fn_view_cloned_record($str_name_table, $int_id_record);
    }
  }

  function fn_create_record_grid_control(){

    $obj_clone=$this->obj_clone;

    $this->str_sql="DELETE FROM $obj_clone->SchemaName.gridcontrol where NAME='$obj_clone->ObjectNames';";
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $obj_ini->str_Name=$obj_clone->ObjectNames;
    $obj_ini->str_Title=$obj_clone->ObjectNames;
    $obj_ini->str_View=1;
    $obj_ini->str_Object=$obj_clone->ObjectName;
    $obj_ini->str_NewObjectPage="";
    $obj_ini->str_SQLFrom="$obj_clone->ObjectName LEFT JOIN $obj_clone->ParentObjectName ON $obj_clone->ObjectName.{$obj_clone->ParentObjectName}Id=$obj_clone->ParentObjectName.Id LEFT JOIN User ON $obj_clone->ParentObjectName.OwnerId=User.Id";
    $obj_ini->str_SQLWhere="";
    $obj_ini->str_TablePermission="W";
    $this->fn_insert_record_grid_control($obj_ini);

    /////////////////////CREATE ParentObjectChild Grid Control
    $this->str_sql="DELETE FROM $obj_clone->SchemaName.gridcontrol where NAME='$obj_clone->ParentObjectName$obj_clone->ObjectNames';";
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $obj_ini->str_Name=$obj_clone->ParentObjectName.$obj_clone->ObjectNames;
    $obj_ini->str_Title=$obj_clone->ObjectNames;
    $obj_ini->str_View=0;
    $obj_ini->str_Object=$obj_clone->ObjectName;
    $obj_ini->str_NewObjectPage='page.asp?p='.$obj_clone->ObjectName.'&id=new&pid={'.$obj_clone->ParentObjectName.'Id}';
    $obj_ini->str_SQLFrom="$obj_clone->ObjectName INNER JOIN $obj_clone->ParentObjectName ON $obj_clone->ObjectName.{$obj_clone->ParentObjectName}Id=$obj_clone->ParentObjectName.Id";
    $obj_ini->str_SQLWhere="$obj_clone->ObjectName.{$obj_clone->ParentObjectName}Id={Id}";
    $obj_ini->str_TablePermission="W";
    $this->fn_insert_record_grid_control($obj_ini);
  }

  function fn_insert_record_grid_control($obj_ini){

    $obj_clone=$this->obj_clone;
    $str_name_table="gridcontrol";

    if(!isset($obj_ini->str_Name)){die("GridControl Name required");}
    if(!isset($obj_ini->str_Title)){die("GridControl Title required");}
    if(!isset($obj_ini->str_View)){die("GridControl View required");}
    if(!isset($obj_ini->str_Object)){die("GridControl Object required");}
    if(!isset($obj_ini->str_NewObjectPage)){die("GridControl NewObjectPage required");}
    if(!isset($obj_ini->str_SQLFrom)){die("GridControl SQLFrom required");}
    if(!isset($obj_ini->str_SQLWhere)){die("GridControl SQLWhere required");}
    if(!isset($obj_ini->str_TablePermission)){die("GridControl TablePermission required");}

    $this->str_sql=<<<heredoc
    INSERT INTO $obj_clone->SchemaName.$str_name_table
      (
      `Name`,
      `Title`,
      `View`,
      `FilterField`,
      `Object`,
      `NewObjectPage`,
      `SQLFrom`,
      `SQLWhere`,
      `TablePermission`,
      `ModifiedDate`,
      `ModifiedBy`,
      `CreatedDate`,
      `CreatedBy`
      )
    VALUES
    (
      '$obj_ini->str_Name',
      '$obj_ini->str_Title',
      '$obj_ini->str_View',
      '*',
      '$obj_ini->str_Object',
      '$obj_ini->str_NewObjectPage',
      '$obj_ini->str_SQLFrom',
      '$obj_ini->str_SQLWhere',
      '$obj_ini->str_TablePermission',
      '$this->str_date',
      '1',
      '$this->str_date',
      '1'
    );
  heredoc;
    $this->fn_record_action();
    if($this->view_step){
      $int_id_record=$this->fn_get_last_insert_id();
      $this->fn_view_cloned_record($str_name_table, $int_id_record);
    }
  }



  function fn_create_record_menutab(){

    $obj_clone=$this->obj_clone;

    $this->str_sql="DELETE FROM $obj_clone->SchemaName.menutab where NAME='$obj_clone->ObjectNames';";
    $this->fn_record_action();

    $obj_ini = new \stdClass();
    $obj_ini->str_Name=$obj_clone->ObjectNames;
    $obj_ini->str_ModuleId=1;
    $obj_ini->str_TabOrder=100;
    $obj_ini->str_Live=1;
    $obj_ini->str_PageToLink="page.asp?p=$obj_clone->ObjectNames";

    $this->fn_insert_record_menutab($obj_ini);
  }

  function fn_insert_record_menutab($obj_ini){

    $obj_clone=$this->obj_clone;
    $str_name_table="menutab";

    if(!isset($obj_ini->str_Name)){die("MenuTab Name required");}
    if(!isset($obj_ini->str_ModuleId)){die("MenuTab ModuleId required");}
    if(!isset($obj_ini->str_TabOrder)){die("MenuTab TabOrder required");}
    if(!isset($obj_ini->str_Live)){die("MenuTab Live required");}
    if(!isset($obj_ini->str_PageToLink)){die("MenuTab PageToLink required");}


    $this->str_sql=<<<heredoc
    INSERT INTO $obj_clone->SchemaName.$str_name_table
      (
      `Name`,
      `ModuleId`,
      `TabOrder`,
      `Live`,
      `Standard`,
      `PageToLink`,
      `ModifiedDate`,
      `ModifiedBy`,
      `CreatedDate`,
      `CreatedBy`
      )
    VALUES
    (
      '$obj_ini->str_Name',
      '$obj_ini->str_ModuleId',
      '$obj_ini->str_TabOrder',
      '$obj_ini->str_Live',
      '1',
      '$obj_ini->str_PageToLink',
      '$this->str_date',
      '1',
      '$this->str_date',
      '1'
    );
  heredoc;
    $this->fn_record_action();
    if($this->view_step){
      $int_id_record=$this->fn_get_last_insert_id();
      $this->fn_view_cloned_record($str_name_table, $int_id_record);
    }
  }


}

/*
{
"SchemaName":"data182219",
"ObjectName":"Finance"
}
//*/
?>
