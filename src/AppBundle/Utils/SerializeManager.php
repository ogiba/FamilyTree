<?php
/**
 * Created by PhpStorm.
 * User: ogiba
 * Date: 20.08.2017
 * Time: 15:08
 */

namespace AppBundle\Utils;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SerializeManager
{

    private $serializer;
    /**
     * SerializeManager constructor.
     */
    public function __construct()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function serializeJson($data) {
        $jsonData = $this->serializer->serialize($data, 'json');
        return $jsonData;
    }
    public function deserializeJson($jsonData, $class) {
        $data = $this->serializer->deserialize($jsonData, $class, "json");
        return $data;
    }
}