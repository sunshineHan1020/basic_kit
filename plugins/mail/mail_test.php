<?php

error_reporting( E_ALL );
ini_set( "display_errors", 1 );
include_once $_SERVER['DOCUMENT_ROOT'] . '/plugins/mail/send_mail.php';
$result_mail = send_mail_daum("디센트 법률사무소", 'www@hanbom.com', '', 'testest', htmlspecialchars_decode('testest'), '', '');
print_r($result_mail);

?>