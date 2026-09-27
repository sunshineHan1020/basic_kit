<?
//--------------------------------------------------------------------------------------------------
// url
//--------------------------------------------------------------------------------------------------
class url_class {

	//----------------------------------------------------------------------------------------------
	
	/**
	 * 생성자
	 */
	public function __construct() {	}

	//----------------------------------------------------------------------------------------------

	/** 
	 * 현재 url 에서 값을 대치하여 대치한 새로운 url를 반환하는 함수
	 *
	 * @param array replace_array								대치할 값의 배열
	 * @param bool clear										빈 값의 삭제 여부
	 * @return string											변경된 url
	 */
	public function replace_url($replace_array, $clear = TRUE) {
		
		//------------------------------------------------------------------------------------------
		// $_SERVER['QUERY_STRING'] 의 값을 가져와 분리한다.
		//------------------------------------------------------------------------------------------
		$query_string = parse_url('?'.$_SERVER['QUERY_STRING']);
		@$query_string_array_size=0;

		if (isset($query_string['query']) === TRUE) {

			$query_string_array = explode('&', $query_string['query']);
			$query_string_array_size = sizeof($query_string_array);

		}

		$now_query_string_array = array();

		for($i=0;$i<$query_string_array_size;$i=$i+1) {
			
			$temp = explode('=', $query_string_array[$i]);

			if (empty($temp[1]) === FALSE) {
				$now_query_string_array[$temp[0]] = $temp[1];
			}

		}

		return '?'.http_build_query(array_replace($now_query_string_array, $replace_array));

	}

	//----------------------------------------------------------------------------------------------
	
	/**
	 * 파괴자
	 */
	public function __destruct() { }

}
?>