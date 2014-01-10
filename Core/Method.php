<?php 
namespace KWT\Yahoo\BossBundle\Core;

abstract class Method{
	
	abstract public function get_name();
	
	abstract public function build_signature($request,$consumer,$token);
	
	public function check_signature($request,$consumer,$token,$signature){
		$built=$this->build_signature($request,$consumer,$token);
		if(strlen($built)==0||strlen($signature)==0){
			return false;
		}
		if(strlen($built)!=strlen($signature)){
			return false;
		}
		$result=0;
		for($i=0;$i<strlen($signature);$i++){
			$result|=ord($built{$i})^ord($signature{$i});
		}
		return ($result==0);
	}
	
}