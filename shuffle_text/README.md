# Text Shuffler

Program do mieszania liter w wyrazach tekstu, zachowując pierwszą i ostatnią literę każdego wyrazu.

## Opis

Text Shuffler to narzędzie, które:
- Przetwarza pliki tekstowe, mieszając losowo litery w każdym wyrazie
- Zachowuje pierwszą i ostatnią literę każdego wyrazu bez zmian
- Zapisuje przetworzony tekst do nowego pliku z dodatkowym oznaczeniem "_shuffled" w nazwie

## Wymagania

- PHP 7.0 lub nowszy

## Użycie

### Bezpośrednie uruchomienie z podaniem nazwy pliku

```
php shuffle_text.php przyklad.txt
```

### Uruchomienie bez parametrów

Jeśli uruchomisz program bez parametrów, zostaniesz poproszony o wprowadzenie nazwy pliku:

```
php shuffle_text.php
```

Zobaczysz komunikat:
```
Podaj nazwę pliku do przetworzenia:
```

## Jak to działa?

1. Program odczytuje plik tekstowy podany jako argument
2. Dla każdego wyrazu:
    - Identyfikuje wyrazy jako ciągi znaków oddzielone spacjami
    - Zachowuje pierwszą i ostatnią literę każdego wyrazu
    - Miesza losowo wszystkie litery znajdujące się pomiędzy nimi
    - Znaki niebędące literami (np. znaki interpunkcyjne) pozostają na swoich miejscach
3. Zapisuje przetworzony tekst do nowego pliku z sufiksem "_shuffled" dodanym do nazwy oryginalnego pliku

## Licencja

MIT
