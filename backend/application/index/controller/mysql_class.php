<?php
@header('Content-Type: text/html; charset=utf-8');
   class mySQL_Class
   {
       var $conn;
	   private $Server;
	   private $Uid;
	   private $Pwd;
	   private $Database;
	   public $tpl;
	   
	   function mySQL_Class($tpl='',$server="127.0.0.1",$uid="sql151035",$pwd="771a315d",$database="sql151035")
	   {
	       $this->tpl = $tpl;
	       $this->Server=$server;
	       $this->Uid=$uid;
	       $this->Pwd=$pwd;
	       $this->Database=$database;
		   $this->conn=@mysql_connect($this->Server,$this->Uid,$this->Pwd) or die("连接mysql服务器失败，程序中止");
		   @mysql_select_db($this->Database,$this->conn) or die("选择数据库失败，程序中止");
		   @mysql_query("set names 'UTF8'",$this->conn);
	   }
	   
	   
	   
	   function SelectDB($database)
	   {
	      @mysql_select_db($database,$this->conn) or die("选择数据库失败，程序中止");
		   @mysql_query("set names 'UTF8'",$this->conn);
	   }
	   
	   //$sql:  与select有关的语句
	   function QueryRS($sql)
	   {
	       $rs=mysql_query($sql,$this->conn);
		   if($rs)
		   {
		        return $rs;
		   }else{
		        return NULL;
		   }
	   }
	   
	   //$sql:  与select有关的语句
	   function QueryRow($sql)
	   {
	      $rs=mysql_query($sql,$this->conn);
		  if($rs)
		  {
			 return @mysql_fetch_array($rs);
		  }else{
		     return NULL;
		  }
	   }
	   
	   //$sql:  与select有关的语句
	   function QueryObject($sql)
	   {
	      $rs=mysql_query($sql,$this->conn);
		  if($rs)
		  {
			 return mysql_fetch_object($rs);
		  }else{
		     return NULL;
		  }
	   }
	   
	   //$sql: 与select有关的语句
	   function QueryOne($sql)
	   {
	      $rs=mysql_query($sql,$this->conn);
		  if($rs)
		  {
		     $arr=mysql_fetch_array($rs,MYSQL_NUM);
			 return $arr[0];
		  }else{
		      return NULL;
		  }
	   }
	   
	   
	   
	   
	   
	   
	   
	   
	   
	   
	    function Querytitle($id)
	   {
		   $id=$this->$id;
		   	   $sqltitle="select TypeName from tb_news_type where id=$id";

	      $rs=mysql_query($sqltitle,$this->conn);
		  if($rs)
		  {
		     $arr=mysql_fetch_array($rs,MYSQL_NUM);
			 return $arr[0];
		  }else{
		      return NULL;
		  }
	   }
	   
	      //定义标题
	  
	   
	   
	   //$sql: 非select语句
	   function QuerySQL($sql,$hasID=false)
	   {
	       $rs=mysql_query($sql,$this->conn);
		   if($rs)
		   {
		      if($hasID)
			  {
			      $sql=trim(strtolower($sql));
				  if(substr($sql,0,6)=="insert")
				  {
				      return mysql_insert_id($this->conn);
				  }else{
				      return mysql_affected_rows($this->conn);
				  }
			  }else{
			      return mysql_affected_rows($this->conn);
			  }
		   }else{
		      return -1;
		   }
	   }
	   
	   //$rs: 是记录集对象
	   function FetchArray($rs)
	   {
	      if($rs)
		  {
		      return mysql_fetch_array($rs);
		  }else{
		      return NULL;
		  }
	   }
	   
	   //$rs: 是记录集对象
	   function FetchObject($rs)
	   {
	      if($rs)
		  {
		      return mysql_fetch_object($rs);
		  }else{
		      return NULL;
		  }
	   }
	   
	   
	   
	   //getAllNum用于返回$rs对应的记录集对象中的记录总数
	   function getAllNum($rs)
	   {
	   	  if($rs)
		  {
			 return mysql_num_rows($rs);
		  }else{
		  	return 0;
		  }
	   }
	   
	   
	   function GteHtml($s,$e,$str)
	   {
		   $str=strip_tags($str);
		   $str=mb_substr($arr[Title],$s,$e,'gbk');
		   return $str;
	   }	   
	   
	   
	  

	   //是否开启缓存
	     function is_cache()
	   {
$cache=$this->QueryOne("select `cache` from `tb_setting_info` where `id`='1'");
return $cache;
	   }
	   
	   
	   
	   
	   
   }
   
 

	
	

   
	
?>