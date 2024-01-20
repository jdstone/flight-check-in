<?php
require_once("HttpRequest.php");

$res = new HttpReq();
// $proxy_list = $res->getProxyWebList();
// echo $proxy_list[0]->$port;
// $proxyList1 = $res->getRandomProxy();

// echo $proxyList1[0];

$randomProxy = $res->getRandomProxy();
// echo "Response time: ".$res->pingDomain("google.com")."<br>";
// $res->ping("google.com")."<br><br>";
print "Random ProxyIP: ".$randomProxy["ip"]."<br>";
print "Random ProxyPort: ".$randomProxy["port"]."<br><br>";

// $proxyArray = $res->getProxyList();
$proxy = $res->getSingleProxy();
// echo $proxyArray[0]->ipAddress;
// echo gettype($proxyArray)."<br><br>";
// echo "TYPE: ".gettype($proxy)."<br><br>";
foreach ($proxy as $key => $value)
{
    echo $key." Proxy: ".$proxy[$key]->ipAddress."<br><br>";
}
// echo "1st Proxy: ".$proxy[0]->ipAddress."<br><br>";
// echo $proxyArray[1]->ipAddress."<br><br>";
// for ($i = 0; $i <= )
/* foreach ($proxyArray as $obj)
{
    echo "IP Address: ".$obj->ipAddress."<br>";
    echo "Country: ".$obj->country."<br>";
    echo "Port: ".$obj->port."<br><br>";
    // $res->testProxy($obj->ipAddress, $obj->port);
    // echo $value[0]->$value;
    // var_dump($value);
} */

// echo $res->testProxy('191.37.183.209', '60139');

