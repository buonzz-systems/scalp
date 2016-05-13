<?php namespace Buonzz\Scalp;

use Elasticsearch\ClientBuilder;
use Monolog\Logger;

class WebServer{
	private $shtml = '';

	public function serve(){
		$this->shtml = '<html><head><style>*{color:#fff;}</style></head><body>Hello</body></html>';
		return $this->shtml;
	}
}