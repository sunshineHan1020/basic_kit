<?
/* 공통 호출 설정 파일 */
header("Content-Type: text/html; charset=UTF-8");
ini_set('log_errors', 'On');
ini_set("zlib.output_compression", "On");

// 보안설정이나 프레임이 달라도 쿠키가 통하도록 설정
header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');


require_once $_SERVER['DOCUMENT_ROOT']."/lib/config.php";
require_once $_SERVER['DOCUMENT_ROOT']."/lib/lib.php";


$db=db_con($_cfg['dsn']);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$db->exec("set names utf8");

session_cache_expire(360);
ini_set('session.cookie_lifetime', 0) ;
ini_set('session.gc_maxlifetime', 86400);
ini_set("session.cache_expire",14400);
session_start();



function requestBody($data){
	$data = json_decode($data);
	$data = objectToArray($data);
	return $data;
}

//$_REQUEST = requestBody(file_get_contents("php://input"));


@$result['code']='500';
@$result['msg']='잘못된 접근입니다.';
@$result['force_update']='FALSE';


if(empty($_REQUEST['deviceId'])){
	echo json_encode($result);
	exit;
}

?>
