<?php 
	class TwitterController extends AppController {

	public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session');
	
	public function index() {

	}	

	function updateTweet() {

		App::import('Vendor', 'OAuth/twitteroauth');	
		if ($this->request->is('Post')) {	

			$tweet = $this->data['Post']['tweet'];
				define("CONSUMER_KEY", "8iDndIc69mgh3ZINhNonQ");
				define("CONSUMER_SECRET", "Yd7IImyvpArg54RmgMW8TiLlZEpfkOjgzAiVGcZkfw");
				define("OAUTH_TOKEN", "627355725-8lEPbpvxmzZp6Xo1VrOjMS1vwYnj9qe7RXFIef9e");
				define("OAUTH_SECRET", "mKiOUgw8s2IuEbQsgBHyYrLAeSAO2fl3Erb1PI2AU");
	 
				$connection = new twitteroauth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
				$content = $connection->get('account/verify_credentials');
				
	 			$connection->post('statuses/update', array('status' => $tweet));

	 			$this->Session->setFlash('Your tweet has sucsessfully been posted.');

					



		}
	}
}
?>