<?php 
namespace KWT\Yahoo\BossBundle\Service;

use KWT\Yahoo\BossBundle\Core\Util;
use KWT\Yahoo\BossBundle\Core\Request;
use KWT\Yahoo\BossBundle\Core\MethodHMACSHA1;
use KWT\Yahoo\BossBundle\Exception\YahooBossApiException;

class BOSS{
	private $_api_url='http://yboss.yahooapis.com/geo/placefinder';
	private $_consumer=null;
	private $_url=null;
	private $_headers=null;
	
	public function __construct($container){
		$this->_consumer=new \stdClass();
		$this->_consumer->key=$container->getParameter('kwt_yahoo_boss_key');
		$this->_consumer->secret=$container->getParameter('kwt_yahoo_boss_secret');
		if($container->hasParameter('kwt_yahoo_boss_callback')){
			$this->_consumer->callback_url=$container->getParameter('kwt_yahoo_boss_callback');
		}
	}
	
	public function callService($query){
		$request=Request::from_consumer_and_token($this->_consumer,null,'GET',$this->_api_url,array('q'=>$query));
		$request->sign_request(new MethodHMACSHA1(),$this->_consumer,null);
		$this->_url=sprintf('%s?%s',$this->_api_url,Util::build_http_query(array('q'=>$query)));
		$this->_headers=array($request->to_header());
		return $this->getResult($request);
	}
	
	protected function getResult(Request $request){
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_HTTPHEADER,$this->_headers);
		curl_setopt($curl,CURLOPT_ENCODING,'gzip');
		curl_setopt($curl,CURLOPT_URL,$this->_url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		if(($response=curl_exec($curl))==false){
			throw new YahooBossApiException('Connection error');
		}
		/*$response=$this->xmlToJson($response);
		if(isset($response->error)){
			throw new YahooBossApiException($response->error->description.': '.$response->error->detail);
		}*/
		return $response;
	}
	
	protected function xmlToJson($xml){
		$contents=str_replace(array("\n","\r","\t"),'',$xml);
		$contents=trim(str_replace('"',"'",$contents));
		$sx=simplexml_load_string($contents);
		$json=json_encode($sx);
		return $json;
	}
	
}