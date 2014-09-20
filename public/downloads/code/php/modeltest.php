<?php
class Modeltest extends CI_Model{
	
	public $date;
	//public $startDay;
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Chongqing');
		$this->date = date('Y-m-d');
		echo "HAHA!!!";
		//$this->startDay =
	}
	//$dateOffset=0，0就是今天，1是明天，-1是昨天類推
	public function getDateOffSet($dateOffset = 0){
		$endDate = date('Y-m-d');
		if($dateOffset != 0){
			$nDay = $dateOffset;
			$unixDate = strtotime($endDate);
			$strStartDate = $nDay.' days';
			$endDate = date('Y-m-d', strtotime($strStartDate, $unixDate));
		}
		return $endDate;
	}
	//$startDate是從$endDate為起始點算的，一般來說必為負數，-5就是$endDate的5天前。參數$endDate格式為'Y-m-d'，目前必須先從getDateOffSet()取得
	public function getStartDate($startDate, $endDate){
		//$endDate=$this->getDateOffSet($dateOffset);	
		$unixDate = strtotime($endDate);
		$strStartDate = $startDate.' days';
		$startDate = date('Y-m-d', strtotime($strStartDate, $unixDate));
		return $startDate;
	}
	//一次取得$endDate,$startDate，回傳的是一個陣列
	public function getStartToEndDates($startDate = -1, $dateOffset = 0){
		$endDate = date('Y-m-d');
		if($dateOffset != 0){
			$nDay = $dateOffset;
			$unixDate = strtotime($endDate);
			$strStartDate = $nDay.' days';
			$endDate = date('Y-m-d', strtotime($strStartDate, $unixDate));
		}
		$unixDate = strtotime($endDate);
		$strStartDate = $startDate.' days';
		$startDate = date('Y-m-d', strtotime($strStartDate, $unixDate));
		$dates['endDate'] = $endDate;
		$dates['startDate'] = $startDate;
		return $dates;
	}
	public function getFeaturedNewsArticles($list_num, $startDate = -7, $dateOffset=0){//撈一週精選美妝新聞
		$endDate = $this->getDateOffSet($dateOffset);
		$startDate = $this->getStartDate($startDate, $endDate);
		//test area start  想拔掉測試hide起來就好 下面參數也就不用改了
		$endDate='2012-05-06';
		$startDate='2012-04-06';
		//test area stop
		$this->db->from('cosme_beautynews');
		$this->db->join('cosme_beautynews_paragraphs', 'cosme_beautynews.news_id=cosme_beautynews_paragraphs.news_id', 'left');
		$this->db->where('news_publishdate >', $startDate);
		$this->db->where('news_publishdate <', $endDate);
		$this->db->limit($list_num);
		$this->db->order_by("news_pageview", "desc"); //照新聞人氣排名
		$query = $this->db->get();
		$data = $query->result_array();
		$return_data = array();
		$return_data['news_data'] = $data;

		return $return_data;
	}
	public function getRecommendReviews($list_Num, $nday){//撈每日最新推薦資料，合併cosme_product
		$crv_seq = $this->getRandomReviewsData();
		$strDay = $nday.' day';		
		$startDate = date("Y-m-d", strtotime($strDay));
		$this->db->from('cosme_review');
		$this->db->join('cosme_product', 'cosme_review.crv_pseq=cosme_product.product_id', 'left');
		foreach ($crv_seq as $value) {
			$this->db->or_where('crv_seq', $value);
		}
		$this->db->limit($list_Num);
		$query = $this->db->get();
		$data = $query->result_array();
		$return_data = array();
		$return_data['reviewData'] = $data;
		return $return_data;
	}
	public function getPollTop($topNumber, $startDate, $dateOffset){ //抓人氣最高的產品心得,時間參數部份已補上，參數部份統一用DAYS去算
		$endDate = $this->getDateOffSet($dateOffset);
		//test area start
		$endDate = '2012-05-06';
		//test area end
		$unixDate = strtotime($endDate);
		$startDate = date('Y-m-d', strtotime("- 2 months", $unixDate));
		$dates = $this->getStartToEndDates($startDate, $dateOffset);
		$endDate = $dates['endDate'];
		$startDate = $dates['startDate'];
		$this->db->from('cosme_review_poll');
		$this->db->select('crv_seq,count(*)');
		$this->db->group_by('crv_seq');
		$this->db->where('createtime >', $startDate);
		$this->db->where('createtime <', $endDate);
		$this->db->limit($topNumber);
		$this->db->order_by('count(*)', 'desc');
		$query = $this->db->get();
		$data = $query->result_array();
		return $data;
	}
	public function getRandomReviewsData($poll_Num=20, $rand_Num=4, $startDate=-30, $dateOffset=0){//從人氣最高的心得做20取4併只回傳crv_id
		$finalData = array();
		$crv_seq = array();
		$firstdata = $this->getPollTop($poll_Num, $startDate, $dateOffset);//隨機取20筆人氣指數最高的資料
		$seconddata = array_rand($firstdata, $rand_Num);//20筆中再取4,但是好像無法只取一個值了，因為第二個參數被設定好，似乎是強迫要大於1
		foreach ($seconddata as $keyValue) {              //4筆資料的陣列
			$finalData[] = $firstdata[$keyValue];	
		}
		foreach ($finalData as $value) {
			$crv_seq[] = $value['crv_seq'];  //再把陣列中的心得編號取出
		}
		return $crv_seq;
	}
	public function getProductsIdByReviewsId(){
		$crv_seq = $this->getRandomReviewsData();	
		$this->db->from('cosme_review');
		$this->db->select('crv_seq, crv_pseq');
		foreach ($crv_seq as $value) {
			$this->db->or_where('crv_seq', $value);
		}	
		$query = $this->db->get();
		$data = $query->result_array();
		return $data;
	}
	public function getProductsSpecial($year, $month, $isOdd=true){//撈新品搶先王所需資料，合併資料表cosme_review,cosme_product
		$product_id = $this->getProductsTopId(5, $year, $month, $isOdd);
		$this->db->from('cosme_product');
		$this->db->join('cosme_review', 'cosme_product.first_review_id = cosme_review.crv_seq', 'left');
		foreach ($product_id as $value) {
			$this->db->or_where('cosme_product.product_id', $value);
		}
		$query = $this->db->get();
		$data = $query->result_array();
		$returnData['reviewData'] = $data;
		return $returnData;
	}
	public function getProductsTop($year, $month, $isOdd = true){//按照邏輯撈當月份新產品60筆，邏輯在這資料表已經先處理過了，$isOdd是判斷奇數，是的話回傳當月首日
		// $date=date('Y-m-d');
		// $dateq=strtotime($date);
		// $startDate=date('Y-m-d',strtotime("- 3 months",$dateq));
		$date = ($isOdd == true) ? '01' : '16';
		$pickup_yearmonth = $year.$month.$date;
		$this->db->from('cosme_newproduct_userank_product_pickup');
		$this->db->where('pickup_yearmonth', $pickup_yearmonth);	
		$query = $this->db->get();
		$return_data = $query->result_array();
		return $return_data;
		}
	public function getProductsTopId($rand_num = 5, $year, $month, $isOdd){//隨機取出5筆，然後只回傳product_id
		$thirdData = array();
		$findalData = array();
		$firstData = $this->getProductsTop($year, $month, $isOdd);
		$secondData = array_rand($firstData, $rand_num);

		//TODO check foreach data
		foreach ($secondData as $keyValue) {
			$thirdData[] = $firstData[$keyValue];
		}
		foreach ($thirdData as $value) {
			$findalData[] = $value['product_id'];
		}
		return $findalData;
	}
	//熱門注目使用心得
	public function getHotReview(){
		$hotReviewId = $this->getHotReviewId();
		$this->db->from('cosme_review');
		$this->db->join('cosme_members', 'cosme_review.crv_memid=cosme_members.cmem_seq', 'left');
		$this->db->where('crv_seq', $hotReviewId);
		$query = $this->db->get();
		$Data = $query->row_array();//單筆資料用的陣列
		$returnData['hotReviewData'] = $Data;
		return $returnData;
	}
	//TODO check this logic
	//排序得地方好像是錯的，要的是一段時間內的PV而非最終的
	public function getTopReview($startDate = -1, $dateOffset = 0){
		$endDate = $this->getDateOffSet($dateOffset);
		$startDate = $this->getStartDate($startDate, $endDate);
		//test area start
		$startDate = '2012-05-01';
		$endDate = '2012-05-02';
		//test area end
		$this->db->from('cosme_review');			
        $this->db->where('crv_date >', $startDate);
		$this->db->where('crv_date <', $endDate);
		$this->db->order_by('review_pageview_final', 'desc');
		$this->db->limit(1);
		$query = $this->db->get();
		$returnData = $query->result_array();
		return $returnData;	
	}
	public function getHotReviewId(){
		$hotReviewData = $this->getTopReview();
		foreach ($hotReviewData as $value) {
			$hotReviewId = $value['crv_seq'];
		}
		return $hotReviewId;
	}
	public function getReviewByPhotos(){
		$reviewId = $this->getTopReviewsIdByPhotos();	
		$picUrls = $this->getPhotosUrl($reviewId);
		$this->db->from('cosme_review');
		$this->db->join('cosme_members','cosme_review.crv_memid=cosme_members.cmem_seq','left');
		$this->db->where('crv_seq',$reviewId);
		$query = $this->db->get();
		$data = $query->result_array();
		$returnData['reviewData'] = $data;
		$returnData['picUrlData'] = $picUrls;
		return $returnData;
	}
	
	//撈出商品圖片前五多的心得
	public function getTopReviewsByPhotos($topNum = 5, $startDate = -1, $dateOffset = 0){
		$endDate = $this->getDateOffSet($dateOffset);
		$startDate = $this->getStartDate($startDate,$endDate);	
		$this->db->from('cosme_review_pic');
		$this->db->join('cosme_review', 'cosme_review_pic.review_id=cosme_review.crv_seq', 'left');
		$this->db->select('review_id, crv_date, count(pic_id)');
		$this->db->where('review_id >', 1);//防範把REVIEW—ID為NULL的也算近來
		$this->db->where('crv_date >', '2012-05-02');
		$this->db->where('crv_date <', '2012-05-03');
		$this->db->group_by('review_id');
		$this->db->order_by('count(pic_id)', 'desc');
		$this->db->limit($topNum);
		$query = $this->db->get();
		$data = $query->result_array();
		return $data;
	}
	//取回最多商品圖片的心得的單個ID
	public function getTopReviewsIdByPhotos(){
		$firstData = $this->getTopReviewsByPhotos();
		$secondData = array_rand($firstData);
		$thirdData =$firstData[$secondData];
		$finalData = $thirdData['review_id'];
		return $finalData;		
	}
	public function getPhotosByReviewId($reviewId){
		//$reviewId=$this->getTopReviewsIdByPhotos();	
		$this->db->from('cosme_review_pic');
		$this->db->select('review_id,pic_id');
		$this->db->where('review_id',$reviewId);
		$query = $this->db->get();
		$returnData = $query->result_array();
		return $returnData;
		
	}
	public function getPhotosIdsByReviewId($reviewId){
		$returnData = array();	
		$picIds = $this->getPhotosByReviewId($reviewId);
		foreach ($picIds as $value) {
			$returnData[] = $value['pic_id'];
		}
		return $returnData;
	}
	public function getPhotosUrl($reviewId){
		if(!isset($reviewId)){
			echo "Warning Message.It might be wrong!!!!Photos didn't response to review!!!<br>";
			$reviewId = $this->getTopReviewsIdByPhotos();
		}
		$returnData = array();	
		$picIds = $this->getPhotosIdsByReviewId($reviewId);
		foreach ($picIds as $picId) {
			$strPicUrl = "http://img2.urcosme.com:901/api/productApi/reviewimage/pic_id/".$picId."/format/json";
			$returnData[] = $this->getUrlArray($strPicUrl);
		}
		return $returnData;
	}
	//撈側邊欄廣告，猜測這裡撈的是活動新幹線的資料
	public function getAdsBanner($startDate = -1,$dateOffset = 0){
		$endDate = $this->getDateOffSet($dateOffset);
		$startDate = $this->getStartDate($startDate,$endDate);
		$this->db->from('cosme_ads_banner');
		$this->db->like('start_date', '2012-05-07', 'after');//撈當天所有時間
		//$this->db->where('start_date','2012-05-07');//只撈當天00：00：00
		$query = $this->db->get();
		$returnData['cosme_ads_banner'] = $query->result_array();
		return $returnData;
	}
    //文藝大大分享的處理方法
 	public function getUrlArray($strUrl){
 		$return_data = array();
		$curl = curl_init();
		//設置相關的url 關的url
		curl_setopt($curl, CURLOPT_URL, $strUrl);
		//設置header
		curl_setopt($curl, CURLOPT_HEADER, 0);
		//要求顯示出螢幕上
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		//執行
		$return_data = curl_exec($curl);
		//關閉連線
        curl_close($curl);
        $return_data = json_decode($return_data,true);
        return $return_data;
    }
}
?>
