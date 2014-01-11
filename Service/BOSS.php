<?php 
namespace KWT\Yahoo\BossBundle\Service;

use KWT\Yahoo\BossBundle\Core\Util;
use KWT\Yahoo\BossBundle\Core\Request;
use KWT\Yahoo\BossBundle\Core\MethodHMACSHA1;
use KWT\Yahoo\BossBundle\Exception\YahooBossApiException;

class BOSS{
	private $_api_url='http://yboss.yahooapis.com/ysearch/news,web,images';
	private $_consumer=null;
	private $_url=null;
	private $_headers=null;
	
	public function __construct($container){
		$this->_consumer=new \stdClass();
		$this->_consumer->key=$container->getParameter('kwt_yahoo_boss_key');
		$this->_consumer->secret=$container->getParameter('kwt_yahoo_boss_secret');
		if($container->hasParameter('kwt_yahoo_boss_api_url')){
			$this->_api_url=$container->getParameter('kwt_yahoo_boss_api_url');
		}
		if($container->hasParameter('kwt_yahoo_boss_callback')){
			$this->_consumer->callback_url=$container->getParameter('kwt_yahoo_boss_callback');
		}
	}
	
	public function callService(array $args){
		if(!isset($args['format'])){
			$args['format']='json';
		}
		$request=Request::from_consumer_and_token($this->_consumer,null,'GET',$this->_api_url,$args);
		$request->sign_request(new MethodHMACSHA1(),$this->_consumer,null);
		$this->_url=sprintf('%s?%s',$this->_api_url,Util::build_http_query($args));
		$this->_headers=array($request->to_header());
		$this->getResult($request);
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
		$response=json_decode($response);
		if(isset($response->error)){
			throw new YahooBossApiException($response->error->description.': '.$response->error->detail);
		}
		return $response;
	}
	
}