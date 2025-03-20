<?php

namespace App;

class PeselValidator
{
    private const WEIGHTS = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3];

    public function validChecksum(string $pesel): bool
    {
        if (!preg_match('/^\d{11}$/', $pesel)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += self::WEIGHTS[$i] * $pesel[$i];
        }

        $checksum = (10 - ($sum % 10)) % 10;

        return $checksum === (int)$pesel[10];
    }

    public function getGender(string $pesel): ?string
    {
        if (!$this->validChecksum($pesel)) {
            return null;
        }

        return ((int)$pesel[9] % 2 === 0) ? 'K' : 'M';
    }

    public function getBirthDate(string $pesel): ?string
    {
        if (!$this->validChecksum($pesel)) {
            return null;
        }

        $year = (int)substr($pesel, 0, 2);
        $month = (int)substr($pesel, 2, 2);
        $day = (int)substr($pesel, 4, 2);

        $century = 1900;

        if ($month > 80 && $month < 93) {
            $century = 1800;
            $month -= 80;
        } elseif ($month > 20 && $month < 33) {
            $century = 2000;
            $month -= 20;
        } elseif ($month > 40 && $month < 53) {
            $century = 2100;
            $month -= 40;
        } elseif ($month > 60 && $month < 73) {
            $century = 2200;
            $month -= 60;
        }

        $year = $century + $year;

        if (!checkdate($month, $day, $year)) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
