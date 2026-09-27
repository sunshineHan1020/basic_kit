<?

/*
const low_act_week_guide = 7;
const low_act_goal_guide = 6;
const high_act_week_guide =5;
const high_act_goal_guide =1;
const screen_week_guide=1;
const screen_weekend_guide =2;
const screen_week_guide_minute=0;
const screen_weekend_guide_minute =0;
const sleep_start_guide="11:00";
const sleep_end_guide="07:00";
const high_act_guide_minute =0;
const low_act_guide_minute =0;

*/

function getScheduleType($at){
	if($at==1) return " 훈련";
	if($at==2) return "기념일";
	if($at==3) return "대회";
	if($at==4) return "매치";
}

function addZero($num){
	if($num<10){
		return "0".$num;
	}else{
		return $num;
	}
}

function getWeekInfo($_date)
{
    $BASIC_DOW = 1; // 1(mon) ~ 7(sun)
    list($yy, $mm, $dd) = explode('-', $_date);
    
    $dow = date('N', mktime(0, 0, 0, $mm, 1, $yy));
    
    if ($dow <= $BASIC_DOW)
    {
        $diff = $BASIC_DOW - $dow;
        $srt_day = $diff+1;
    } else {
        $diff = 7-$dow;
        $srt_day = $diff + $BASIC_DOW + 1;
    }

    if ($dd < $srt_day)
    {
        $new_date = date('Y-m-d', mktime(0, 0, 0, $mm, 0, $yy));
        return getWeekInfo($new_date);
    } else {
        $wom = ceil(($dd-($srt_day-1))/7);

        // 이곳을 수정하면 원하시는 결과로 리턴하셔도 됩니다.
        $new_date = (int)$mm. '-' .$wom; 
        return $new_date;
    }
}


function getWeeYearkInfo($_date)
{
    $BASIC_DOW = 1; // 1(mon) ~ 7(sun)
    list($yy, $mm, $dd) = explode('-', $_date);

    $dow = date('N', mktime(0, 0, 0, $mm, 1, $yy));
    
    if ($dow <= $BASIC_DOW)
    {
        $diff = $BASIC_DOW - $dow;
        $srt_day = $diff+1;
    } else {
        $diff = 7-$dow;
        $srt_day = $diff + $BASIC_DOW + 1;
    }

    if ($dd < $srt_day)
    {
        $new_date = date('Y-m-d', mktime(0, 0, 0, $mm, 0, $yy));
        return getWeeYearkInfo($new_date);
    } else {
        $wom = ceil(($dd-($srt_day-1))/7);

        // 이곳을 수정하면 원하시는 결과로 리턴하셔도 됩니다.
        $new_date = $yy."-".(int)$mm. '-' .$wom; 
        return $new_date;
    }
}

function sleep_time_week_cal($sleep_start,$sleep_end){
	$sleep_s_arr = explode(":",$sleep_start);
	$sleep_e_arr = explode(":",$sleep_end);
	$shour = (12 - $sleep_s_arr[0])*60*7;
	if($sleep_s_arr[1]!="00"){
		$shour+=   ((60-$sleep_s_arr[1])*7);
	}
	$shour+=  $sleep_e_arr[0]*60*7;
	if($sleep_e_arr[1]!="00"){
		$shour+=   ((60-$sleep_e_arr[1])*7);
	}
	return $shour;
}


function sleep_time_cal($sleep_start,$sleep_end){
	$sleep_s_arr = explode(":",$sleep_start);
	$sleep_e_arr = explode(":",$sleep_end);
	$shour = (12 - $sleep_s_arr[0])*60;
	if($sleep_s_arr[1]!="00"){
		$shour+=   ((60-$sleep_s_arr[1]));
	}
	$shour+=  $sleep_e_arr[0]*60;
	if($sleep_e_arr[1]!="00"){
		$shour+=   ((60-$sleep_e_arr[1]));
	}
	return $shour;
}

function getDayLabel($date) {
	$week = array('S', 'M', 'T', 'W', 'T', 'F', 'S');
	$weekday = $week[ date('w'  , strtotime($date)  ) ] ;;
	return $weekday;
}


function getDayYoil($date){
	$week = array('일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일');
	$weekday = $week[ date('w'  , strtotime($date)  ) ] ;;
	return $weekday;
}




@$nowJucha=$_REQUEST['nowJucha'];	//기록 날짜
$day = $nowJucha;

if(empty($nowJucha)){
	$nowJucha = getWeekInfo(date('Y-m-d'));
	$day = date('Y-m-d');
	$inWeek = getWeeYearkInfo(date('Y-m-d'));
	$prevJu =  date("Y-m-d", strtotime("-7 Day")) ;
	$nextJu =  date("Y-m-d", strtotime("+7 Day"));
	$prevDay = date("Y-m-d", strtotime("-1 Day")) ;
	$nextDay = date("Y-m-d", strtotime("+1 Day")) ;

}else{
	$nowJucha = getWeekInfo($nowJucha);
	$inWeek = getWeeYearkInfo($day);
	$prevJu = date("Y-m-d", strtotime("-7 day", strtotime($day))); 
	$nextJu =  date("Y-m-d", strtotime("+7 day", strtotime($day))); 
	$prevDay = date("Y-m-d", strtotime("-1 day", strtotime($day))); 
	$nextDay =  date("Y-m-d", strtotime("+1 day", strtotime($day))); 
}
 

$nowJuchaArr = explode("-",$nowJucha);
$nowJu = $nowJuchaArr[0]."-".$nowJuchaArr [1];
$lastWeek=false;
$lastDay = false;

if( $nextJu > date('Y-m-d') ) $lastWeek = true;
if( $nextDay > date('Y-m-d') ) $lastDay = true;



$ddArr = explode("-",$day);
$day_year = $ddArr[0];
$day_month = $ddArr[1];
$day_day = $ddArr[2];

$today_w  = date('w',mktime(0,0,0,$day_month,$day_day,$day_year));
if($today_w == 0) $today_w = 7;

$start_mon = date("Y-m-d", mktime(0,0,0,  $day_month  ,$day_day-$today_w+1, $day_year));
$end_mon=  date("Y-m-d", strtotime("+6 day", strtotime($start_mon))); 




?>