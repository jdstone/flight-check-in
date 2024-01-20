<?php

namespace Karriere\JsonDecoder;
require_once '../vendor/autoload.php';

class Json
{
    function __construct()
    {
        
    }

    public function createJsonObject()
    {
        
    }
}

class Person
{
    public $id;
    public $name;

    public function decodeData()
    {
        $jsonDecoder = new JsonDecoder();
        $jsonData = '{"id": 1, "name": "Johnny 5"}';

        $person = $jsonDecoder->decode($jsonData, Person::class);

        return $person;
    }
}

$john = new Person();

echo $john->decodeData()->id."<br>";
echo $john->decodeData()->name;

