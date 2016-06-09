<?php
/*
	If using ouput in JSON, html file NEEDS to be in UTF-8 character encoding.

*/
	class Stock{

		public static function get_price($code, $json = false){
			$res_array = array();
			if(is_array($code)){
				foreach($code as $cd){
					$res_array[] = self::getPriceByCode($cd);
				}
				return ($json) ? json_encode($res_array, JSON_UNESCAPED_UNICODE) : $res_array;
			}else{
				return ($json) ? json_encode(self::getPriceByCode($code), JSON_UNESCAPED_UNICODE) : self::getPriceByCode($code);
			}
		}

		public static function get_code_list($str){
			return self::GetCodeByName($str);
		}

		private static function getCodeByName($name){
			$url = "http://info.finance.yahoo.co.jp/search/?query={$name}";
			$page = file_get_contents($url);
			$results = array();
			$pageResults = array();
			$num_pages = preg_match_all('/&p=(\d)"/', $page, $pageResults);
			$pageResults2 = array();
			foreach($pageResults[0] as $value){
				$pageResults2[] = str_replace(array("&p=", "\""), "", $value);
			}

			//echo preg_match_all('/name highlight">(.*)</', $page, $results);
			//var_dump($pageResults2);
			//needs to get all the results for each page
		}
		private static function getPriceByCode($code){
			$result = array();
			$url = "http://stocks.finance.yahoo.co.jp/stocks/detail/?code=" . $code . '.T';
			$page = file_get_contents($url);

			//GET symbol
			$symbol = array();
			$price = array();
			if(preg_match('#<th class="symbol"><h1>(.*)</h1></th>#', $page, $symbol)){
				if(preg_match('#<td class="stoksPrice">(.+)</td>#', $page, $price)){
					$result[$code]["price"] = $price[1];
				}
				$result[$code]["name"] = $symbol[1];
				return $result;
			}else{
				return false;
			}
		}
	}
?>
