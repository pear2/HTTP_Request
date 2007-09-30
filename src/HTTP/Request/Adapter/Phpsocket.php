<?php
class PEAR2_HTTP_Request_Adapter_Phpsocket_Socket {
        public $lineLength = 2048;
        private $_handle;

        public function __construct($handle) {
                $this->_handle = $handle;
        }

        public function readLine() {
                $line = '';
                while(!$this->eof()) {
                        $line .= @fgets($this->_handle, $this->lineLength);
                        if (substr($line, -1) == "\n") {
                                return rtrim($line, "\r\n");
                        }
                }
                return false;
        }

        public function read($size) {
                if ($this->eof()) {
                        return false;
                }
                return @fread($this->_handle,$size);
        }

        public function write($payload) {
                return fwrite($this->_handle,$payload,strlen($payload));
        }

        public function eof() {
                return feof($this->_handle);
        }
}

/**
 * A class which represents an Http Reponse
 * Handles parsing cookies and headers
 *
 * Based on PEAR TTP_Response
 *
 * @version $Revision: 1.52 $
 */
class PEAR2_HTTP_Request_Adapter_Phpsocket extends PEAR2_HTTP_Request_Adapter {

    /**
     * Http Protocol
     * @var string
     */
    public $protocol;
    
    /**
     * HTTP Return code
     * @var string
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     */
    public $code = 100;
    
    /**
     * Cookies set in response  
     * @var array
     */
    private $cookies;

    /**
     * Used by _readChunked(): remaining length of the current chunk
     * @var string
     */
    private $_chunkLength = 0;

    /**
     * Bytes left to read from message-body
     * @var null|int
     */
    private $_toRead = null;

    /**
     * Raw Response to be parsed
     */
    private $_stream;

    public function sendRequest() {
        $payload = $this->_buildHeaders($this->uri->path,$this->uri->host,$this->headers,strlen($this->body));
        $payload .= $this->body;

        $errno    = 0;
        $errstr   = '';
        $handle   = fsockopen($this->uri->host, $this->uri->port, $errno, $errstr, 30);
        stream_set_timeout($handle,10);

        if (!is_resource($handle)) {
            throw new Exception($errstr,$errno);
        }

        $this->_stream = new PEAR2_HTTP_Request_Adapter_Phpsocket_Socket($handle);

        $this->_stream->write($payload);

        $this->parse();

        $details['code'] = $this->code;
        $details['httpVersion'] = $this->protocol;


        return new PEAR2_HTTP_Request_Response($details,$this->body,$this->headers,$this->cookies);

    }

    /**
     * Parse a HTTP response
     * 
     * This extracts response code, headers, cookies and decodes body if it 
     * was encoded in some way
     *
     * @access public
     * @param  bool      Whether to store response body in object property, set
     *                   this to false if downloading a LARGE file and using a Listener.
     *                   This is assumed to be true if body is gzip-encoded.
     * @param  bool      Whether the response can actually have a message-body.
     *                   Will be set to false for HEAD requests.
     * @throws Exception
     * @return boolean     true on success
     */
    public function parse($saveBody = true, $canHaveBody = true)
    {
        do {
            $line = $this->_stream->readLine();
            if (sscanf($line, 'HTTP/%s %s', $http_version, $returncode) != 2) {
                throw new PEAR2_HTTP_Request_Exception('Malformed response.');
            } else {
                $this->protocol = 'HTTP/' . $http_version;
                $this->code     = intval($returncode);
            }
            while ('' !== ($header = $this->_stream->readLine())) {
                $this->_processHeader($header);
            }
        } while ($this->code == 100);

        // RFC 2616, section 4.4:
        // 1. Any response message which "MUST NOT" include a message-body ... 
        // is always terminated by the first empty line after the header fields 
        // 3. ... If a message is received with both a
        // Transfer-Encoding header field and a Content-Length header field,
        // the latter MUST be ignored.
        $canHaveBody = $canHaveBody && $this->code >= 200 && 
                       $this->code != 204 && $this->code != 304;

        // If response body is present, read it and decode
        $chunked = isset($this->headers['transfer-encoding']) && ('chunked' == $this->headers['transfer-encoding']);
        $gzipped = isset($this->headers['content-encoding']) && ('gzip' == $this->headers['content-encoding']);
        $hasBody = false;
        if ($canHaveBody && ($chunked || !isset($this->headers['content-length']) || 
                0 != $this->headers['content-length']))
        {
            if ($chunked || !isset($this->headers['content-length'])) {
                $this->_toRead = null;
            } else {
                $this->_toRead = $this->headers['content-length'];
            }
            while (!$this->_stream->eof() && (is_null($this->_toRead) || $this->_toRead > 0)) {
                if ($chunked) {
                    $data = $this->_readChunked();
                } elseif (is_null($this->_toRead)) {
                    $data = $this->_stream->read(4096);
                } else {
                    $data = $this->_stream->read(min(4096, $this->_toRead));
                    $this->_toRead -= strlen($data);
                }
                if ($data == '') {
                    break;
                } else {
                    $hasBody = true;
                    if ($saveBody || $gzipped) {
                        $this->body .= $data;
                    }
                }
            }
        }

        if ($hasBody) {
            // Uncompress the body if needed
            if ($gzipped) {
                $body = $this->_decodeGzip($this->body);
                if (PEAR::isError($body)) {
                    return $body;
                }
                $this->body = $body;
            }
        }
        return true;
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
    * Read a part of response body encoded with chunked Transfer-Encoding
    * 
    * @access private
    * @return string
    */
    private function _readChunked()
    {
        // at start of the next chunk?
        if (0 == $this->_chunkLength) {
            $line = $this->_stream->readLine();
            if (preg_match('/^([0-9a-f]+)/i', $line, $matches)) {
                $this->_chunkLength = hexdec($matches[1]); 
                // Chunk with zero length indicates the end
                if (0 == $this->_chunkLength) {
                    $this->_stream->readLine(); // make this an eof()
                    return '';
                }
            } else {
                return '';
            }
        }
        $data = $this->_stream->read($this->_chunkLength);
        $this->_chunkLength -= strlen($data);
        if (0 == $this->_chunkLength) {
            $this->_stream->readLine(); // Trailing CRLF
        }
        return $data;
    }

   /**
    * Decodes the message-body encoded by gzip
    *
    * The real decoding work is done by gzinflate() built-in function, this
    * method only parses the header and checks data for compliance with
    * RFC 1952  
    *
    * @access   private
    * @param    string  gzip-encoded data
    * @return   string  decoded data
    */
    private function _decodeGzip($data)
    {
        $length = strlen($data);
        // If it doesn't look like gzip-encoded data, don't bother
        if ($length < 18 || strcmp(substr($data, 0, 2), "\x1f\x8b")) {
            return $data;
        }
        $method = ord(substr($data, 2, 1));
        if ($method != 8) {
            throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): unknown compression method');
        }
        
        $flags = ord(substr($data, 3, 1));

        if ($flags & 224) {
            throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): reserved bits are set');
        }

        // header is 10 bytes minimum. may be longer, though.
        $headerLength = 10;
        // extra fields, need to skip 'em
        if ($flags & 4) {
            if ($length - $headerLength - 2 < 8) {
                throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): data too short');
            }
            
            $extraLength = unpack('v', substr($data, 10, 2));
            if ($length - $headerLength - 2 - $extraLength[1] < 8) {
                throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): data too short');
            }

            $headerLength += $extraLength[1] + 2;
        }
        // file name, need to skip that
        if ($flags & 8) {
            if ($length - $headerLength - 1 < 8) {
                throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): data too short');
            }
            $filenameLength = strpos(substr($data, $headerLength), chr(0));
            if (false === $filenameLength || $length - $headerLength - $filenameLength - 1 < 8) {
                throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): data too short');
            }
            $headerLength += $filenameLength + 1;
        }
        // comment, need to skip that also
        if ($flags & 16) {
            if ($length - $headerLength - 1 < 8) {
                throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): data too short');
            }
            $commentLength = strpos(substr($data, $headerLength), chr(0));
            if (false === $commentLength || $length - $headerLength - $commentLength - 1 < 8) {
                throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): data too short');
            }
            $headerLength += $commentLength + 1;
        }
        // have a CRC for header. let's check
        if ($flags & 1) {
            if ($length - $headerLength - 2 < 8) {
                throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): data too short');
            }
            $crcReal   = 0xffff & crc32(substr($data, 0, $headerLength));
            $crcStored = unpack('v', substr($data, $headerLength, 2));
            if ($crcReal != $crcStored[1]) {
                throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): header CRC check failed');
            }
            $headerLength += 2;
        }
        // unpacked data CRC and size at the end of encoded data
        $tmp = unpack('V2', substr($data, -8));
        $dataCrc  = $tmp[1];
        $dataSize = $tmp[2];

        // finally, call the gzinflate() function
        $unpacked = @gzinflate(substr($data, $headerLength, -8), $dataSize);
        if (false === $unpacked) {
            throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): gzinflate() call failed');
        } elseif ($dataSize != strlen($unpacked)) {
            throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): data size check failed');
        } elseif ($dataCrc != crc32($unpacked)) {
            throw new PEAR2_HTTP_Request_Exception('_decodeGzip(): data CRC check failed');
        }
        return $unpacked;
    }

    private function _buildHeaders($path, $host, $headers,$bodySize) {
        $httpRequest  = "$this->verb $path $this->httpVersion\r\n";
        $httpRequest .= "Host: $host\r\n";
        foreach($headers as $key => $value) {
            $httpRequest .= "$key: $value\r\n";
        }
        if ($bodySize > 0) {
            $httpRequest .= "Content-Length:".$bodySize."\r\n";
        }
        $httpRequest .= "\r\n";

        return $httpRequest;
    }
} // End class PEAR2_HTTP_Response
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>
