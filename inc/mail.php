<?php


// define("DATA_ROOT",$_SERVER['DOCUMENT_ROOT']."/txDB/data2"); //define data folder root here
namespace Includes\Mail;
use Includes\Crypt\Encrypt as encrypt;
use Includes\Crypt\Encrypt as decrypt;
class Handler{
	

	function send($mail_contents) {
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->Host = "";
		$mail->Port = "";
		$mail->IsHTML(true);
		$mail->CharSet  = "";
		$mail->Encoding = "";
		
		$mail->FromName = $mail_contents['fromName'];
		$mail->From     = $mail_contents['from'];
		$mail->Subject  = $mail_contents['subject'];
		$mail->Body     = $mail_contents['body'];
		$mail->WordWrap = $mail_contents['wordwrap'];
		$mail->AddAddress($mail_contents['to']);

		$sent = $mail->Send();
		if($sent) {
			return 1;
		} else {
			return 0;
		}


	}


}


?>
