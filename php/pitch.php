<?php
/*
only pitch one time
*/
$start=time();
require_once("SQL.php");
require_once('function.php');
$dataset=$link->table('user')->where(array('pitchid'=>array('=','')))->unlimit()->select();
$G_W=array('G'=>array('机械与汽车工程学院','建筑学院','土木与交通学院','电子与信息学院','材料科学与工程学院','化学与化工学院','轻工科学与工程学院','食品科学与工程学院','数学学院','物理与光电学院','自动化科学与工程学院','计算机科学与工程学院','电力学院','生物科学与工程学院','环境与能源学院','软件学院'),'W'=>array('工商管理学院（创业教育学院）','公共管理学院','马克思主义学院','外国语学院','法学院（知识产权学院）','新闻与传播学院','艺术学院','体育学院','设计学院','医学院','国际教育学院','经济与贸易学院'));
$type=divide($dataset,'type',array('A'=>array('A'),'B'=>array('B'),'C'=>array('C'),'D'=>array('D')));
$resultset=array();
foreach (range(65,68) as $i) {
	$gender[chr($i)]=divide($type[chr($i)],'gender',array('female'=>array('female'),'male'=>array('male')));
	$style[chr($i)]['female']=divide($gender[chr($i)]['female'],'college',$G_W);
	$style[chr($i)]['male']=divide($gender[chr($i)]['male'],'college',$G_W);
	echo chr($i).'[male] G：'.count($style[chr($i)]['male']['G']).' W：'.count($style[chr($i)]['male']['W']).'</br>';
	echo chr($i).'[female] G：'.count($style[chr($i)]['female']['G']).' W：'.count($style[chr($i)]['female']['W']).'</br>';
	if(count($style[chr($i)]['male']['G'])<=count($style[chr($i)]['female']['W']))
		bpitch($style[chr($i)]['male']['G'],$style[chr($i)]['female']['W'],$resultset);
	else
		gpitch($style[chr($i)]['female']['W'],$style[chr($i)]['male']['G'],$resultset);
	if(count($style[chr($i)]['male']['W'])<=count($style[chr($i)]['female']['G']))
		bpitch($style[chr($i)]['male']['W'],$style[chr($i)]['female']['G'],$resultset);	
	else
		gpitch($style[chr($i)]['female']['G'],$style[chr($i)]['male']['W'],$resultset);		
	/*
		同类型遗漏者匹配
	*/
	$style[chr($i)]['male']['default']=array_merge($style[chr($i)]['male']['G'],$style[chr($i)]['male']['W']);
	$style[chr($i)]['female']['default']=array_merge($style[chr($i)]['female']['G'],$style[chr($i)]['female']['W']);
	unset($style[chr($i)]['male']['G']);
	unset($style[chr($i)]['male']['W']);
	unset($style[chr($i)]['female']['G']);
	unset($style[chr($i)]['female']['W']);
	if(count($style[chr($i)]['male']['default'])<=count($style[chr($i)]['female']['default']))
		bpitch($style[chr($i)]['male']['default'],$style[chr($i)]['female']['default'],$resultset);
	else
		gpitch($style[chr($i)]['female']['default'],$style[chr($i)]['male']['default'],$resultset);
}
/*
	不同类型遗漏者匹配
*/

$leave['male']=array_merge($style['A']['male']['default'],$style['B']['male']['default'],$style['C']['male']['default'],$style['D']['male']['default']);
$leave['female']=array_merge($style['A']['female']['default'],$style['B']['female']['default'],$style['C']['female']['default'],$style['D']['female']['default']);
pitch($leave['male'],$leave['female'],$resultset);
if(count($leave['male']))
pitch($leave['male'],$leave['female'],$resultset,false);
echo time()-$start;
echo '</br></br>';
/*
	写数据库
*/
foreach ($resultset as $pitchresult) {
	try {
		mainsql::beginTransaction();
		$link->table('user')->where(array('id'=>array('=',$pitchresult['id'])))->update(array('pitchid'=>$pitchresult['pitchid']));
		$link->table('user')->where(array('id'=>array('=',$pitchresult['id'])))->setInc(array('pitchtime'=>1));
		$re=$link->table('user')->where(array('id'=>array('=',$pitchresult['pitchid'])))->select();
		$pitchid=($re[0]['pitchid']=='')?$pitchresult['id']:($re[0]['pitchid'].'|'.$pitchresult['id']);
		$link->table('user')->where(array('id'=>array('=',$pitchresult['pitchid'])))->update(array('pitchid'=>$pitchid));
		$link->table('user')->where(array('id'=>array('=',$pitchresult['pitchid'])))->setInc(array('pitchtime'=>1));
		mainsql::commit();
	} catch (PDOException  $e) {
		mainsql::rollBack();
		echo $e->getMessage();
	}

}
echo time()-$start;
?>