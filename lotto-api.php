<?php

namespace Lotto;

/**
 * Klient do publicznego API Lotto.
 *
 * Umożliwia wykonywanie zapytań GET do endpointów API Lotto
 * (https://developers.lotto.pl/api/open/v1/) z obsługą klucza API oraz błędów HTTP i sieciowych.
 * 
 */
class Client
{
    /**
     * Bazowy adres API Lotto.
     *
     * @var string
     */
    private $baseUrl = 'https://developers.lotto.pl/api/open/v1/';

    /**
     * Klucz API Lotto.
     *
     * @var string
     */
    private $apiKey;

    /**
     * Konstruktor klienta Lotto.
     *
     * @param string $apiKey Klucz API uzyskany z Lotto.
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Wysyła zapytanie GET do wybranego endpointu Lotto API.
     *
     * @param string $endpoint Nazwa endpointu (np. "games/lotto").
     * @param array $params Parametry zapytania (opcjonalnie).
     * @param string $accept Nagłówek Accept (domyślnie "application/json").
     *
     * @return array Zdekodowana odpowiedź JSON z API.
     *
     * @throws \Exception W przypadku błędu sieciowego lub odpowiedzi innej niż HTTP 200.
     */
    public function request($endpoint, $params = [], $accept = 'application/json')
    {
        $url = $this->baseUrl . $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $headers = [
            'accept: ' . $accept,
            'secret: ' . $this->apiKey
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        // Obsługa błędów cURL
        if ($error) {
            throw new \Exception("cURL Error: " . $error);
        }

        $res = json_decode($response, true);

        // Wszystko OK
        if ($httpCode == 200) {
            return $res;
        }

        $msg = match($httpCode) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            422 => 'Validation Error',
            500 => 'Internal Server Error',
            default => 'Unknown status code',
        };

        throw new \Exception(
            $msg . ': ' . $endpoint . '[ ' . print_r($params, true) . " ]\n\nResponse: " . print_r($res, true),
            $httpCode
        );
    }

}


?>