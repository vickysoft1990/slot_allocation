<?php

session_start();
include '../common/inc.common.php';
$aca_year=$_SESSION['aca_year_id'];

ini_set('memory_limit', '512M');// to increase the memory to process large amount of data
ini_set('max_execution_time', 300);// five min max execution time
function fillSlots($requiredSubjects, $availableSlots, $filledSlots =array()) {
   // print_r("ss");
	if (!$requiredSubjects) {
		ksort($filledSlots);
		return $filledSlots;
	}
    foreach ($requiredSubjects as $rIndex => $subject) {
        foreach ($availableSlots[$subject] as $sIndex => $slot) {
            if (!isset($filledSlots[$slot])) {
                unset($requiredSubjects[$rIndex], $availableSlots[$subject][$sIndex]);
                $result = fillSlots(
					$requiredSubjects,
					$availableSlots,
					array_replace($filledSlots, [$slot => $subject])
				);
				if ($result) {
					return $result;
				}
            }
        }
    }
}

$sql="select cls,sec from set_tt group by cls,sec";
$datarr=$Cobj->union($sql);
//$datarr=1;
for($g=0;$g<sizeof($datarr);$g++){
$cls=$datarr[$g]['cls'];
$sec=$datarr[$g]['sec'];

//$cls=4;
//$sec=1;
//$sql="select CONCAT(day,t_n_refid,emp_refid) 'catid' from time_table where class='$cls' and section='$sec' ";
//$ttdatarr=$Cobj->union($sql);

$sql="select sub_refid,hr_count,emp_refid from set_tt where cls='$cls' and sec='$sec' and aca_refid='$aca_year' order by hr_count desc";
$hrcuntarr=$Cobj->union($sql);

//for subject counts********************************
for($qw=0;$qw<sizeof($hrcuntarr);$qw++){
	//foreach ($array as $key => $value)

	$sub_refidy=$hrcuntarr[$qw]['sub_refid'];
	$hourscount=$hrcuntarr[$qw]['hr_count'];
	$emp_refidy=$hrcuntarr[$qw]['emp_refid'];
//$emp_refidy[$sub_refidy]=$hourscount;
$arrsubjectcount[$emp_refidy][$sub_refidy]=$hourscount;
}

//*******************************************************
$inarry="";
for($yu=0;$yu<sizeof($hrcuntarr);$yu++){
	$inarry .=$hrcuntarr[$yu]['emp_refid'].",";
}
$in=trim($inarry,',');
 $sql="select CONCAT(day,t_n_refid,employee_refid) 'catid' from time_table  where  aca_year='$aca_year'  and employee_refid in($in) order by employee_refid ";
$empdataarr=$Cobj->union($sql);

$checkarry=array();
foreach($empdataarr as $wrq){
	array_push($checkarry,$wrq['catid']);
	}
$daysubject=array();
$tubarray=array();
$sub1=array();
$sub2=array();
for($day=1;$day<=6;$day++){
if($day==1){
	for($t=0;$t<sizeof($hrcuntarr);$t++){
		
	$subject_id=$hrcuntarr[$t]['sub_refid'];
	$hr_count=$hrcuntarr[$t]['hr_count']; 
	$emp_refid=$hrcuntarr[$t]['emp_refid'];
	$avghrcount=$hr_count/6;
	$avghrcount=(string)$avghrcount;
	$cval=$avghrcount[0];
	$extval=$avghrcount[2];
		if($cval==1){
		array_push($daysubject,$emp_refid);
		array_push($sub1,$subject_id);
		}
		elseif($cval==2){
		array_push($daysubject,$emp_refid); array_push($sub1,$subject_id);
		array_push($daysubject,$emp_refid); array_push($sub1,$subject_id);
		}
		elseif($cval==3){
		array_push($daysubject,$emp_refid); array_push($sub1,$subject_id);
		array_push($daysubject,$emp_refid); array_push($sub1,$subject_id);
		array_push($daysubject,$emp_refid); array_push($sub1,$subject_id);
		}
				
	if($extval==1){$n=1;
	array_push($tubarray,$emp_refid);
	array_push($sub2,$subject_id);
	}elseif($extval==3){$n=2;
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	}elseif($extval==5){$n=3;
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	}elseif($extval==6){$n=4;
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	}elseif($extval==8){$n=5;
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	array_push($tubarray,$emp_refid); array_push($sub2,$subject_id);
	}			
		}
	
	}
//echo "<br><br>";
$eighthours=array();
$eighsub=array();
// find 8 hours
$no=0;
foreach($daysubject as $val){
			array_push($eighthours,$val);
		
		$valnew=$sub1[$no];
			array_push($eighsub,$valnew);
$no=$no+1;
}
shuffle($eighthours);
$si=sizeof($eighthours);
$number=8-$si;
$sg=sizeof($tubarray);
if($number>$sg){$number=$sg;}
$rand_keys = array_rand($tubarray, $number);
for($ri=0;$ri<$number;$ri++){
	$val=$tubarray[$rand_keys[$ri]];
	array_push($eighthours,$val);
    unset($tubarray[$rand_keys[$ri]]);
		    $val2=$sub2[$rand_keys[$ri]];
			array_push($eighsub,$val2);
		    unset($sub2[$rand_keys[$ri]]);
}

$tubarray=array_values($tubarray);
$sub2=array_values($sub2);

//print_r($eighthours);
$uniqueemp=array_unique($eighthours);

//echo "eighthours<br>";
// ********************************* runs succfully****************&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&7&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
	//echo "<br>";
	$dt1=$day."1"; // one row and 8 hours free check for each 
	$dt2=$day."2";
	$dt3=$day."3";
	$dt4=$day."4";
	$dt5=$day."5";
	$dt6=$day."6";
	$dt7=$day."7";
	$dt8=$day."8";
	foreach($uniqueemp as $unqemp){
	$empid=$unqemp;
$chk1=$dt1."".$empid;
$chk2=$dt2."".$empid;
$chk3=$dt3."".$empid;
$chk4=$dt4."".$empid;
$chk5=$dt5."".$empid;
$chk6=$dt6."".$empid;
$chk7=$dt7."".$empid;
$chk8=$dt8."".$empid;

if (in_array($chk1, $checkarry)) {  }
  else{	//  echo "free";
	$finalarry[$chk1]=1;
	$emp[$empid][]=1;
	  }
if (in_array($chk2, $checkarry))  { }
  else{	//  echo "free";
	$finalarry[$chk2]=2;
		$emp[$empid][]=2;

  }
if (in_array($chk3, $checkarry))  { }
  else{	//  echo "free";
	$finalarry[$chk3]=3;
		$emp[$empid][]=3;

  }
if (in_array($chk4, $checkarry))  { }
  else{	//  echo "free";
	$finalarry[$chk4]=4;
		$emp[$empid][]=4;

  }
if (in_array($chk5, $checkarry))  { }
  else{	//  echo "free";
	$finalarry[$chk5]=5;
		$emp[$empid][]=5;

  }
if (in_array($chk6, $checkarry))  { }
  else{	//  echo "free";
	$finalarry[$chk6]=6;
	$emp[$empid][]=6;

  }
if (in_array($chk7, $checkarry))  { }
  else{	//  echo "free";
	$finalarry[$chk7]=7;
	$emp[$empid][]=7;

  }
if (in_array($chk8, $checkarry))  { }
  else{	//  echo "free";
	$finalarry[$chk8]=8;
		$emp[$empid][]=8;

  }
	}
	
	$subjectSlots=$emp;
	$requiredSubjects=$eighthours;
	//print_r($emp);
//	print_r($subjectSlots);echo"<br>requiredSubjects=>";
	//print_r($requiredSubjects);
	echo "<br>stackoverflow";
//****************************************************************start of shuduling array ******************
usort($requiredSubjects, function($a, $b) use ($subjectSlots) {
	if($subjectSlots[$a]==$subjectSlots[$b]){
		$res=0;
	}elseif($subjectSlots[$a]<$subjectSlots[$b]){
		$res="-1";
	}elseif($subjectSlots[$a]>$subjectSlots[$b]){
		$res=1;
	}
	   // return $subjectSlots[$a] <=> $subjectSlots[$b];
return $res;
});
echo"eight hours";
print_r($requiredSubjects);
echo"avssssstttttttttttttttt";

$filledslot=fillSlots($requiredSubjects, $subjectSlots);

print_r($filledslot);
//***************************************************************end of shuduling slot

for($o=1;$o<9;$o++){
    
$class=$cls;
$section=$sec;
//$aca_year=4;
$dayy=$day;
$t_n_ref=$o;
$employee_ref=$filledslot[$o];
if($employee_ref==""){$employee_ref=0;}
$subject_ref=12;
foreach($hrcuntarr as $valemp){
    //print_r(array_keys($a,"10",false));
$ty1=$valemp['emp_refid'];
$ty2=$valemp['sub_refid'];
$ty3=$valemp['hr_count'];

   $subjectsarray['sub_refid']=$valemp['emp_refid'];
   
}
$keyq="";
$q=$arrsubjectcount[$employee_ref];
foreach ($q as $keyq => $valueq)
{
	if($valueq<=0){
	    continue;
	}
	if($valueq>=1){
	    $valueq=$valueq-1;
	    $arrsubjectcount[$employee_ref][$keyq]=$valueq;
	}
}

		$InputArray['class']=$class;
			$InputArray['section']=$section;
			$InputArray['aca_year']=$aca_year;
			$InputArray['day']=$dayy;
			$InputArray['t_n_refid']=$t_n_ref;
			$InputArray['subject_refid']=$keyq;
			$InputArray['employee_refid']=$employee_ref;
			$res = $Cobj->addNewData("time_table", $InputArray, "");

$sql_valuesarr[] ="('$class','$section','$aca_year','$dayy','$t_n_ref','$subject_ref','$employee_ref') ";
}
//echo"vigen1";
unset($eighthours);
unset($eighsub);
unset($finalarry);
unset($emp);
unset($subjectSlots);
unset($requiredSubjects);
unset($filledSlots);
unset($uniqueemp);
//echo"vigen2";

}// end of day loop
//echo"vigen3";
//break;
echo "hi";
}
//
//echo $sql;
//$sql_values=implode(",",$sql_valuesarr);

//echo $sql="insert into time_table(class,section,aca_year,day,t_n_refid,subject_refid,employee_refid) values $sql_values";
//$sql=rtrim($sql," , ");
//$result=$Cobj->union($sql);
unset($sql_valuesarr);

echo"success";
exit;
?>