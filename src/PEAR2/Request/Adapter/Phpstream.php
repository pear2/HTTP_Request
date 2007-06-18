<?php
/**
 * driver that uses php http:// stream to do requests
 *
 * Loosely Based on PEAR Http_Response
 *
 * @author  Elizabeth Marie Smith <auroraeosrose@php.net>
 * @author  Joshua Eichorn <josh@bluga.net>
 * @version $Revision: 1.52 $
 */
class Bluga_Http_Request_Adapter_PhpStream extends Bluga_Http_Request_Adapter {

    /**
     * Throws exception if allow_url_fopen is off
     */
    public function __construct()
    {
        if (ini_get('allow_url_fopen') == false)
        {
            throw new Bluga_Http_Request_Exception('allow_url_fopen is off, the http:// stream wrapper will not function');
        }
    }

    /**
     *@todo make timeouts configurable
     */
    public function sendRequest() {

        // create context with proper junk
        $ctx = stream_context_create( array(
                $this->uri->protocol => array(
                    'method' => $this->verb,
                    'content' => $this->body,
                    'header' => $this->buildHeaderString(),
               )));
        $fp = fopen($this->uri->url, 'rb', false, $ctx);
        if (!$fp) {
           throw new Bluga_Http_Request_Exception('Url ' . $this->uri->url . ' could not be opened');
        }

        stream_set_timeout($fp, 10);
        $body = stream_get_contents($fp);

        if ($body === false) {
           throw new Bluga_Http_Request_Exception('Url ' . $this->uri->url . ' did not return a response');
        }

	$meta = stream_get_meta_data($fp);
	fclose($fp);

	$headers = $meta['wrapper_data'];

	$details = (array)$this->uri;

	$tmp = $this->parseResponseCode($headers[0]);
	$details['code'] = $tmp['code'];
	$details['httpVersion'] = $tmp['httpVersion'];

	$cookies = array();
	foreach($headers as $line) {
		if (preg_match('/^Set-Cookie:/i',$line)) {
			$cookies[] = $this->parseCookie($line);
		}
	}

        return new Bluga_Http_Request_Response($details,$body,$headers,$cookies);
    }

    private function buildHeaderString() {
        $out = '';
        foreach($this->headers as $header => $value) {
            $out .= "$header: $value\r\n";
        }
        return $out;
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>
