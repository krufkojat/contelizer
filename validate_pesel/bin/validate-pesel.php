<?php

require __DIR__ . '/../vendor/autoload.php';

use App\PeselValidator;

if (PHP_SAPI === 'cli') {
    if ($argc < 2) {
        echo "Podaj numer PESEL: ";
        $pesel = trim(fgets(STDIN));
    } else {
        $pesel = $argv[1];
    }

    $validator = new PeselValidator();

    $isValid = $validator->validChecksum($pesel);

    echo "PESEL: $pesel\n";
    echo "Poprawność: " . ($isValid ? "TAK" : "NIE") . "\n";

    if ($isValid) {
        $gender = $validator->getGender($pesel);
        echo "Płeć: " . ($gender === 'M' ? "Mężczyzna" : "Kobieta") . "\n";

        $birthDate = $validator->getBirthDate($pesel);
        echo "Data urodzenia: $birthDate\n";
    }
}
