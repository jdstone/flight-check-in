<?php
require '../vendor/autoload.php';
require_once("HttpRequest.php");
include("../lib/LIB_http.php");
include("../lib/LIB_parse.php");

// interface ProxyInterface
// class Proxy extends HttpRequest
class Proxy
{
    public $country;
    public $ipAddress;
    public $port;
    // private $res;

    function __construct(string $country, string $ipAddress, string $port)
    {
        $this->country = $country;
        $this->ipAddress = $ipAddress;
        $this->port = $port;
        // $this->getProxyList();
        // self::getProxyList();
    }

    public function getStatusCode()
    {
        echo $res->getStatusCode();
    }

    public function getProxyList()
    {
        $singleProxy;
        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', 'https://free-proxy-list.net');
        $body = $res->getBody();
        echo $body;
        // $httpRequest = new HttpReq("https://free-proxy-list.net");
        // $proxyListReq = $httpRequest->request();

        // $proxyListReqArray = parse_array($proxyListReq, "<tbody>", "</tbody>");
        $proxyListReqArray = parse_array($body, "<tbody>", "</tbody>");
        $row = parse_array($proxyListReqArray[0], "<tr>", "</tr>");

        if (isset($proxyListReqArray) && !empty($proxyListReqArray[0]))
        {
            foreach ($row as $key => $value)
            {
                $tableCells = str_replace("<tr>","", $value);
                $tableCells = str_replace("</tr>","", $tableCells);
                $tableCells = preg_replace("/ class='..'/", "", $tableCells);
                $tableCell = parse_array($tableCells, "<td>", "</td>");
                $country = $tableCell[2]; // US = United States
                $https = $tableCell[6]; // yes / no
                if ($https == "<td>yes</td>" and $country == "<td>US</td>")
                {
                    // $this->ipAddress = return_between($tableCell[0], "<td>", "</td>", "EXCL");
                    // $this->port = return_between($tableCell[1], "<td>", "</td>", "EXCL");
                    $ipAddress = return_between($tableCell[0], "<td>", "</td>", "EXCL");
                    $port = return_between($tableCell[1], "<td>", "</td>", "EXCL");
                    $country = return_between($country, "<td>", "</td>", "EXCL");
                    $https = return_between($https, "<td>", "</td>", "EXCL");
                    // $singleProxy = new Proxy($this->ipAddress, $this->port);
                    // echo nl2br("<b>Proxy:</b> $this->ipAddress:$this->port <b>HTTPS:</b> $https\n");
                }
            }
        }
        // return $singleProxy;
    }
}

