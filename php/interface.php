<?php
require_once('SQL.php');
$college=array('机械与汽车工程学院','建筑学院','土木与交通学院','电子与信息学院','材料科学与工程学院','化学与化工学院','轻工科学与工程学院','食品科学与工程学院','数学学院','物理与光电学院','经济与贸易学院','自动化科学与工程学院','计算机科学与工程学院','电力学院','生物科学与工程学院','环境与能源学院','软件学院','工商管理学院（创业教育学院）','公共管理学院','马克思主义学院','外国语学院','法学院（知识产权学院）','新闻与传播学院','艺术学院','体育学院','设计学院','医学院','国际教育学院');//total 28=17+11
$grade=array('大一','大二','大三','大四','研究生');
$sgrade=array_flip($grade);
$type=array('A'=>"文艺爱情",'B'=>"科幻动作",'C'=>"恐惧悬疑",'D'=>"动画喜剧");
if($_SERVER['REQUEST_METHOD']!= 'POST')
	die('感谢关注百步梯的活动');
if(isset($_POST['insert'])){//报名入口
	$data=array();
	//设置积分
	try {
		$mark=json_decode($_POST['mark']);
		if($mark==NULL)
			die(json_encode(array('status'=>-1,'info'=>'感谢关注百步梯的活动')));
		else{
			$mark=(array)$mark;
			$data['A']=$mark['A'];
			$data['B']=$mark['B'];
			$data['C']=$mark['C'];
			$data['D']=$mark['D'];
			if(arsort($mark))
				$data['type']=array_keys($mark)[0];
		}
	} catch (Exception $e) {
		die(json_encode(array('status'=>-2,'info'=>'感谢关注百步梯的活动')));
	}
	//设置name
	$data['name']=htmlentities($_POST['name']);
	//设置gender
	switch (strtolower($_POST['gender'])) {
		case 'female':
		case 'male':
			$data['gender']=strtolower($_POST['gender']);
			break;
		default:
			die(json_encode(array('status'=>-3,'info'=>'感谢关注百步梯的活动')));
			break;
	}
	//设置grade
	if(in_array($_POST['grade'], $grade))
		$data['grade']=$sgrade[$_POST['grade']];
	else
		die(json_encode(array('status'=>-4,'info'=>'感谢关注百步梯的活动')));
	//设置college
	if(in_array($_POST['college'], $college))
		$data['college']=$_POST['college'];
	else
		die(json_encode(array('status'=>-5,'info'=>'感谢关注百步梯的活动')));
	//设置phone
	if(strlen($_POST['phone'])==11 AND preg_match('/1[3,5,7,8]\d{9}/U',$_POST['phone']))
		$data['phone']=$_POST['phone'];
	else
		die(json_encode(array('status'=>-6,'info'=>'感谢关注百步梯的活动')));
	//设置wechat号
	$data['wechat']=isset($_POST['wechat'])?htmlentities($_POST['wechat']):'';
	//生成lottery_code
	$re=$link->table('user')->field('lottery_code')->where(array('phone'=>array("=",$_POST['phone'])))->select();
	if(count($re)==0)
		$data['lottery_code']=chr(rand(65,90)).rand(0,255).$_SERVER['REQUEST_TIME'];
	else
		$data['lottery_code']=$re[0]['lottery_code'];
	//正式插入数据
	$re=$link->table('user')->where(array('phone'=>array("=",$_POST['phone'])))->insert($data);
	if($re)
		die(json_encode(array('status'=> 1,'info'=>$data['lottery_code'],'type'=>$type[$data['type']])));
}

if(isset($_POST['result'])){//获取结果入口
	if(strlen($_POST['phone'])==11 AND preg_match('/1[3,5,7,8]\d{9}/U',$_POST['phone']))
		$where['phone']=$_POST['phone'];
	else
		die(json_encode(array('status'=>-1,'info'=>'感谢关注百步梯的活动')));
	$re=$link->table('user')->field('user.id user.name user.phone user.pitchid user.type user.gender user.grade u.name u.phone u.type u.gender u.grade')->where(array('user.phone'=>array('=',$_POST['phone'])))->join('user as u')->on(array('user.pitchid'=>array('=','u.id')))->select();
	$return=array();
	try {
		$return[0]['name']=$re['name'][0];
		$return[0]['phone']=$re['phone'][0];
		$return[0]['wechat']=$re['wechat'][0];
		$return[0]['type']=$type[$re['type'][0]];
		$return[0]['gender']=$re['gender'][0];
		$return[0]['grade']=$re['grade'][0];
		$return[1]['name']=$re['name'][1];
		$return[1]['phone']=$re['phone'][1];
		$return[1]['wechat']=$re['wechat'][1];
		$return[1]['type']=$type[$re['type'][1]];
		$return[1]['gender']=$re['gender'][1];
		$return[1]['grade']=$re['grade'][1];	
	} catch (Exception $e) {
		die(json_encode(array('status'=>-1,'info'=>'配对失败')));
	}

	die(json_encode(array('status'=>'1','info'=>$return)));
}
?>