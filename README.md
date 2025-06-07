# Simple Lotto PHP Client

Prosty klient do pobierania danych z publicznego API Lotto:  
https://developers.lotto.pl/api/open/v1/

**Szczegółowy opis i przykład znajdziesz tu:**  
[Proste API Lotto w PHP – Artykuł na blogu Eskim.pl](https://eskim.pl/proste-api-lotto-w-php/)

**Jeśli projekt Ci się podoba, możesz postawić mi kawę:**  
[https://www.buymeacoffee.com/eskim](https://www.buymeacoffee.com/eskim)

---

## Instalacja

Skopiuj plik `Client.php` do swojego projektu i załaduj przez `require_once` lub użyj autoloadera PSR-4.

## Użycie

```php

$client = new Lotto\Client('TWOJ_API_KEY');

try {
    // Przykład pobrania danych o grze Lotto
    $response = $client->request('lotteries/draw-results/last-results');
    print_r($response);

    // Przykład z parametrami
    $response = $client->request('lotteries/info', ['gameType' => 'Lotto']);
    print_r($response);
	
} catch (Exception $e) {

    echo "Błąd: " . $e->getMessage();
}
