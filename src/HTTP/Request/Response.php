<?php
/**
 * A class which represents an HTTP Reponse
 *
 */
class PEAR2_HTTP_Request_Response 
{
    /**
     * HTTP Return code
     * @var string
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     */
    public $code = 100;
    
    /**
     * Response headers
     * @var array
     */
    public $headers;

    /**
     * Cookies set in response  
     * @var array
     */
    public $cookies;

    /**
     * Response body
     * @var string
     */
    public $body = '';

    /**
     * Constructor
     *
     *
     * @see $this->body
     * @see $this->headers
     * @see $this->cookies
     */
    public function __construct($details, $body, $headers, $cookies)
    {
        foreach($details as $name => $value) {
            $this->$name = $value;
        }
        
        $this->body    = $body;
        $this->headers = $headers;
        $this->cookies = $cookies;
    }
} 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>
