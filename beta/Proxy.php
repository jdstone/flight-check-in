<?php
require_once("HttpRequest.php");
include("../lib/LIB_http.php");
include("../lib/LIB_parse.php");

class Proxy
{
  public $ipAddress;
  public $port;

  function __construct(string $ipAddress, string $port)
  {
    $this->ipAddress = $ipAddress;
    $this->port = $port;
  }

  public function getProxyList()
  {
    $singleProxy;
    $httpRequest = new HttpReq("https://free-proxy-list.net");
    $proxyListReq = $httpRequest->request();

    $proxyListReqArray = parse_array($proxyListReq, "<tbody>", "</tbody>");
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
          $ipAddress = return_between($tableCell[0], "<td>", "</td>", "EXCL");
          $port = return_between($tableCell[1], "<td>", "</td>", "EXCL");
          $country = return_between($country, "<td>", "</td>", "EXCL");
          $https = return_between($https, "<td>", "</td>", "EXCL");
        }
      }
    }
  }
}
