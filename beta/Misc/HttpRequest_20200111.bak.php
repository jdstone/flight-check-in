<?php
require_once("Proxy.php");

define('CURL_RETRY',3);

class HttpReq
{
    private $curl;
    private $response;
    public $httpCode;
    protected $url;
    protected $post;
    protected $data;
    protected $proxy;
    protected $webPage;


    public function __construct(string $url, bool $post = FALSE, string $postData = "", bool $proxy = FALSE, bool $referer = FALSE,
      string $refererData = "", bool $header = FALSE, array $headerData = [], bool $encoding = FALSE, string $encodingData = "")
    {
        $this->url = $url;
        $this->post = $post;
        $this->postData = $postData;
        $this->proxy = $proxy;
        $this->referer = $referer;
        $this->refererData = $refererData;
        $this->header = $header;
        $this->headerData = $headerData;
        $this->encoding = $encoding;
        $this->encodingData = $encodingData;
    }

    public function setHttpCode($httpCode)
    {
        $this->httpCode = $httpCode;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function request()
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        curl_setopt($this->curl, CURLOPT_USERAGENT, WEBBOT_NAME);
        if ($this->post === TRUE)
        {
            curl_setopt($this->curl, CURLOPT_POST, $this->post);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->postData);
        }
        if ($this->proxy === TRUE)
        {
            echo "HERE1";
            /* curl_setopt($this->curl, CURLOPT_DNS_USE_GLOBAL_CACHE, FALSE);
            curl_setopt($this->curl, CURLOPT_DNS_CACHE_TIMEOUT, 0); */

            /* $proxyObj = $this->getProxyList();
            $randomNum = rand(0, sizeof($proxyObj)) - 1;
            $randomProxyIP = $proxyObj[$randomNum]->ipAddress;
            $randomProxyPort = $proxyObj[$randomNum]->port;
            print "Random Num: $randomNum\n<br>";
            print "Random ProxyIP: $randomProxyIP\n<br>";
            print "Random ProxyPort: $randomProxyPort\n<br>";
            curl_setopt($this->curl, CURLOPT_PROXY, $randomProxyIP);
            curl_setopt($this->curl, CURLOPT_PROXYPORT, $randomProxyPort); */

            /* $randomProxy = $this->getRandomProxy();
            print "Random ProxyIP: ".$randomProxy["ip"];
            print "Random ProxyPort: ".$randomProxy["port"];
            curl_setopt($this->curl, CURLOPT_PROXY, $randomProxy["ip"]);
            curl_setopt($this->curl, CURLOPT_PROXYPORT, $randomProxy["port"]); */

            $this->setCurlRandomProxy();

            /* curl_setopt($this->curl, CURLOPT_PROXY, "89.187.181.123");
            curl_setopt($this->curl, CURLOPT_PROXYPORT, "3128"); */
            curl_setopt($this->curl, CURLOPT_HTTPPROXYTUNNEL, TRUE);
        }
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, "cookies.txt");
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, "cookies.txt");
        curl_setopt($this->curl, CURLOPT_VERBOSE, TRUE);
        // CURLOPT_CONNECTTIMEOUT - The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);
        // CURLOPT_TIMEOUT - The maximum number of seconds to allow cURL functions to execute.
        // curl_setopt($this->curl, CURLOPT_TIMEOUT, 400); // timeout in seconds

        if ($this->referer === TRUE)
        {
            curl_setopt($this->curl, CURLOPT_REFERER, $this->refererData);
        }
        if ($this->header === TRUE)
        {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headerData);
        }
        if ($this->encoding === TRUE)
        {
            curl_setopt($this->curl, CURLOPT_ENCODING, $this->encodingData);
        }

        // $this->response = curl_exec($this->curl);
        // echo "\n\nTYPE: ".gettype(curl_getinfo($this->curl, CURLINFO_HTTP_CODE))."\n\n";
        /* if ((empty(curl_getinfo($this->curl, CURLINFO_HTTP_CODE))) OR (curl_getinfo($this->curl, CURLINFO_HTTP_CODE) != 0) OR
          (curl_getinfo($this->curl, CURLINFO_HTTP_CODE) != 404))
        // if ((isset(curl_getinfo($this->curl, CURLINFO_HTTP_CODE))) OR (curl_getinfo($this->curl, CURLINFO_HTTP_CODE) != "0"))
        {
            echo "\n\nHTTP STATUS CODE: ".curl_getinfo($this->curl, CURLINFO_HTTP_CODE)."\n\n";
        } */
        // echo "\n\nHTTP STATUS CODE: ".curl_getinfo($this->curl, CURLINFO_HTTP_CODE)."\n\n";
        // $this->httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        // curl_close($this->curl);

        // return $this->response;
        return $this->exec();
    }

    private function exec()
    {
        $this->retryExec();
        $this->httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        echo "\n\nSTATUS: $this->httpCode\n\n";
        while ($this->httpCode == 403)
        {
            echo "HERE2";
            $this->setCurlRandomProxy();
            $this->retryExec();
        }
        $this->httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $response = $this->response;
        curl_close($this->curl);
        return $response;
    }

    private function retryExec()
    {
        $i = 0;
        while ($i < CURL_RETRY)
        {
            $this->response = curl_exec($this->curl);
            // echo "\n\nSTATUS: $this->httpCode\n\n";
            // echo "\n\nSTATUS: ".curl_getinfo($this->curl, CURLINFO_HTTP_CODE)."\n\n";
            if ($this->response) break;
            if ($i < CURL_RETRY) sleep($i);
            $i++;
        }
        echo "\n\nTRIED ".($i+1)." TIME(S)\n\n";
    }

    private function getProxyList()
    {
        $proxyListArray;
        $httpRequest = new HttpReq("https://free-proxy-list.net");
        $proxyListReq = $httpRequest->request();

        $proxyListReqArray = parse_array($proxyListReq, "<tbody>", "</tbody>");
        $row = parse_array($proxyListReqArray[0], "<tr>", "</tr>");

        if (isset($proxyListReqArray) && !empty($proxyListReqArray[0]))
        {
            foreach ($row as $key => $value)
            {
                $tableCells = str_replace("<tr>","",$value);
                $tableCells = str_replace("</tr>","",$tableCells);
                $tableCells = preg_replace("/ class='..'/", "", $tableCells);
                $tableCell = parse_array($tableCells, "<td>", "</td>");
                $country = $tableCell[2]; // US = United States
                $https = $tableCell[6]; // yes / no
                if ($https == "<td>yes</td>" and $country == "<td>US</td>")
                {
                    $ipAddress = return_between($tableCell[0], "<td>", "</td>", "EXCL");
                    $port = return_between($tableCell[1], "<td>", "</td>", "EXCL");
                    $country = return_between($country, "<td>", "</td>", "EXCL");
                    $https = return_between($https, "<td>", "</td>", "EXCL");

                    /* print "ipAddress: $ipAddress\n";
                    print "port: $port\n"; */
                    if ($ipAddress == "" OR $port == "")
                    {
                        throw new Exception("ipAddress and/or port must not be empty.");
                    }

                    $proxyListArray[] = new Proxy($ipAddress, $port);
                }
            }
        }
        return $proxyListArray;
    }

    private function getRandomProxy()
    {
        $proxyObj = $this->getProxyList();

        $randomNum = rand(0, sizeof($proxyObj)) - 1;
        $randomProxyIP = $proxyObj[$randomNum]->ipAddress;
        $randomProxyPort = $proxyObj[$randomNum]->port;

        $proxyIpPortArray = [
          "ip" => $randomProxyIP,
          "port" => $randomProxyPort,
        ];

        return $proxyIpPortArray;
    }

    private function setCurlRandomProxy()
    {
        $proxyObj = $this->getProxyList();

        $randomNum = rand(0, sizeof($proxyObj)) - 1;
        $randomProxyIP = $proxyObj[$randomNum]->ipAddress;
        $randomProxyPort = $proxyObj[$randomNum]->port;
        print "\nipAddress: $randomProxyIP\n";
        print "\nport: $randomProxyPort\n";

        curl_setopt($this->curl, CURLOPT_PROXY, $randomProxyIP);
        curl_setopt($this->curl, CURLOPT_PROXYPORT, $randomProxyPort);
    }
}

