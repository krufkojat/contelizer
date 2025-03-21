# Aplikacja korzystająca z API GoREST

Aplikacja oparta o Symfony 5, której celem jest komunikacja z publicznym API dostępny pod adresem [https://gorest.co.in/](https://gorest.co.in/). Projekt umożliwia pobieranie listy użytkowników, wyszukiwanie ich oraz edycję istniejących wpisów, a wszystko to z wykorzystaniem jQuery i AJAX, aby zapewnić interakcje bez konieczności odświeżania strony.

## Funkcjonalności

- **Wyświetlanie listy użytkowników:** Pobieranie danych z API i prezentacja w przejrzystym widoku.
- **Wyszukiwanie użytkowników:** Dynamiczne filtrowanie użytkowników na podstawie kryteriów wyszukiwania.
- **Edycja wpisów:** Możliwość modyfikowania danych użytkowników z odpowiednimi zabezpieczeniami.
- **AJAX i jQuery:** Implementacja dynamicznych akcji bez przeładowania strony.
- **Bezpieczeństwo:** Walidacja danych oraz zabezpieczenia (m.in. token CSRF) stosowane przy operacjach edycji.

## Wymagania

- PHP w wersji co najmniej 8.0
- Composer
- Symfony 5
- jQuery (do obsługi zapytań AJAX)
- Klucz API (wymagany do autoryzacji zapytań do [GoREST](https://gorest.co.in/))

## Licencja

MIT
