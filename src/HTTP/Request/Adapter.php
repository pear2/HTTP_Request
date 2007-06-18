<?php
abstract class PEAR2_HTTP_Request_Adapter 
{

    public $httpVersion = 'HTTP/1.1';
    public $uri;
    public $verb = 'GET';
    public $headers;
    public $body;
    public $requestTimeout = 10;

    public function sendRequest() 
    {
    }

    protected function parseResponseCode($line) 
    {
        if (sscanf($line, 'HTTP/%s %s', $http_version, $returncode) != 2) {
            throw new PEAR2_HTTP_Request_Exception('Malformed response.');
        } else {
            return array('code' => intval($returncode), 'httpVersion' => $http_version);
        }
    }

    /**
     * Parse a Set-Cookie header to fill $_cookies array
     *
     * @access private
     * @param  string    value of Set-Cookie header
     */
    protected function parseCookie($headervalue)
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

                if ($elName == 'secure') {
                    $cookie['secure'] = true;
                } elseif ($elName == 'expires') {
                    $cookie['expires'] = str_replace('"', '', $elValue);
                } elseif ($elName == 'path' || $elName == 'domain') {
                    $cookie[$elName] = urldecode($elValue);
                } else {
                    $cookie[$elName] = $elValue;
                }
            }
        }
        return $cookie;
    }
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
