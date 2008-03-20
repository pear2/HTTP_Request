<?php
class PEAR2_HTTP_Request_Adapter_Peclhttp extends PEAR2_HTTP_Request_Adapter {

	/**
	 * Throws exception if allow_url_fopen is off
	 */
    public function __construct()
	{
        if (!extension_loaded('http')) {
	        throw new PEAR2_HTTP_Request_Exception(
		        'The http extension must be loaded in order to use the Peclhttp adapter'
			);
		}
	}


	
    /**
     * Send the request
     *
     * This function sends the actual request to the
     * remote/local webserver using pecl http
     *
     * @link http://us2.php.net/manual/en/http.request.options.php
     */
    public function sendRequest() 
    {
        $info = array();
        $options = array(
            'connecttimeout'    => $this->timeout,
            'headers'           => $this->headers,
            );

        // if we have any listeners register an onprogress callback
        $options['onprogress'] = array($this,'_onprogress');


        $response = http_request($method,$this->uri,$this->body,$options,$info);
    }	   

    /**
     * Progress handler maps callback progress to listeners
     * @todo implement progress callback
     */
    protected _onprogress($status) {
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
