$( document ).ready(function() {
  var obj_data, obj_ini;
  obj_ini=new Object();
  obj_ini.str_tag="data-xscript";
  obj_ini.bln_data_menu=true;
  obj_ini.bln_navbar_top=false;
  obj_ini.data_query_text='SELECT true;';
  fn_add_con(obj_ini);
  obj_data=new cls_data(obj_ini);
  obj_data.fn_execute();
});
function fn_add_con(obj_ini){
  var obj_con;
  obj_ini.arr_con=[];
  obj_con=new Object();
  obj_con.str_name="con-rss";
  obj_con.str_title="RSS Script";
  obj_con.str_host="104.199.12.102";
  obj_con.str_user="";
  obj_con.str_password="";
  obj_con.str_schema="data182219";
  //obj_con.data_query_text='{"SchemaName":"data182219","ObjectName":"Finance"}';
  obj_ini.arr_con.push(obj_con);
}
