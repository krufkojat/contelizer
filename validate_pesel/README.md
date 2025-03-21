# PESEL Validator

Program do walidacji numeru PESEL, który dodatkowo odczytuje płeć oraz datę urodzenia z podanego numeru.

## Opis

PESEL Validator to narzędzie, które pozwala na:
- Sprawdzenie poprawności numeru PESEL
- Odczytanie płci osoby na podstawie numeru PESEL
- Ustalenie daty urodzenia na podstawie numeru PESEL

Program waliduje numer PESEL zgodnie z oficjalnym algorytmem, uwzględniając sumę kontrolną oraz poprawność zakodowanej daty.

## Wymagania

- PHP 8.0 lub nowszy

## Użycie

### Bezpośrednie uruchomienie z podaniem numeru PESEL

```
bin/validate-pesel.php 12345678901
```

### Uruchomienie bez parametrów

Jeśli uruchomisz program bez parametrów, zostaniesz poproszony o wprowadzenie numeru PESEL:

```
bin/validate-pesel.php
```

Zobaczysz komunikat:
```
Podaj numer PESEL:
```

## Licencja

MIT
