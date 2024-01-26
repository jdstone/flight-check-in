<?php
require '../vendor/autoload.php';
require_once("Proxy.php");
require_once("../vendor/pear/net_ping/Net/Ping.php");

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

define("USER_AGENT", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");

class HttpReq
{
    private $curl;
    private $response;
    public $httpCode;
    private $url;
    protected $post;
    protected $data;
    protected $proxy;
    protected $webPage;

    public function __construct(string $url, string $postData = "", string $refererData = "", array $headerData = [], string $encodingData = "")
    {
        $this->url = $url;
        $this->postData = $postData;
        $this->refererData = $refererData;
        $this->headerData = $headerData;
        $this->encodingData = $encodingData;
    }

    // public function request($url, $jsonData, $referer, $headerData, $encodingData)
    public function request()
    {
        echo "TRYING $this->url REQUEST\n";
        $client = new GuzzleHttp\Client();
        $proxy = $this->getSingleProxy();
        try
        {
            $response = $client->request('POST', $this->url, [
            //   'json' => $this->postData,
              'headers' => [
                'User-Agent' => USER_AGENT
                // 'Referer' => $referer
              ],
              'curl' => [
                CURLOPT_PROXY => $proxy[0]->ipAddress,
                CURLOPT_PROXYPORT => $proxy[0]->port,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_POSTFIELDS => $this->postData,
                CURLOPT_REFERER => $this->refererData,
                CURLOPT_HTTPHEADER => $this->headerData,
                CURLOPT_ENCODING => $this->encodingData
                // CURLOPT_USERAGENT => USER_AGENT
                // CURLOPT_HTTPPROXYTUNNEL => FALSE
              ]
            ]);
            $body = $response->getBody();
        }
        catch (RequestException $e)
        {
            echo Psr7\str($e->getRequest());
            if ($e->hasResponse())
            {
                echo Psr7\str($e->getResponse());
            }
            return false;
        }
        // echo $body;

        /* $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        curl_setopt($this->curl, CURLOPT_USERAGENT, WEBBOT_NAME);
        if ($this->post === TRUE)
        {
            curl_setopt($this->curl, CURLOPT_POST, $this->post);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->postData);
        }

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, "cookies.txt");
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, "cookies.txt");
        curl_setopt($this->curl, CURLOPT_VERBOSE, TRUE);
        // CURLOPT_CONNECTTIMEOUT - The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
        // curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);
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

        $this->response = curl_exec($this->curl);
        // echo "\n\nTYPE: ".gettype(curl_getinfo($this->curl, CURLINFO_HTTP_CODE))."\n\n";
        if ((empty(curl_getinfo($this->curl, CURLINFO_HTTP_CODE))) OR (curl_getinfo($this->curl, CURLINFO_HTTP_CODE) != 0) OR
          (curl_getinfo($this->curl, CURLINFO_HTTP_CODE) != 404))
        // if ((isset(curl_getinfo($this->curl, CURLINFO_HTTP_CODE))) OR (curl_getinfo($this->curl, CURLINFO_HTTP_CODE) != "0"))
        {
            echo "\n\nHTTP STATUS CODE: ".curl_getinfo($this->curl, CURLINFO_HTTP_CODE)."\n\n";
        }
        // echo "\n\nHTTP STATUS CODE: ".curl_getinfo($this->curl, CURLINFO_HTTP_CODE)."\n\n";
        $this->httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);

        // return $this->response;
        return $this->exec();
        return $body; */
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

    public function getProxyWebList()
    {
        $proxyListArray;
        $client = new GuzzleHttp\Client();
        // $res = $client->request('GET', 'https://free-proxy-list.net');
        $res = $client->request('GET', 'https://www.us-proxy.org');
        $body = $res->getBody();
        // echo $body;
        // $httpRequest = new HttpReq("https://free-proxy-list.net");
        // $proxyListReq = $httpRequest->request();

        // $proxyListReqArray = parse_array($proxyListReq, "<tbody>", "</tbody>");
        $proxyListReqArray = parse_array($body, "<tbody>", "</tbody>");
        $row = parse_array($proxyListReqArray[0], "<tr>", "</tr>");

        if (isset($proxyListReqArray) && !empty($proxyListReqArray[0]))
        {
            foreach ($row as $key => $value)
            {
                $tableCells = str_replace("<tr>","",$value);
                $tableCells = str_replace("</tr>","",$tableCells);
                $tableCells = preg_replace("/ class='..'/", "", $tableCells);
                $tableCell = parse_array($tableCells, "<td>", "</td>");
                $country = $tableCell[2]; // US = United States, GB = United Kingdom
                $anonymity = $tableCell[4]; // anonymous, elite proxy, or transparent
                $https = $tableCell[6]; // yes / no
                $last_checked = $tableCell[7]; // "# minutes ago"
                if ($https == "<td>yes</td>" and $country == "<td>US</td>" and ($anonymity == "<td>elite proxy</td>" or $anonymity == "<td>anonymous</td>"))
                // if ($https == "<td>no</td>" and $country == "<td>GB</td>")
                // if ($https == "<td>yes</td>" and ($country == "<td>US</td>" or $country == "<td>GB</td>" or $country == "<td>BR</td>"))
                {
                    // echo "IF<br>";
                    $ipAddress = return_between($tableCell[0], "<td>", "</td>", "EXCL");
                    $port = return_between($tableCell[1], "<td>", "</td>", "EXCL");
                    $country = return_between($country, "<td>", "</td>", "EXCL");
                    $https = return_between($https, "<td>", "</td>", "EXCL");

                    // print "ipAddress: $ipAddress<br>";
                    // print "port: $port<br>";
                    if ($ipAddress == "" OR $port == "" OR $country == "")
                    {
                        throw new Exception("ipAddress and/or port must not be empty.");
                    }

                    $proxyListArray[] = new Proxy($country, $ipAddress, $port);
                }
                else if ($https == "<td>yes</td>" and $country == "<td>BR</td>")
                {
                    echo "ELSE<br>";
                    $ipAddress = return_between($tableCell[0], "<td>", "</td>", "EXCL");
                    $port = return_between($tableCell[1], "<td>", "</td>", "EXCL");
                    $country = return_between($country, "<td>", "</td>", "EXCL");
                    $https = return_between($https, "<td>", "</td>", "EXCL");

                    // print "ipAddress: $ipAddress<br>";
                    // print "port: $port<br>";
                    if ($ipAddress == "" OR $port == "" OR $country == "")
                    {
                        throw new Exception("ipAddress and/or port must not be empty.");
                    }

                    $proxyListArray[] = new Proxy($country, $ipAddress, $port);
                }
            }
        }
        return $proxyListArray;
    }

    public function getRandomProxy()
    {
        $proxyObj = $this->getProxyWebList();

        echo "SIZE: ".sizeof($proxyObj)."\n";
        $randomNum = rand(0, (sizeof($proxyObj)));
        echo "Random Num: ".$randomNum."\n";

        if ($randomNum == 0)
        {
            $randomProxyIP = $proxyObj[$randomNum]->ipAddress;
            $randomProxyPort = $proxyObj[$randomNum]->port;
            // echo "IF<br>";
        }
        else
        {
            $randomProxyIP = $proxyObj[$randomNum - 1]->ipAddress;
            $randomProxyPort = $proxyObj[$randomNum - 1]->port;
            // echo "ELSE<br>";
        }

        $proxyIpPortArray = [
          "ip" => $randomProxyIP,
          "port" => $randomProxyPort,
        ];

        return $proxyIpPortArray;
    }

    public function getProxyList()
    {
        $fastProxyArray;
        $proxyWebListObj = $this->getProxyWebList();

        for ($i = 0; $i < sizeof($proxyWebListObj); $i++)
        {
            echo "IP Address: ".$proxyWebListObj[$i]->ipAddress."\n";
            echo "Country: ".$proxyWebListObj[$i]->country."\n";
            echo "Port: ".$proxyWebListObj[$i]->port."\n\n";
            // echo $value[0]->$value;
            // var_dump($value);
            // if ($this->testProxy($proxyWebListObj[$i]->ipAddress, $proxyWebListObj[$i]->port))
            // {
            //     $fastProxyArray[] = $proxyWebListObj[$i];
            // }
        }
        // Test proxy and return $proxyObj
        // return $proxyOjb;
        return $proxyWebListObj;
    }

    public function getSingleProxy()
    {
        $fastProxyArray;

        $proxyWebListObj = $this->getProxyList();

        // echo $proxyWebListObj[0]->ipAddress."<br><br>";
        // echo $proxyWebListObj[1]->ipAddress."<br><br>";
        /* foreach ($proxyWebListObj as $obj)
        {
            echo "IP Address: ".$obj->ipAddress."<br>";
            echo "Country: ".$obj->country."<br>";
            echo "Port: ".$obj->port."<br><br>";
            // echo $value[0]->$value;
            // var_dump($value);
            if ($this->testProxy($obj->ipAddress, $obj->port))
            {
                $fastProxyArray[] = $obj;
            }
        } */
        for ($i = 0; $i < sizeof($proxyWebListObj); $i++)
        // for ($i = 0; $i < 5; $i++)
        {
            echo "\n\n";
            echo "IP Address: ".$proxyWebListObj[$i]->ipAddress."\n";
            echo "Country: ".$proxyWebListObj[$i]->country."\n";
            echo "Port: ".$proxyWebListObj[$i]->port."\n\n";
            // echo $value[0]->$value;
            // var_dump($value);
            if ($this->testProxy($proxyWebListObj[$i]->ipAddress, $proxyWebListObj[$i]->port))
            {
                $fastProxyArray[] = $proxyWebListObj[$i];
            }
        }

        /* $randomNum = rand(0, sizeof($proxyObj)) - 1;
        $randomProxyIP = $proxyObj[$randomNum]->ipAddress;
        $randomProxyPort = $proxyObj[$randomNum]->port;

        $proxyIpPortArray = [
          "ip" => $randomProxyIP,
          "port" => $randomProxyPort,
        ]; */

        /* if (count($proxyWebListObj) > 0)
        {
            return $proxyWebListObj;
        } */

        return $fastProxyArray;
    }

    /* public function testProxy($ipAddress, $port)
    {
        // test the proxy IP and port and if good, return it.
        // otherwise, throw and exception.
        $siteResponseTime = $this->ping($ipAddress);
        echo "Response Time: ".$siteResponseTime."<br><br>";
        // $res->ping("google.com")."<br><br>";

        if ($siteResponseTime != -1)
        if ($siteResponseTime < 250)
        {
            $client = new GuzzleHttp\Client();
            // $res = $client->request('GET', 'https://ifconfig.co/ip', ['proxy' => $ipAddress.':'.$port]);
            // $res = $client->request('GET', 'https://ifconfig.me/ip', ['proxy' => $ipAddress.':'.$port]);
            echo "Using Proxy IP: ".$ipAddress.":".$port."<br><br>";

            echo "HERE1";
            // $res = $client->request('GET', 'https://www.google.com', ['proxy' => $ipAddress.':'.$port, 'timeout' => 5]);
            // $res = $client->request('GET', 'https://www.google.com');
            $res = $client->request('GET', 'https://ifconfig.me/ip', [
              'debug' => true,
            //   'timeout' => 30,
              'curl' => [
                CURLOPT_PROXY => $ipAddress,
                CURLOPT_PROXYPORT => $port
                // CURLOPT_HTTPPROXYTUNNEL => FALSE
              ]
            ]);
            // $body = $res->getBody();
            // echo $body;
            return true;

            // if ($body)
            // {
            //     // return true;
            //     echo "INPUT IP Address: ".$ipAddress;
            //     echo $body;
            // }
            // throw new Exception("Invalid IP ADDRESS");
        }
        else
        {
            throw new Exception("Proxy is down, or is too slow.", 1);
        }
        return false;
    } */

    public function testProxy($ipAddress, $port)
    {
        // test the proxy IP and port and if good, return it.
        // otherwise, throw and exception.
        $siteResponseTime = $this->ping($ipAddress);
        if (isset($siteResponseTime))
        {
            echo "Response Time: ".$siteResponseTime."\n\n";
        }
        // $response->ping("google.com")."<br><br>";

        // if ($siteResponseTime != -1)
        if ($siteResponseTime < 250)
        {
            $handlerStack = HandlerStack::create(new CurlHandler());
            $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
            $client = new Client(array('handler' => $handlerStack));
            // $client = new GuzzleHttp\Client();
            // $response = $client->request('GET', 'https://ifconfig.co/ip', ['proxy' => $ipAddress.':'.$port]);
            // $response = $client->request('GET', 'https://ifconfig.me/ip', ['proxy' => $ipAddress.':'.$port]);
            echo "Using Proxy IP: ".$ipAddress.":".$port."\n\n";

            // echo "HERE1";
            // $response = $client->request('GET', 'https://www.google.com', ['proxy' => $ipAddress.':'.$port, 'timeout' => 5]);
            // $response = $client->request('GET', 'https://www.google.com');
            try
            {
                $response = $client->request('GET', 'https://ifconfig.me/ip', [
                  'debug' => true,
                  'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36',
                  ],
                //   'timeout' => 10,
                  'curl' => [
                    CURLOPT_PROXY => $ipAddress,
                    CURLOPT_PROXYPORT => $port,
                    CURLOPT_CONNECTTIMEOUT => 5
                    // CURLOPT_HTTPPROXYTUNNEL => FALSE
                  ]
                // ])->getBody()->getContents();
                ]);
                return true;
            }
            catch (RequestException $e)
            {
                echo Psr7\str($e->getRequest());
                if ($e->hasResponse())
                {
                    echo Psr7\str($e->getResponse());
                }
                return false;
            }
            // $body = $response->getBody();
            // echo $body;
            // return \GuzzleHttp\json_decode($response, true);
            // echo "Returning TRUE...";
            // return true;
        }
        // return false;
    }

    public function retryDecider()
    {
        return function (
          $retries,
          Request $request,
          Response $response = null,
          RequestException $exception = null
        )
        {
            // limit the number of retries to NUMBER
            if ($retries >= 2)
            {
                return false;
            }

            // retry connection exceptions
            if ($exception instanceof ConnectException)
            {
                return true;
            }

            if ($response)
            {
                // retry on server errors
                if ($response->getStatusCode() >= 500 )
                {
                    return true;
                }
            }

            return false;
        };
    }

    public function retryDelay()
    {
        return function ($numberOfRetries)
        {
            return 1000 * $numberOfRetries;
        };
    }

    // function to check response time
    function pingDomain($domain)
    {
        $starttime = microtime(true);
        $file      = fsockopen($domain, 80, $errno, $errstr, 10);
        $stoptime  = microtime(true);
        $status    = 0;

        if (!$file) $status = -1;  // site is down
        else
        {
            fclose($file);
            $status = ($stoptime - $starttime) * 1000;
            $status = floor($status);
        }
        return $status;
    }

    // function to check response time
    function ping($domain)
    {
        $ping = Net_Ping::factory();
        if (PEAR::isError($ping))
        {
            return $ping->getMessage();
        }
        else
        {
            $ping->setArgs(array('count' => 2, 'timeout' => 5));
            // var_dump($ping->ping('google.com');
            return $ping->ping($domain)->getAvg();
        }
    }
}

