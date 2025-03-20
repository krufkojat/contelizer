<?php

namespace Tests\App;

use App\PeselValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class PeselValidatorTest extends TestCase
{
    private PeselValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new PeselValidator();
    }

    #[DataProvider('validPeselProvider')]
    public function testValidChecksum_WithValidPesel_ReturnsTrue(string $pesel): void
    {
        $this->assertTrue($this->validator->validChecksum($pesel));
    }

    #[DataProvider('invalidPeselProvider')]
    public function testValidChecksum_WithInvalidPesel_ReturnsFalse(string $pesel): void
    {
        $this->assertFalse($this->validator->validChecksum($pesel));
    }

    #[DataProvider('malePeselProvider')]
    public function testGetGender_WithMalePesel_ReturnsM(string $pesel): void
    {
        $this->assertEquals('M', $this->validator->getGender($pesel));
    }

    #[DataProvider('femalePeselProvider')]
    public function testGetGender_WithFemalePesel_ReturnsK(string $pesel): void
    {
        $this->assertEquals('K', $this->validator->getGender($pesel));
    }

    #[DataProvider('invalidPeselProvider')]
    public function testGetGender_WithInvalidPesel_ReturnsNull(string $pesel): void
    {
        $this->assertNull($this->validator->getGender($pesel));
    }

    #[DataProvider('birthDateProvider')]
    public function testGetBirthDate_WithValidPesel_ReturnsCorrectDate(string $pesel, string $expectedDate): void
    {
        $this->assertEquals($expectedDate, $this->validator->getBirthDate($pesel));
    }

    #[DataProvider('invalidPeselProvider')]
    public function testGetBirthDate_WithInvalidPesel_ReturnsNull(string $pesel): void
    {
        $this->assertNull($this->validator->getBirthDate($pesel));
    }

    public static function validPeselProvider(): array
    {
        return [
            ['01272867467'],
            ['94052619846'],
        ];
    }

    public static function invalidPeselProvider(): array
    {
        return [
            ['1234567890'], // Za krótki
            ['123456789012'], // Za długi
            ['abcdefghijk'], // Błędny format
            ['44051401454'], // Nieprawidłowa suma kontrolna
            [''], // Pusty
        ];
    }

    public static function malePeselProvider(): array
    {
        return [
            ['02220894153'],
            ['80100596232'],
        ];
    }

    public static function femalePeselProvider(): array
    {
        return [
            ['05232524625'],
            ['60110612845'],
        ];
    }

    public static function birthDateProvider(): array
    {
        return [
            ['58051487585', '1958-05-14'],
            ['84041014246', '1984-04-10'],
        ];
    }
}
