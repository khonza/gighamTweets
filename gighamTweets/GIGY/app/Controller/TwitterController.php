<?php 
	class TwitterController extends AppController {

	public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session');
	
	public function index() {

	}	

	function updateTweet() {

		App::import('Vendor', 'OAuth/twitteroauth');	
		if ($this->request->is('Post')) {	

		$tweet[0]['category'] = "6";
		$tweet[0]['consumer'] = "8iDndIc69mgh3ZINhNonQ";
		$tweet[0]['consumer_secret'] = "Yd7IImyvpArg54RmgMW8TiLlZEpfkOjgzAiVGcZkfw";
		$tweet[0]['oauth_token'] = "627355725-8lEPbpvxmzZp6Xo1VrOjMS1vwYnj9qe7RXFIef9e";
		$tweet[0]['oauth_secret'] = "mKiOUgw8s2IuEbQsgBHyYrLAeSAO2fl3Erb1PI2AU";

			$tweet = $this->tweetfeed(array('key'=>'4567jklpffg554few4554rffrer5ff3'));
			
			//$tweet = $this->data['Post']['tweet'];

				// define("CONSUMER_KEY", "8iDndIc69mgh3ZINhNonQ");
				// define("CONSUMER_SECRET", "Yd7IImyvpArg54RmgMW8TiLlZEpfkOjgzAiVGcZkfw");
				// define("OAUTH_TOKEN", "627355725-8lEPbpvxmzZp6Xo1VrOjMS1vwYnj9qe7RXFIef9e");
				// define("OAUTH_SECRET", "mKiOUgw8s2IuEbQsgBHyYrLAeSAO2fl3Erb1PI2AU");
	 
				// $connection = new twitteroauth($tweet[0]['consumer'], $tweet[0]['consumer_secret'], 
				// 	$tweet[0]['oauth_token'] , $tweet[0]['oauth_secret']);
				// $content = $connection->get('account/verify_credentials');
				
	 		// 	$connection->post('statuses/update', array('status' => $tweet));

	 		// 	$this->Session->setFlash('Your tweet has sucsessfully been posted.');

					
debug ($tweet);


		}



function tweet_feed($id = null) {
	$this->autoRender = false; 
	$api_ids  = Configure::read('API_KEYS');
	//debug ($api_ids);
	//echo "==========".in_array($this->params['url']['key'], $api_ids);
	
	debug ($this->params);
	
	if (isset($this->params['url']['key']) && in_array($this->params['url']['key'], $api_ids)){

		if (isset($this->params['url']['count'])){
		$limit = $this->params['url']['count'];
		} else {
		$limit = 10;
		}

	//echo " ============".$limit;
	$this->set('limit', $limit);
	$this->Event->recursive = 0;
	//$this->Event->contain('Venue');
	$search_fields = array('Event.id', 'Event.name', 
	'Event.start_date', 'Event.end_date','Event.image',
	'Event.start_time', 'Event.end_time', 
	'Event.description', 
	'Event.ticketurl',  
	'Venue.venue_name', 
	'Venue.building', 
	'Venue.street', 
	'Venue.suburb', 
	'Venue.post_code', 
	'Venue.area', 
	'Venue.latitude', 
	'Venue.longitude', 
	//'EventsUser.remember'
	//'Venue.Area.name', 'Venue.Area.id', 'Venue.Area.restricted'
	);

	//if (isset($id) && $id == "popular" ){
	//$order = 'Event.rank DESC';
	//} else {
	$order = 'Event.interesting DESC, Event.rank DESC';	
	//}	
	
	// check here if it's a date range or a single day 
	// to decided whether to group or not
	if (isset($this->params['url']['start_date']) ){
	$start = $this->params['url']['start_date'];
	} else {
	$start = date('Y-m-d');	
	}

	if (isset($this->params['start_date']) ){
	$end = $this->params['end_date'];
	} else {
	$end = date('Y-m-d', strtotime('+ 1 month'));
	}
	
	
	$day_cond = array(
	$this->eventUtilities->dateLimit($start,$end ),
	);

$cat_cond = array();
if (isset($this->params['url']['category'])){
	$cats = $this->params['url']['category'];
	$this->Event->bindModel(array('hasOne'=>array('CategoriesEvent')), false);
	$cat_cond = $this->CategoryUtilities->getSortCategories($cats);	
	//$cat_cond = array('Event.Categiory.id' => $cats);	
}

$area_cond = array();
if (isset($this->params['url']['area'])){
	$area_cond = array('Venue.area_id' => $this->params['url']['area']);	
	//debug($area_cond);
} 
	

$region_cond['Venue.region_id'] = 1;



$conds[0] = $day_cond;
$conds[1] = $cat_cond;
$conds[2] = $area_cond;
$conds[3] = $region_cond;
$conds[4] = array('Event.flagged'=>0);

if (isset($this->params['url']['twitter'])){
$conds[5] = array('Event.tweeted'=>0);
$limit=10;
} 

	//$this->Event->bindModel(array('hasOne'=>array('CategoriesEvent')), false);
	$events = $this->Event->find('all', 
	array('fields' => $search_fields, 
		'group' => array('Event.name'), 
		'conditions'=> $conds,
		'limit'=> $limit,
		'order' => $order
	));
	// $output['Event'] = $event['Event'];
	// $output['Venue'] = $event['Venue'];
	$this->set('total_filtered_events',$this->totalEventsCount($conds));

for ($i=0; $i< count($events); $i++) { 
  $events[$i]['Event']['url'] = "http://gigham.com/capetown/events/view/".$events[$i]['Event']['id'];
}

$events = Set::sort($events, '{n}.Venue.rank', 'DESC');
$events = Set::sort($events, '{n}.Event.rank', 'DESC');
$events = Set::sort($events, '{n}.Event.start_date', 'ASC');

$this->set('events', $events);

if (isset($this->params['url']['twitter'])){
echo " =========== tweet <br>";
$x = rand(0,9);

$event = $events[$x];
$events = array();
$events [0] = $event;
$event['Event']['tweeted'] = 1;	
$this->Event->save($event);
} 

$events = Set::sort($events, '{n}.Event.start_date', 'ASC');

	//debug($events);
//if (isset($this->params['url']['raw']) ){
//	 $output = $events;
//} else {
//	$output = json_encode($events);
//}
	debug($output);
	//echo $output;
	//return $events;
} else {
	echo 'error';
}

$tweet = $events[0]['Event']['name']." at ". $events[0]['Venue']['venue_name']." on the ".$events[0]['Event']['start_date'];


return $tweet;
//debug($conds);



} //rss

	}
}
?>