<?php

function shuffleWord(string $word): string
{
    $length = mb_strlen($word, 'UTF-8');

    if ($length < 4) {
        return $word;
    }

    $firstChar = mb_substr($word, 0, 1, 'UTF-8');
    $lastChar = mb_substr($word, -1, 1, 'UTF-8');
    $middleChars = mb_substr($word, 1, $length -2, 'UTF-8');

    $chars = preg_split('//u', $middleChars, -1, PREG_SPLIT_NO_EMPTY);

    var_dump($chars);

    shuffle($chars);
    $charsShuffled = implode('', $chars);

    return $firstChar . $charsShuffled . $lastChar;
}

function processText(string $text): string
{
    return preg_replace_callback('/\p{L}{3,}/u', function ($match) {
        return shuffleWord($match[0]);
    }, $text);
}

/**
 * @throws Exception
 */
function processFile(string $source): void
{
    if (!file_exists($source)) {
        throw new \Exception("Błąd: Plik źródłowy '$source' nie istnieje.");
    }

    $pathInfo = pathinfo($source);

    $destination = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_shuffled.' . $pathInfo['extension'];

    $content = file_get_contents($source);

    if (!$content) {
        throw new \Exception("Błąd: Nie można odczytać pliku '$source'.");
    }

    $shuffledContent = processText($content);

    if (file_put_contents($destination, $shuffledContent) === false) {
        throw new \Exception("Błąd: Nie można zapisać do pliku '$destination'.");
    }
}

if (PHP_SAPI === 'cli') {
    if ($argc < 2) {
        echo "Podaj nazwę pliku źródłowego: ";
        $source = trim(fgets(STDIN));
    } else {
        $source = $argv[1];
    }

    try {
        processFile($source);

        echo "Wyrazy zostały pomieszane.\n";
    } catch (\Exception $e) {
        die($e->getMessage() . "\n");
    }
}

