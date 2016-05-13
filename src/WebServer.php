<?php namespace Buonzz\Scalp;

use Buonzz\Scalp\ElasticServer;

class WebServer{
	private $shtml = '';

	public function serve($id){

		if(!is_null($id))
		{
			$client = ElasticServer::build_client();
			
			$data = ElasticServer::get_thumb_by_fileid($client,$id);

			$this->shtml = '<html><head><style>*{color:#fff;}</style>
				</head><body><img src="data:image/jpg;base64,'. $data.'"/></body>
			</html>';
		}
		else
			$this->shtml = '<html><head><style>*{color:#fff;}</style></head><body><p style="color:#000;">Pass an id as query string</p></body></html>';	
		
		return $this->shtml;
	}
}