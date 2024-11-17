<?php

/**
 * @see https://npowest.ru
 *
 * @license Shareware
 * @copyright (c) 2019-2024 NPOWest
 */

declare(strict_types=1);

namespace App\Core;

use function array_slice;
use function chr;

final class Controller
{
    /** @var array<int, string> */
    private array $char = [
        0xA0 => 'Б', 0xA1 => 'Г', 0xA2 => 'Ё', 0xA3 => 'Ж', 0xA4 => 'З',
        0xA5 => 'И', 0xA6 => 'Й', 0xA7 => 'Л', 0xA8 => 'П', 0xA9 => 'У',
        0xAA => 'Ф', 0xAB => 'Ч', 0xAC => 'Ш', 0xAD => 'Ъ', 0xAE => 'Э',
        0xAF => 'Э', 0xB0 => 'Ю', 0xB1 => 'Я', 0xB2 => 'б', 0xB3 => 'в',
        0xB4 => 'г', 0xB5 => 'е', 0xB6 => 'ж', 0xB7 => 'з', 0xB8 => 'и',
        0xB9 => 'й', 0xBA => 'к', 0xBB => 'л', 0xBC => 'м', 0xBD => 'н',
        0xBE => 'п', 0xBF => 'т', 0xC0 => 'ч', 0xC1 => 'ш', 0xC2 => 'ъ',
        0xC3 => 'ы', 0xC4 => 'ь', 0xC5 => 'э', 0xC6 => 'ю', 0xC7 => 'я',
        0xD9 => '+', 0xDA => '-', 0xE0 => 'Д', 0xE1 => 'Ц', 0xE2 => 'Щ',
        0xE3 => 'д', 0xE4 => 'ф', 0xE5 => 'ц', 0xE6 => 'щ', 0xFF => '_',
    ];

    /**
     * @return array<array<string, int|string>>
     */
    public function lsdWest02(string $msg): array
    {
        return $this->convertMessage($msg, true);
    }//end lsdWest02()

    /**
     * @return array<array<string, int|string>>
     */
    public function lsdWest02m(string $msg): array
    {
        return $this->convertMessage($msg, false);
    }//end lsdWest02m()

    /**
     * @return array<array<string, int|string>>
     */
    private function convertMessage(string $msg, bool $isBasic): array
    {
        $result    = [];
        $string    = '';
        $length    = ($isBasic) ? 32 : null;
        $msgChunks = array_slice(mb_str_split($msg, 2), 3, $length);
        $code      = 0;

        foreach ($msgChunks as $str)
        {
            $bt = hexdec($str);
            if (10 == $bt)
            {
                $result[] = ['string' => $string, 'code' => $code];
                $string   = '';
                $code     = 0;

                continue;
            }

            if ($bt < 10)
            {
                $code = $bt;
            }

            $char    = ($isBasic) ? ($this->char[$bt] ?? chr($bt)) : $this->processExtendedCharacter($bt);
            $string .= $char;
        }

        if ('' !== $string)
        {
            $result[] = ['string' => $string, 'code' => $code];
        }

        return array_slice($result, 0, 6);
    }//end convertMessage()

    private function processExtendedCharacter(int $bt): string
    {
        switch (true)
        {
            case 10 === $bt:
                return "\n";

            case $bt >= 32 && $bt <= 126:
                return chr($bt);

            case $bt >= 192:
                return iconv('CP1251', 'UTF-8', chr($bt));
            default:
                return '';
                // Не возвращает ничего, если символ не определен
        }
    }//end processExtendedCharacter()
}//end class
