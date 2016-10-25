<?php
/*
input:tow array like that array('A'=>int,'B'=>int,'C'=>int,'D'=>int)
output:distance between tow array
info:distance metric is euclidean metric
 */
function distance($one,$another){
	$re=0;
	for($i=65;$i<69;$i++){
		$dis=$one[chr($i)]-$another[chr($i)];
		if($dis<0)
			$dis=-$dis;
		$re+=$dis*$dis;
	}
	$re=sqrt($re);
	return $re;
}
/*
input:an array which is seem to be a class
output:the center of mass between this array
 */
function merge(&$classarray){
	$re=array();
	for($i=65;$i<69;$i++)
		$re[chr($i)]=0;
	for($i=65;$i<69;$i++)
		for($j=0;$j<count($classarray);$j++)
			$re[chr($i)]+=$classarray[$j][chr($i)];
			//array_sum();
	for($i=65;$i<69;$i++)
		$re[chr($i)]=$re[chr($i)]/count($classarray);
	return $re;
}
/*
input:
	$data 需要分类的数据集,$CLASS_NUM 标量参数K,$MAX_TIME 最大迭代次数,$THRESHOLD 阈值
output:
	CLASS_ARRAY:分类数组
 */
function kmeans($data,$CLASS_NUM=4,$MAX_TIME=3,$THRESHOLD=3){
	$CLASS_ARRAY=array();//分类的类数据数组
	$CLASS_CENTER=array();//类质心数组
	//随机选取$CLASS_NUM个数组作为起始质心
	foreach(array_rand($data,$CLASS_NUM) as $k)
		$CLASS_CENTER[]=$data[$k];
	for($repeat=0;$repeat<$MAX_TIME;$repeat++){//重复计算，减少误差
		for($i=0;$i<$CLASS_NUM;$i++)
			$CLASS_ARRAY[$i]=array();
		for($ergodicity=0;$ergodicity<count($data);$ergodicity++){//遍历dataset
			$key=0;//用于标记data所属的类
			for($i=0,$tmp=0;$i<$CLASS_NUM;$i++){//计算data与各center的距离，并取最小值
				$tmpp=distance($data[$ergodicity],$CLASS_CENTER[$i]);
				if($tmp == 0 OR $tmp>=$tmpp){
					$tmp=$tmpp;
					$key=$i;
				}
			}
			$CLASS_ARRAY[$key][]=$data[$ergodicity];//
		}
		for($i=0;$i<$CLASS_NUM;$i++)
				$CLASS_CENTER[$i]=merge($CLASS_ARRAY[$i]);
	}
	return $CLASS_CENTER;
}
/*
input:target_array to be divided,key to divide,$tips
	tips look like array('tipA'=>array('word1','word2'),'tipB')
return:a divided array look like array('tipA',......,'default')
*/
function divide($targets,$key,$tips){
	$re=array();
		foreach ($targets as $k=>$target) {
			$i=-1;
			foreach ($tips as $tipname => $tip) {
				if(in_array($target[$key], $tip)){
					$i=$tipname;
					break;
				}
			}
			$re[$i==-1?'default':$i][$k]=$target;
		}
	foreach ($tips as $tipname => $tip) {
		if(!isset($re[$tipname]))
			$re[$tipname]=array();
	}
	return $re;
}
function Mysort($a,$b){
	$key=0;
	if($a['pitchtime']<=$b['pitchtime'])
		$key--;
	else
		$key++;
	if($a['grade']>=$b['grade'])
		$key--;
	else
		$key++;
	return $key;
}
function bpitch(&$male,&$female,&$resultset){
	foreach ($male as $key => $value) {
		$targetset=divide($female,'grade',array('tobepitch'=>range(0,$value['grade'])));
		if(!isset($targetset['tobepitch']) OR count($targetset['tobepitch'])==0)
			break;
		uasort($targetset['tobepitch'],'Mysort');
		foreach ($targetset['tobepitch'] as $targetkey => $target){
			if($target['college']!=$value['college']){
				$resultset[]=array('id'=>$value['id'],'pitchid'=>$target['id']);
				$female[$targetkey]['pitchtime']+=1;
				unset($female[$targetkey]);
				unset($male[$key]);
				break;
			}
		}
	}
}
function gpitch(&$female,&$male,&$resultset){
	foreach ($female as $key => $value) {
		$targetset=divide($male,'grade',array('tobepitch'=>range($value['grade'],4)));
		if(!isset($targetset['tobepitch']) OR count($targetset['tobepitch'])==0)
			break;
		uasort($targetset['tobepitch'],'Mysort');
		foreach ($targetset['tobepitch'] as $targetkey => $target){
			if($target['college']!=$value['college']){
				$resultset[]=array('id'=>$value['id'],'pitchid'=>$target['id']);
				$male[$targetkey]['pitchtime']+=1;
				unset($male[$targetkey]);
				unset($female[$key]);
				break;
			}
		}
	}
}
function pitch(&$left,&$right,&$resultset,$tip=TRUE){
	foreach ($left as $key => $value) {
		if($tip){
				$targetset=divide($right,'grade',array('tobepitch'=>range(0,$value['grade'])));
				uasort($targetset['tobepitch'],'Mysort');//多次匹配时用这条
		}else
				$targetset['tobepitch']=$right;
		foreach ($targetset['tobepitch'] as $targetkey => $target){
			if($target['college']!=$value['college']){
				$resultset[]=array('id'=>$value['id'],'pitchid'=>$target['id']);
				$right[$targetkey]['pitchtime']+=1;
				if(count($right)>1)
					unset($right[$targetkey]);
				unset($left[$key]);
				break;
			}
		}
	}	
}
?>