<?php
/**
 * class to do http requests, uses a adapter based system for performing those requests
 *
 * Loosely Based on PEAR HTTP_Response
 *
 * @author  Elizabeth Marie Smith <auroraeosrose@php.net>
 * @author  Joshua Eichorn <josh@bluga.net>
 * @version $Id$
 */
class PEAR2_HTTP_Request 
{

    /**
     * The adapter that the requester uses.
     *
     * @see adapters
     */
    protected $adapter;

    /**
     * Magic to retrieve items that are actually stored in the adapter
     *
     * @param  string $name name of var to get
     */
    public function __get($name)
    {
        if (isset($this->adapter->$name)) {
            return $this->adapter->$name;
        }
    }

    /**
     * Magic to set items that are actually stored in the adapter
     *
     * @param  string $name name of var to set
     * @param  mixed $value to give to var
     */
    public function __set($name, $value)
    {
        switch($name) {
            case 'verb':
                $this->adapter->verb = strtoupper($value);
                break;
            case 'uri':
            case 'url':
                $this->adapter->uri = new Net_URL2($value);
                break;
            case 'body':
            case 'content':
                if (is_array($value)) {
                    $this->adapter->body = http_build_query($value);
                    $this->setHeader('Content-Type','application/x-www-form-urlencoded');
                    if ($this->adapter->verb == 'GET') {
                        $this->adapter->verb = 'POST';
                    }
                } else {
                    $this->adapter->body = $value;
                }
                break;
            case 'requestTimeout':
                $this->adapter->$name = (int)$value;
            default:
                $this->adapter->$name = $value;
                break;
        }
    }

    /**
     * sets up the adapter
     *
     * @param  string $class adapter to use
     */
    public function __construct($url = null, $class = null) 
    {
        if (!is_null($class) && $class instanceof PEAR2_HTTP_Request_Adapter) {
            $this->adapter = new $class;
        } elseif (extension_loaded('pecl_http')) {
            $this->adapter = new PEAR2_HTTP_Request_Adapter_Http;
        } elseif (ini_get('allow_url_fopen') == true) {
            $this->adapter = new PEAR2_HTTP_Request_Adapter_Phpstream;
        } else {
            $this->adapter = new PEAR2_HTTP_Request_Adapter_Phpsocket;
        }

        if ($url) {
            $this->url = $url;
        }
    }

    /**
     * asks for a response class from the adapter
     *
     * @param  string $class adapter to use
     * @return mixed  The response from the adapter
     */
    public function sendRequest() 
    {
        $response = $this->adapter->sendRequest();
        return $response;
    }

    /**
     * Setter for request headers
     * 
     * @see $this->adapter->headers
     */
    public function setHeader($header, $value) 
    {
        $this->adapter->headers[$header] = $value;
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>
