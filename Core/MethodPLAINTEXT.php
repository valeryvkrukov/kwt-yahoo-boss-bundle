<?php 
namespace KWT\Yahoo\BossBundle\Core;

use KWT\Yahoo\BossBundle\Core\Util;
use KWT\Yahoo\BossBundle\Core\Method;

class MethodPLAINTEXT extends Method{
	
	public function get_name(){
		return 'PLAINTEXT';
	}
	
	public function build_signature($request,$consumer,$token){
		$key_parts=array($consumer->secret,(($token)?$token->secret:''));
		$key_parts=Util::urlencode_rfc3986($key_parts);
		$key=implode('&',$key_parts);
		$request->base_string=$key;
		return $key;
	}
	
}