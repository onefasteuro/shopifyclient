<?php
	
namespace onefasteuro\ShopifyClient;
	


abstract class AbstractClient
{
    protected $token;
    protected $session = null;

    public function __construct(\Requests_Session $client)
    {
        $this->session = $client;
    }

    public function token()
	{
		return $this->token;
	}

    public function setToken($t)
    {
        $this->token = $t;
        return $this;
    }

    abstract protected function endpoint();

    protected function preparePayload($gql, $variables = [])
    {
        $send = (count($variables) > 0) ? ["query" => $gql, "variables" => $variables] : ["query" => $gql];
        $send_payload = json_encode($send);

        return $send_payload;
    }

    public function session()
    {
        return $this->session;
    }

    abstract protected function transport($send_payload);


	public function query($gql, $variables = [])
	{
	    //client not ready
	    if(!$this->session instanceof \Requests_Session) {

        }

		$send_payload = $this->preparePayload($gql, $variables);

		return $this->transport($send_payload);
	}

	public static function parse(\Requests_Response $response)
    {
        return json_decode($response->body, true);
    }
}
