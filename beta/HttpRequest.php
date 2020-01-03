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
      $this->setCurlRandomProxy();

      curl_setopt($this->curl, CURLOPT_HTTPPROXYTUNNEL, TRUE);
    }
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($this->curl, CURLOPT_COOKIEJAR, "cookies.txt");
    curl_setopt($this->curl, CURLOPT_COOKIEFILE, "cookies.txt");
    curl_setopt($this->curl, CURLOPT_VERBOSE, TRUE);
    // CURLOPT_CONNECTTIMEOUT - The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
    curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);

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

    return $this->exec();
  }

  private function exec()
  {
    $this->retryExec();
    $this->httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    while ($this->httpCode == 403)
    {
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
      if ($this->response) break;
      if ($i < CURL_RETRY) sleep($i);
      $i++;
    }
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

    curl_setopt($this->curl, CURLOPT_PROXY, $randomProxyIP);
    curl_setopt($this->curl, CURLOPT_PROXYPORT, $randomProxyPort);
  }
}
