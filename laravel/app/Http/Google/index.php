<?php

require_once 'api/vendor/autoload.php';

$client = new Google\Client();

$client->setAuthConfig('content-api-key.json');

$client->addScope(Google_Service_ShoppingContent::CONTENT);

$service = new Google_Service_ShoppingContent($client);

$product = new Google_Service_ShoppingContent_Product();
$product->setOfferId('book123');
$product->setTitle('A Tale of Two Cities');
$product->setDescription('A classic novel about the French Revolution');
$product->setLink('http://my-book-shop.com/tale-of-two-cities.html');
$product->setImageLink('http://my-book-shop.com/tale-of-two-cities.jpg');
$product->setContentLanguage('en');
$product->setTargetCountry('GB');
$product->setChannel('online');
$product->setAvailability('in stock');
$product->setCondition('new');
$product->setGoogleProductCategory('Media > Books');
$product->setGtin('9780007350896');

$price = new Google_Service_ShoppingContent_Price();
$price->setValue('2.50');
$price->setCurrency('GBP');

$shipping_price = new Google_Service_ShoppingContent_Price();
$shipping_price->setValue('0.99');
$shipping_price->setCurrency('GBP');

$shipping = new Google_Service_ShoppingContent_ProductShipping();
$shipping->setPrice($shipping_price);
$shipping->setCountry('GB');
$shipping->setService('Standard shipping');

$shipping_weight = new Google_Service_ShoppingContent_ProductShippingWeight();
$shipping_weight->setValue(200);
$shipping_weight->setUnit('grams');

$product->setPrice($price);
$product->setShipping(array($shipping));
$product->setShippingWeight($shipping_weight);

$result = $service->products->insert(360703426, $product);

var_dump($result);

?>