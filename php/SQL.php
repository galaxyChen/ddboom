<?php
interface SQL{
	public function __toString();
	public function resetting();
	public function getdata();

	public function insert($data=NULL);
	public function delete();
	public function select();
	public function update($data=NULL);
	public function setInc($data);
	public function setDec($data);
	public function where($arg,$addition=NULL);
	public function table($string);
	public function join($string);
	public function leftjoin($string);
	public function rightjoin($string);
	public function fulljoin($string);
	public function on($arg,$addition=NULL);
	public function union();
	public function unionall();
	public function setdata($data);
	public function field($string);
	public function order($arg);
	public function limit($from,$each=NULL);
	public function unlimit();

}
class mainsql implements SQL{
	private static $PDO;
	private $__statement;
	private $__result;

	private $__hash__;
	private $__tablename;
	private $__sql;
	private $__field;//需要选的列名
	private $__where;//where 语句
	private $__wheredata;
	private $__join;
	private $__on;
	private $__onwhere;
	private $__from;
	private $__each;
	private $__order;
	private $__dataset;
	private $__unlimit;
	static function initialize(){
		global $dbhost,$dbuser,$dbpswd,$dbname;
		mainsql::$PDO=new PDO("mysql:host=".$dbhost.";dbname=".$dbname,$dbuser,$dbpswd);
		mainsql::$PDO->query("SET NAMES UTF8");	
	}
	static function getError(){
		return mainsql::$PDO->errorinfo();
	}
	static function beginTransaction(){
		return mainsql::$PDO->beginTransaction();
	}
	static function commit(){
		return mainsql::$PDO->commit();
	}
	static function rollBack(){
		return mainsql::$PDO->rollBack();
	}
	public function __construct(){
		$this->__hash__=crc32(spl_object_hash($this));
		$this->resetting();
		$this->__result=NULL;
		$this->__statement=NULL;
	}
	public function __toString(){
		return $this->__sql;
	}
	public function resetting($reset=TRUE){
		if($reset){
		$this->__tablename='';
		$this->__sql='';
		$this->__field=' * ';
		$this->__where='';
		$this->__wheredata=array();
		$this->__join=array();
		$this->__on=array();
		$this->__from=0;
		$this->__each=30;
		$this->__order=array();
		$this->__dataset=array();
		$this->__type='2';//select		
		$this->__unlimit=false;
		}
		return $this;
	}
	public function getdata(){
		if($this->__type==2 OR $this->__type==4)//select or setInc setDec
			return $this->__wheredata;
		elseif($this->__type==0)
			return $this->__dataset;
		else
			return array_merge($this->__wheredata,$this->__dataset);
	}
	public function insert($data=NULL){
		if($data!=NULL)
			$this->setdata($data);
		if($this->__where != '' AND count($this->__wheredata)!=0){
			$tmp=$this->select(TRUE,FALSE);
			if(count($tmp)!=0)
				return $this->update($data);
		}
		$this->__type=0;
		$this->checkerror();
		$this->__sql='';
		$col_name='';
		$col_value='';
		foreach ($this->__dataset as $key => $value) {
			if ($col_name != '' and $col_value != ''){
				$col_name.=( ', '.$this->getname(str_replace($this->__hash__,'', $key)).' ');
				$col_value.=' , :'.$key;
			}
			else{
					$col_name=  '( '.$this->getname(str_replace($this->__hash__,'', $key)).' ';
					$col_value=' VALUES( :'.$key;
			}
		}
		$this->__sql='INSERT INTO '.$this->getname($this->__tablename).$col_name. ') '.$col_value.') ';
		$this->__statement=mainsql::$PDO->prepare($this->__sql);
		$this->__result=$this->__statement->execute($this->getdata());
		$this->resetting();
		return $this->__result;
	}
	public function delete(){
		$this->__type=1;
		$this->checkerror();
		$this->__sql='DELETE FROM '.$this->getname($this->__tablename).' WHERE '.$this->__where;
		$this->__statement=mainsql::$PDO->prepare($this->__sql);
		$this->__result=$this->__statement->execute($this->getdata());
		$this->resetting();
		return $this->__result;
	}
	public function select($run=TRUE,$reset=TRUE){
		$this->__type=2;
		if($this->__onwhere!='')
			$this->onend();
		$this->checkerror();
		$sql='SELECT  ' . $this->__field .  ' FROM ' .$this->getname($this->__tablename);
		for ($i=0; $i < count($this->__join); $i++) { 
			# code...
			$sql.=$this->__join[$i].$this->__on[$i];
		}
		$sql.='  WHERE ' .$this->__where;
		if(count($this->__order)!=0){
			$order='';
			foreach ($this->__order as $key => $value) {
				# code...
				if($order=='')
					$order.=' ORDER BY '.$value;
				else
					$order.=' , '.$value;
			}
			$sql.=$order;
		}
		if($this->__unlimit!=TRUE)
			$sql.=(' LIMIT '.$this->__from.','.$this->__each);
		$this->__sql.=$sql;
		if($run){
			$this->__statement=mainsql::$PDO->prepare($this->__sql);
			$this->__result=$this->__statement->execute($this->getdata());
			$this->resetting($reset);
			return $this->__statement->fetchall(PDO::FETCH_NAMED);
		}
		else
			return $this;
	}
	public function update($data=NULL){
		$this->__type=3;
		if($data!=NULL)
			$this->setdata($data);
		$this->checkerror();
		$set='';
		foreach ($this->__dataset as $key => $value) {
				if($set==''){
					$set.=($this->getname(str_replace($this->__hash__,'', $key)).'= :'.$key);
				}
				else
					$set.=(','.$this->getname(str_replace($this->__hash__,'', $key)).' = :'.$key);	
		}
		$this->__sql='UPDATE '.$this->getname($this->__tablename).' SET '.$set. ' WHERE '.$this->__where.' ';
		$this->__statement=mainsql::$PDO->prepare($this->__sql);
		$this->__result=$this->__statement->execute($this->getdata());
		$this->resetting();
		return $this->__result;
	}
	public function setInc($data){//$data=array('colname'=>toaddnum)
		$this->__type=4;
		if($data!=NULL)
			$this->setdata($data);
		$this->checkerror();
		$set='';
		foreach ($this->__dataset as $key => $value) {
				if (is_numeric($value) AND $value>=0) {
					if($set=='')
						$set.=( $this->getname(str_replace($this->__hash__,'', $key)).'='.$this->getname(str_replace($this->__hash__,'', $key)).'+'.$value);
					else
						$set.=(','.$this->getname(str_replace($this->__hash__,'', $key)).' ='.$this->getname(str_replace($this->__hash__,'', $key)).'+'.$value);	
				}else
					die('wrong in setInc with wrong $value');
		}
		$this->__sql='UPDATE '.$this->getname($this->__tablename).' SET '.$set. ' WHERE '.$this->__where.' ';
		$this->__statement=mainsql::$PDO->prepare($this->__sql);
		$this->__result=$this->__statement->execute($this->getdata());
		$this->resetting();
		return $this->__result;
	}
	public function setDec($data){
		$this->__type=4;
		if($data!=NULL)
			$this->setdata($data);
		$this->checkerror();
		$set='';
		foreach ($this->__dataset as $key => $value) {
				if (is_numeric($value) AND $value>=0) {
					if($set=='')
						$set.=( $this->getname(str_replace($this->__hash__,'', $key)).'='.$this->getname(str_replace($this->__hash__,'', $key)).'-'.$value);
					else
						$set.=(','.$this->getname(str_replace($this->__hash__,'', $key)).' ='.$this->getname(str_replace($this->__hash__,'', $key)).'-'.$value);	
				}else
					die('wrong in setInc with wrong $value');
		}
		$this->__sql='UPDATE '.$this->getname($this->__tablename).' SET '.$set. ' WHERE '.$this->__where.' ';
		$this->__statement=mainsql::$PDO->prepare($this->__sql);
		$this->__result=$this->__statement->execute($this->getdata());
		$this->resetting();
		return $this->__result;
	}
	public function where($arg,$addition=NULL){//需要设置清空函数
		if(!is_array($arg)){
			$this->__where.=$arg;
			return $this;
			}
		if($addition==NULL){
			$addition=array();
			for ($i=0; $i <count($arg)-1 ; $i++) { 
				$addition[]=' AND ';
			}
			if(count($arg)==1)
				$addition[]='';
		}
		$this->__where.=$this->build_where($arg,$addition);
		return $this;
	}
	public function table($string){
		$this->__tablename=$string;
		return $this;
	}
	public function join($string){
		$this->__join[]=' INNER JOIN '.$string;
		return $this;
	}
	public function leftjoin($string){
		$this->__join[]=' LEFT JOIN '.$string;
		return $this;
	}
	public function rightjoin($string){
		$this->__join[]=' RIGHT JOIN '.$string;
		return $this;
	}
	public function fulljoin($string){
		$this->__join[]=' FULL  JOIN '.$string;
		return $this;
	}
	public function on($arg,$addition=NULL){
		if(!is_array($arg)){
			$this->__onwhere.=$arg;
			return;
			}
		if($addition==NULL){
			$addition=array();
			for ($i=0; $i <count($arg)-1 ; $i++) { 
				$addition[]=' AND ';
			}
			if(count($arg)==1)
				$addition[]='';
		}
		$this->__onwhere.=$this->build_on($arg,$addition);
		return $this;
	}
	public function onend(){//if call select() or xxxxjoin() but not call onend(),auto call once
		$this->__on[]=' ON '.$this->__onwhere;
		$this->__onwhere='';
		return $this;
	}
	public function union(){
		//-,-思路，调用select(false)->resetting(),然后就等于开始了新的
		$this->select(false);
		$this->__sql.=' UNION ';
		$this->__tablename='';
		$this->__field=' * ';
		$this->__where='';
		$this->__join=array();
		$this->__on=array();
		$this->__from=0;
		$this->__each=30;
		$this->__order=array();
		$this->__dataset=array();
		$this->__type='2';//select		
		$this->__unlimit=false;
		return $this;
	}
	public function unionall(){
		//-,-思路，调用select(false)->resetting(),然后就等于开始了新的
		$this->select(false);
		$this->__sql.=' UNION ALL ';
		$this->__tablename='';
		$this->__field=' * ';
		$this->__where='';
		$this->__join=array();
		$this->__on=array();
		$this->__from=0;
		$this->__each=30;
		$this->__order=array();
		$this->__dataset=array();
		$this->__type='2';//select		
		$this->__unlimit=false;
		return $this;
	}
	public function setdata($data){//需要设置清空函数
		try{
			if(!is_array($data)){
				$array=array();
				foreach (explode(';', $data) as  $value) {
					$a=explode('=', $value);
					if(isset($a[0]) AND isset($a[1]))
						$array[$a[0]]=$a[1];
					else
						throw New Exception("Error in function setdata with a wrong $data");
				}
				$data=&$array;
			}
		}catch(Exception $e){
			die($e->getMessage());
		}
		$hashdata=array();
		foreach ($data as $key => $value) {
			$hashdata[$key.$this->__hash__]=$value;
		}
		$this->__dataset=$hashdata;
		return $this;
	}
	public function field($string){//需要设置清空函数
		$array=explode(' ', $string);
		$this->__field='';
		for ($i=0; $i <count($array) ; $i++) { 
			$this->__field.=($this->getname($array[$i]). (($i<count($array)-1)?' , ':''));
		}
		return $this;
	}
	public function order($arg){
		if(!is_array($arg))
			$this->__order[]=$arg;
		else{
			foreach ($arg as $key => $value) {
				$op='';
				if(is_numeric($value)){
					if($value>=0)
						$op='ASC';
					else
						$op='DESC'; 
				}else{
					switch(strtoupper($value)){
						case 'ASC':$op=' ASC ';break;
						case ' DESC ':$op=' DESC ';break;
						default:$op=' ASC ';break;
					}
				}
				$this->__order[]=$this->getname($key).$op;
			}
		}
		return $this;
	}
	public function limit($from,$each=NULL){
		try {
			if(!is_numeric($from) or ($each != NULL AND !is_numeric($each)))
				throw new Exception("Wrong in limit()", 1);
		} catch (Exception $e) {
			die($e->getMessage());
		}
		$this->__from=$from;
		if($each != NULL)
			$this->__each=$each;
		return $this;
	}
	public function unlimit(){
		$this->__unlimit=TRUE;
		return $this;
	}
	private function build_where($arg,$addition){
		$signal=0;
		$c=array('(',')');
		$re='';
		$i=0;
		foreach ($arg as $colname => $value) {
			if($signal==0)
				{
					if($i<count($arg)-1)//如果不足以让括号配对
						$re.=( $c[$signal].$this->getname($colname).$this->build_whereop($colname,$value)).$addition[$i];
					else
						$re.=( ' '		  .$this->getname($colname).$this->build_whereop($colname,$value)).$addition[$i];
					$signal++;
				}
			else
				{	if($i<count($arg)-1)//
						$re.=( $this->getname($colname).$this->build_whereop($colname,$value)).$c[$signal].$addition[$i];
					else{//这个是最后一个,需要检测$addition[$i]是否存在
						if(!isset($addition[$i]))
							$addition[$i]='';
						$re.=( $this->getname($colname).$this->build_whereop($colname,$value)).$c[$signal].$addition[$i];
					}
					$signal=0;
				}
			$i++;
		}
		return $re;
	}
	private function build_whereop($colname,$value){//子查询在这里
		$re='';
		$v='';
		if(is_object($value[1]) AND get_class($value[1])=='mainsql'){//如果出现子查询
			switch(strtoupper($value[0])){
				case 'exists':$type=' exists ';break;
				case 'notexists':$type=' not exists';break;
				//any
				//all
				case 'EQ':
				case '=':$type=' = ';break;
				case 'LT':
				case '<':$type=" < ";break;
				case 'GT':
				case '>':$type=" > ";break;
				case 'GE':
				case '>=':$type=" >= ";break;
				case 'LE':
				case '<=':$type=" <= ";break;
				case 'NE':
				case 'NEQ':
				case '!=':$type=" != ";break;
				default:$type=" = ";break;
			}
		}else{
			switch(strtoupper($value[0])){
				case 'LIKE':$re=$this->getname($value[1]);break;
				case 'BETWEEN':
					$re='BETWEEN'.':'.$colname.'A'.$this->__hash__.' AND '.':'.$colname.'B'.$this->__hash__;
					$this->__wheredata[':'.$colname.'A'.$this->__hash__]=$value[1];
					$this->__wheredata[':'.$colname.'B'.$this->__hash__]=$value[2];
					break;
				case 'EQ':
				case '=':$type=' = ';break;
				case 'LT':
				case '<':$type=" < ";break;
				case 'GT':
				case '>':$type=" > ";break;
				case 'GE':
				case '>=':$type=" >= ";break;
				case 'LE':
				case '<=':$type=" <= ";break;
				case 'NE':
				case 'NEQ':
				case '!=':$type=" != ";break;
				default:$type=" = ";break;
			}
		}
		if($re == '')
			{
				if(strpos($colname, '.')){
					$colname=str_replace('.', '', $colname);
					$re= ($type.':'.$colname.$this->__hash__);
					$this->__wheredata[':'.$colname.$this->__hash__]=$value[1];	
				}else{
					$re= ($type.':'.$colname.$this->__hash__);
					$this->__wheredata[':'.$colname.$this->__hash__]=$value[1];					
				}

			}
		return $re;
	}
	private function build_on($arg,$addition){
		$signal=0;
		$c=array('(',')','');
		$re='';
		$i=0;
		foreach ($arg as $key => $value) {
			if($signal==0)
				{
					if($i<=count($arg)-1)//如果不足以让括号配对
						$re.=( $c[$signal+2].$this->getname($key).$this->build_onwhereop($value)).$addition[$i];
					else
						$re.=( $c[$signal].$this->getname($key).$this->build_onwhereop($value)).$addition[$i];
					$signal++;
				}
			else
				{	if($i<count($arg)-1)//
						$re.=( $this->getname($key).$this->build_onwhereop($value)).$c[$signal].$addition[$i];
					else{//这个是最后一个,需要检测$addition[$i]是否存在
						if(!isset($addition[$i]))
							$addition[$i]='';
						$re.=( $this->getname($key).$this->build_onwhereop($value)).$c[$signal].$addition[$i];
					}
					$signal=0;
				}
			$i++;
		}
		return $re;		
	}
	private function build_onwhereop($value){
		$re='';
		if(is_numeric($value[1]))
			$v=$value[1];
		else
			$v=$this->getname($value[1]);
		switch(strtoupper($value[0])){
			case 'LIKE':$re=$v;break;
			case 'BETWEEN':
				if(is_numeric($value[2]))
					$v2=$value[2];
				else
					$v2=$this->getname($value[2]);
				$re='BETWEEN'.$v.' AND '.$v2;break;
			case 'EQ':
			case '=':$type=' = ';break;
			case 'LT':
			case '<':$type=" < ";break;
			case 'GT':
			case '>':$type=" > ";break;
			case 'GE':
			case '>=':$type=" >= ";break;
			case 'LE':
			case '<=':$type=" <= ";break;
			case 'NE':
			case 'NEQ':
			case '!=':$type=" != ";break;
			default:$type=" = ";break;
		}
		if($re == '')
			{
				$re= ($type.$v);
			}
		return $re;
	}
	private function checkerror(){
		try{
			if($this->__tablename=='')
				throw new Exception("Tablename is not set");
			if($this->__type!=0 AND $this->__where=='')
				throw new Exception("Please call where() !");
			if(($this->__type==0 OR $this->__type==3 OR $this->__type==4) and count($this->__dataset)==0)
				throw new Exception("Please set data");
			if($this->__type==2 AND count($this->__join)!= count($this->__on))
				throw new Exception("Please make sure join and on is pitchable");
				
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	private function getname($v){
		if(!strpos($v, '.'))
			return ' `'.$v.'` ';
		$str=explode('.',$v);
		return $str[0].'.`'.$str[1].'` ';
	}
}
if(!isset($dbhost))
	$dbhost='119.29.238.68';
if(!isset($dbuser))
	$dbuser='root';
if(!isset($dbpswd))
	$dbpswd='imshabby';
if(!isset($dbname))
	$dbname='duiduipen';
global $link;
mainsql::initialize();
$link=new mainsql();
?>