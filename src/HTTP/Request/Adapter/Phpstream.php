<?php
/**
 * driver that uses php http:// stream to do requests
 *
 * Loosely Based on PEAR HTTP_Response
 *
 * @version $Revision: 1.52 $
 */
class PEAR2_HTTP_Request_Adapter_PhpStream extends PEAR2_HTTP_Request_Adapter 
{
    /**
     * Throws exception if allow_url_fopen is off
     */
    public function __construct()
    {
        if (ini_get('allow_url_fopen') == false) {
            throw new PEAR2_HTTP_Request_Exception(
                'allow_url_fopen is off, the http:// stream wrapper will not function'
            );
        }
    }


    /**
     * Send the request
     *
     * This function sends the actual request to the
     * remote/local webserver using php streams.
     */
    public function sendRequest() 
    {

        // create context with proper junk
        $ctx = stream_context_create( 
            array(
                $this->uri->protocol => array(
                    'method' => $this->verb,
                    'content' => $this->body,
                    'header' => $this->buildHeaderString(),
                )
            )
        );
        
        ini_set('track_errors', 1);
        $fp = @fopen($this->uri->url, 'rb', false, $ctx);
        if (!$fp) {
            // php sucks
            if (strpos($php_errormsg, 'HTTP/1.1 304')) {
                ini_restore('track_errors');
                $details = (array)$this->uri;

                $details['code'] = '304';
                $details['httpVersion'] = '1.1';

                return new PEAR2_HTTP_Request_Response($details,'',array(),array());
            }
            ini_restore('track_errors');
            throw new PEAR2_HTTP_Request_Exception('Url ' . $this->uri->url . ' could not be opened');
        } else {
            ini_restore('track_errors');
        }

        stream_set_timeout($fp, $this->requestTimeout);
        $body = stream_get_contents($fp);

        if ($body === false) {
            throw new PEAR2_HTTP_Request_Exception(
                'Url ' . $this->uri->url . ' did not return a response'
            );
        }

        $meta = stream_get_meta_data($fp);
        fclose($fp);

        $headers = $meta['wrapper_data'];

        $details = (array)$this->uri;

        $tmp = $this->parseResponseCode($headers[0]);
        $details['code'] = $tmp['code'];
        $details['httpVersion'] = $tmp['httpVersion'];

        $cookies = array();
        $this->headers = $this->cookies = array();
        foreach($headers as $line) {
            $this->_processHeader($line);
        }

        return new PEAR2_HTTP_Request_Response($details,$body,$this->headers,$this->cookies);
    }

   /**
    * Processes the response header
    *
    * @access private
    * @param  string    HTTP header
    */
    private function _processHeader($header)
    {
        if (strpos($header, ':') === false) {
            return;
        }
        
        list($headername, $headervalue) = explode(':', $header, 2);
        $headername  = strtolower($headername);
        $headervalue = ltrim($headervalue);
        
        if ('set-cookie' != $headername) {
            if (isset($this->headers[$headername])) {
                $this->headers[$headername] .= ',' . $headervalue;
            } else {
                $this->headers[$headername]  = $headervalue;
            }
        } else {
            $this->_parseCookie($headervalue);
        }
    }
   /**
    * Parse a Set-Cookie header to fill $_cookies array
    *
    * @access private
    * @param  string    value of Set-Cookie header
    */
    private function _parseCookie($headervalue)
    {
        $cookie = array(
            'expires' => null,
            'domain'  => null,
            'path'    => null,
            'secure'  => false
        );

        // Only a name=value pair
        if (!strpos($headervalue, ';')) {
            $pos = strpos($headervalue, '=');
            $cookie['name']  = trim(substr($headervalue, 0, $pos));
            $cookie['value'] = trim(substr($headervalue, $pos + 1));

        // Some optional parameters are supplied
        } else {
            $elements = explode(';', $headervalue);
            $pos = strpos($elements[0], '=');
            $cookie['name']  = trim(substr($elements[0], 0, $pos));
            $cookie['value'] = trim(substr($elements[0], $pos + 1));

            for ($i = 1; $i < count($elements); $i++) {
                if (false === strpos($elements[$i], '=')) {
                    $elName  = trim($elements[$i]);
                    $elValue = null;
                } else {
                    list ($elName, $elValue) = array_map('trim', explode('=', $elements[$i]));
                }
                $elName = strtolower($elName);
                if ('secure' == $elName) {
                    $cookie['secure'] = true;
                } elseif ('expires' == $elName) {
                    $cookie['expires'] = str_replace('"', '', $elValue);
                } elseif ('path' == $elName || 'domain' == $elName) {
                    $cookie[$elName] = urldecode($elValue);
                } else {
                    $cookie[$elName] = $elValue;
                }
            }
        }
        $this->cookies[] = $cookie;
    }

    /**
     * Buuild header String
     *
     * This method builds the header string
     * to be passed to the request.
     *
     * @return string $out  The headers
     */
    private function buildHeaderString() 
    {
        $out = '';
        foreach($this->headers as $header => $value) {
            $out .= "$header: $value\r\n";
        }
        return $out;
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>
