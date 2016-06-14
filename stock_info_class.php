<?php
/*
	If using ouput in JSON, html file NEEDS to be in UTF-8 character encoding.

*/
	class Stock{

		private static function getPageData($url, $parameter){
			$full_url = $url . '?';
			if(is_array($parameter)){
				$full_url .= $parameter[0];
				for($i = 1; $i < sizeof($parameter); $i++){
					$full_url .= "&{$parameter[$i]}";
				}
			}else{
				$full_url = "$url?$parameter";
			}
			return (file_get_contents($full_url)) ? file_get_contents($full_url) : false;
		}

		public static function get_price($code, $json = false){
			$res_array = array();
			if(is_array($code)){
				foreach($code as $cd){
					$res_array[] = self::getPriceFromCode($cd);
				}
				return ($json) ? json_encode($res_array, JSON_UNESCAPED_UNICODE) : $res_array;
			}else{
				return ($json) ? json_encode(self::getPriceFromCode($code), JSON_UNESCAPED_UNICODE) : self::getPriceFromCode($code);
			}
		}

		public static function get_code_list($str){
			//echo self::getPageData("http://info.finance.yahoo.co.jp/search/", array("query=イオン", "p=6"));
			return self::GetCodeByName($str);
		}

		private static function getCodeByName($name){
			$url = "http://info.finance.yahoo.co.jp/search/";
			$firstPage = self::getPageData($url, "query={$name}");
			$results = array();
			$pageResults = array();
			$allResults = array();

			//get num of page
			$num_pages = preg_match_all('/&p=(\d)"/', $firstPage, $pageResults);
			//clean the results of page results
			$pageResults2 = array();
			foreach($pageResults[0] as $value){
				$value = str_replace(array("&p=", "\""), "", $value);
				if(!in_array($value, $pageResults2)){
					$pageResults2[] = $value;
				}
			}

			//=================Get Name, Code, Market from all pages======================//

			//preg_match_all('<dt class="price yjXXL">(.*?)</dt>', $)

			echo $firstPage;
			$temp = array();
			//get data from page 1
			preg_match_all('#name highlight">(.*)</span> |<dt class="price yjXXL">(.*)</dt>#', $firstPage, $results);
			$temp = $results[1];
			//preg_match_all('#<dt class="price yjXXL">(.*)</dt>#', $firstPage, $results);
			//var_dump($results);
			var_dump($temp);
			$allResults = $results[1];
/*
			//get data from the rest of pages
			foreach($pageResults2 as $pageNum){
				$pageData = self::getPageData($url, array("query={$name}", "p={$pageNum}"));
				preg_match_all('/name highlight">(.*)/', $pageData, $results);
				$allResults = array_merge($results[1], $allResults);
			}
*/
/*
			//cleaning data
			$output = array();
			foreach($allResults as $res){
				$match = array();
				$match_price = array();
				preg_match_all('#^(.*)</span> <span class="secondInfo"><em class="code highlight">\[(.*)\]</em> <em class="market yjSt">- (.*)</em></span></a>#', $res, $match);
				$ar = array('market' => $match[3][0],'c_name' => $match[1][0], 'code' => $match[2][0]);
				$output[] = $ar;
			}

			var_dump($output);
			//return $allResults;
*/
		}


		private static function getPriceFromCode($code){
			$result = array();
			$url = "http://stocks.finance.yahoo.co.jp/stocks/detail/?code=" . $code . '.T';
			$page = file_get_contents($url);

			//GET symbol
			$symbol = array();
			$price = array();
			$result['code'] = $code;
			if(preg_match('#<th class="symbol"><h1>(.*)</h1></th>#', $page, $symbol)){
				if(preg_match('#<td class="stoksPrice">(.+)</td>#', $page, $price)){
					$result["price"] = $price[1];
				}
				$result["name"] = $symbol[1];
				return $result;
			}else{
				return false;
			}
		}
	}
?>
