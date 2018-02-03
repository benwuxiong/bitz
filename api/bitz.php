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
		return $this->curl_get_https($url);
	}

	//获取深度
	public function depth($parms){
		$send_data  = $this->getData($parms);
		$url = $this->url.'/depth?'.http_build_query($send_data);
		return $this->curl_get_https($url);
	}

	//成交单
	public function orders($parms){
		$send_data  = $this->getData($parms);
		$url = $this->url.'/orders?'.http_build_query($send_data);
		return $this->curl_get_https($url);
	}

	public function ordersPro($parms){
		$send_data  = $this->getData($parms);
		$url = $this->url.'/ordersPro?'.http_build_query($send_data);
		return $this->curl_get_https($url);
	}

	public function balances(){
		$send_data  = $this->getData();
		$url = $this->url.'/balances?'.http_build_query($send_data);
		return $this->curl_get_https($url);
	}



	//下单
	//$parms=array('type'=>'in','price'=>0.001,'number'=>1,'coin'=>'ltc_btc','tradepwd'=>'***')
	public function tradeAdd($parms)
	{
		$send_data  = $this->getData($parms);
		$url = $this->url.'/tradeAdd';
		return $this->curl_post_https($url,$send_data);
	}

	//我的委托单
	//$parms = array('coin'=>'ltc_btc');
	public function openOrders($parms){
		$send_data  = $this->getData($parms);
		$url = $this->url.'/openOrders';
		return $this->curl_post_https($url,$send_data);
	}

	//撤单
	//$parms = array('id'=>1)
	public function tradeCancel($parms){
		$send_data  = $this->getData($parms);
		$url = $this->url.'/tradeCancel';
		return $this->curl_post_https($url,$send_data);

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

	protected function curl_get_https($url){
	    $curl = curl_init(); // 启动一个CURL会话
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_HEADER, 0);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
	    $tmpInfo = curl_exec($curl);     //返回api的json对象
	    //关闭URL请求
	    curl_close($curl);
	    return $tmpInfo;    //返回json对象
	}


	/* PHP CURL HTTPS POST */
	function curl_post_https($url,$data){ // 模拟提交数据函数
	    $curl = curl_init(); // 启动一个CURL会话
	    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
	    //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
	    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
	    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
	    $tmpInfo = curl_exec($curl); // 执行操作
	    if (curl_errno($curl)) {
	        echo 'Errno'.curl_error($curl);//捕抓异常
	    }
	    curl_close($curl); // 关闭CURL会话
	    return $tmpInfo; // 返回数据，json格式
	}

}
