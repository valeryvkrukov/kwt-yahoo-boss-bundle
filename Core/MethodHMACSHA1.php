<?php 
namespace KWT\Yahoo\BossBundle\Core;

use KWT\Yahoo\BossBundle\Core\Util;
use KWT\Yahoo\BossBundle\Core\Method;

class MethodHMACSHA1 extends Method{
	
	public function get_name(){
		return 'HMAC-SHA1';
	}
	
	public function build_signature($request,$consumer,$token){
		$base_string=$request->get_signature_base_string();
		$request->base_string=$base_string;
		$key_parts=array($consumer->secret,(($token)?$token->secret:''));
		$key_parts=Util::urlencode_rfc3986($key_parts);
		$key=implode('&',$key_parts);
		return base64_encode(hash_hmac('sha1',$base_string,$key,true));
	}
	
}