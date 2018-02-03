<?php
class bitz
{
	protected $api_key;
	protected $api_secret;
	protected $version  = 'v1';
	protected $base_url = 'https://www.bit-z.com/';
	protected $url = '';
	public function __construct($options = null)
	{
		$this->url = $this->base_url.'api_'.$this->version;
		try {
			if (is_array($options))
			{
				foreach ($options as $option => $value)
				{
					$this->$option = $value;
				}
			}
			else
			{
				return false;
			}
		}
		catch (PDOException $e) {
			throw new Exception($e->getMessage());
		}
	}

	//获取牌价数据
	// array('language'=>'en','coin'=>'ltc_btc');
	public function ticker($parms){
		$send_data  = $this->getData($parms);
		$url = $this->url.'/ticker?'.http_build_query($send_data);
		return $this->http_request($url);
	}

	//获取深度
	public function depth($parms){
		$send_data  = $this->getData($parms);
		$url = $this->url.'/depth?'.http_build_query($send_data);
		return $this->http_request($url);
	}

	//成交单
	public function orders($parms){
		$send_data  = $this->getData($parms);
		$url = $this->url.'/orders?'.http_build_query($send_data);
		return $this->http_request($url);
	}

	public function ordersPro($parms){
		$send_data  = $this->getData($parms);
		$url = $this->url.'/ordersPro?'.http_build_query($send_data);
		return $this->http_request($url);
	}

	public function balances(){
		$send_data  = $this->getData();
		$url = $this->url.'/balances?'.http_build_query($send_data);
		return $this->http_request($url);
	}



	//下单
	//$parms=array('type'=>'in','price'=>0.001,'number'=>1,'coin'=>'ltc_btc','tradepwd'=>'***')
	public function tradeAdd($parms)
	{
		$send_data  = $this->getData($parms);
		$url = $this->url.'/tradeAdd';
		return $this->http_request($url,$send_data);
	}

	//我的委托单
	//$parms = array('coin'=>'ltc_btc');
	public function openOrders($parms){
		$send_data  = $this->getData($parms);
		$url = $this->url.'/openOrders';
		return $this->http_request($url,$send_data);
	}

	//撤单
	//$parms = array('id'=>1)
	public function tradeCancel($parms){
		$send_data  = $this->getData($parms);
		$url = $this->url.'/tradeCancel';
		return $this->http_request($url,$send_data);

	}



	//data_array
	protected function getData($data=null){
		$base_arr = array(
			'api_key'	  =>  $this->api_key,
			'timestamp'   =>  time(),
			'nonce'		  =>  $this->getRandomString(6),
		);
		if(isset($data)){
			$send_data = array_merge($base_arr,$data);
		}else{
			$send_data = $base_arr;
		}
		$send_data['sign'] = $this->getSign($send_data);

		return $send_data;
	}


	//sign
	protected function getSign($data)
	{
		ksort($data);
		$data = http_build_query($data);
		return md5($data.$this->api_secret);
	}



	//随机
	protected function getRandomString($len, $chars=null)
	{
	    if (is_null($chars)){
	        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    }
	    mt_srand(10000000*(double)microtime());
	    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
	        $str .= $chars[mt_rand(0, $lc)];
	    }
	    return $str;
	}

	protected function http_request($url,$data = null){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    if(!empty($data)){
	        curl_setopt($curl,CURLOPT_POST,1);
	        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
	    }
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($curl);
	    curl_close($curl);
	    return $output;
	}

}
