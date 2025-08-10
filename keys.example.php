<?php
// Identificador de su tienda
define("USERNAME", "88053210");

// Clave de Test o Producción
define("PASSWORD", "testpassword_6gRP1UzvaR3cISrdaDXCxkS8UCnDZ0bLrfHkEGUUoMaNr");

// Clave Pública de Test o Producción
define("PUBLIC_KEY","88053210:testpublickey_dCjlTRaikHpm9sryZllbtWKYPAveQZ9IGz2eiKZyP29nw");

// Clave HMAC-SHA-256 de Test o Producción
define("HMAC_SHA256","5zxLSAF4NswNPip9WRG0r7DlTCsqavAqOsTsp4wLAHXpl");

function formToken(){
    // URL de Web Service REST
    $url = "https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment";

    // Encabezado Basic con concatenación de "usuario:contraseña" en base64
    $auth = USERNAME.":".PASSWORD;

    $headers = array(
        "Authorization: Basic " . base64_encode($auth),
        "Content-Type: application/json"
    );

    $body = [
        "amount" => $_POST["amount"] * 1,
        "currency" => $_POST["currency"],
        "orderId" => $_POST["orderId"],
        "customer" => [
          "email" => $_POST["email"],
          "billingDetails" => [
            "firstName"=>  $_POST["firstName"],
            "lastName"=>  $_POST["lastName"],
            "phoneNumber"=>  $_POST["phoneNumber"],
            "identityType"=>  $_POST["identityType"],
            "identityCode"=>  $_POST["identityCode"],
            "address"=>  $_POST["address"],
            "country"=>  $_POST["country"],
            "city"=>  $_POST["city"],
            "state"=>  $_POST["state"],
            "zipCode"=>  $_POST["zipCode"],
          ]
        ],
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $raw_response = curl_exec($curl);

    $response = json_decode($raw_response , true);

    $formToken = $response["answer"]["formToken"];

    return $formToken;
}

function checkHash($key){
    $krAnswer = str_replace('\/', '/',  $_POST["kr-answer"]);

    $calculateHash = hash_hmac("sha256", $krAnswer, $key);

    return ($calculateHash == $_POST["kr-hash"]) ;
}
