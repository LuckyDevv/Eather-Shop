<?php
class FixedBitNotation
{
    protected string $_chars;
    protected int $_bitsPerCharacter;
    protected int $_radix;
    protected bool $_rightPadFinalBits;
    protected bool $_padFinalGroup;
    protected string $_padCharacter;
    protected $_charmap;

    public function __construct(int $bitsPerCharacter, string $chars = null, bool $rightPadFinalBits = false, bool $padFinalGroup = false, string $padCharacter = '=')
    {
        if (!is_string($chars) || ($charLength = strlen($chars)) < 2) {
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-,';
            $charLength = 64;
        }
        if ($bitsPerCharacter < 1) {
            $bitsPerCharacter = 1;
            $radix = 2;
        } elseif ($charLength < 1 << $bitsPerCharacter) {
            $bitsPerCharacter = 1;
            $radix = 2;
            while ($charLength >= ($radix <<= 1) && $bitsPerCharacter < 8) {
                $bitsPerCharacter++;
            }
            $radix >>= 1;
        } elseif ($bitsPerCharacter > 8) {
            $bitsPerCharacter = 8;
            $radix = 256;
        } else {
            $radix = 1 << $bitsPerCharacter;
        }
        $this->_chars = $chars;
        $this->_bitsPerCharacter = $bitsPerCharacter;
        $this->_radix = $radix;
        $this->_rightPadFinalBits = $rightPadFinalBits;
        $this->_padFinalGroup = $padFinalGroup;
        $this->_padCharacter = $padCharacter[0];
    }

    public function encode(string $rawString): string
    {
        $bytes = unpack('C*', $rawString);
        $byteCount = count($bytes);
        $encodedString = '';
        $byte = array_shift($bytes);
        $bitsRead = 0;
        $chars = $this->_chars;
        $bitsPerCharacter = $this->_bitsPerCharacter;
        $rightPadFinalBits = $this->_rightPadFinalBits;
        $padFinalGroup = $this->_padFinalGroup;
        $padCharacter = $this->_padCharacter;
        for ($c = 0; $c < $byteCount * 8 / $bitsPerCharacter; $c++) {
            if ($bitsRead + $bitsPerCharacter > 8) {
                $oldBitCount = 8 - $bitsRead;
                $oldBits = $byte ^ ($byte >> $oldBitCount << $oldBitCount);
                $newBitCount = $bitsPerCharacter - $oldBitCount;
                if (!$bytes) {
                    if ($rightPadFinalBits) $oldBits <<= $newBitCount;
                    $encodedString .= $chars[$oldBits];
                    if ($padFinalGroup) {
                        $lcmMap = array(1 => 1, 2 => 1, 3 => 3, 4 => 1,
                        5 => 5, 6 => 3, 7 => 7, 8 => 1);
                        $bytesPerGroup = $lcmMap[$bitsPerCharacter];
                        $pads = $bytesPerGroup * 8 / $bitsPerCharacter 
                        - ceil((strlen($rawString) % $bytesPerGroup)
                        * 8 / $bitsPerCharacter);
                        $encodedString .= str_repeat($padCharacter[0], $pads);
                    }
                    break;
                }
                $byte = array_shift($bytes);
                $bitsRead = 0;
            } else {
                $oldBitCount = 0;
                $newBitCount = $bitsPerCharacter;
            }
            $bits = $byte >> 8 - ($bitsRead + ($newBitCount));
            $bits ^= $bits >> $newBitCount << $newBitCount;
            $bitsRead += $newBitCount;
            if ($oldBitCount) {
                $bits = ($oldBits << $newBitCount) | $bits;
            }
            $encodedString .= $chars[$bits];
        }
        return $encodedString;
    }

    public function decode(string $encodedString, bool $caseSensitive = true, bool $strict = false): ?string
    {
        if (!$encodedString) {
            return '';
        }
        $chars = $this->_chars;
        $bitsPerCharacter = $this->_bitsPerCharacter;
        $radix = $this->_radix;
        $rightPadFinalBits = $this->_rightPadFinalBits;
        $padCharacter = $this->_padCharacter;
        if ($this->_charmap) {
            $char_map = $this->_charmap;
        } else {
            $char_map = array();
            for ($i = 0; $i < $radix; $i++) {
                $char_map[$chars[$i]] = $i;
            }
            $this->_charmap = $char_map;
        }
        $lastNotatedIndex = strlen($encodedString) - 1;
        while ($encodedString[$lastNotatedIndex] == $padCharacter[0]) {
            $encodedString = substr($encodedString, 0, $lastNotatedIndex);
            $lastNotatedIndex--;
        }
        $rawString = '';
        $byte = 0;
        $bitsWritten = 0;
        for ($c = 0; $c <= $lastNotatedIndex; $c++) {
            if (!isset($char_map[$encodedString[$c]]) && !$caseSensitive) {
                if (isset($char_map[$cUpper = strtoupper($encodedString[$c])])) {
                    $char_map[$encodedString[$c]] = $char_map[$cUpper];
                } elseif (isset($charmap[$cLower = strtolower($encodedString[$c])])) {
                    $char_map[$encodedString[$c]] = $char_map[$cLower];
                }
            }
            if (isset($char_map[$encodedString[$c]])) {
                $bitsNeeded = 8 - $bitsWritten;
                $unusedBitCount = $bitsPerCharacter - $bitsNeeded;
                if ($bitsNeeded > $bitsPerCharacter) {
                    $newBits = $char_map[$encodedString[$c]] << $bitsNeeded - $bitsPerCharacter;
                    $bitsWritten += $bitsPerCharacter;
                } elseif ($c != $lastNotatedIndex || $rightPadFinalBits) {
                    $newBits = $char_map[$encodedString[$c]] >> $unusedBitCount;
                    $bitsWritten = 8;
                } else {
                    $newBits = $char_map[$encodedString[$c]];
                    $bitsWritten = 8;
                }
                $byte |= $newBits;
                if ($bitsWritten == 8 || $c == $lastNotatedIndex) {
                    $rawString .= pack('C', $byte);
                    if ($c != $lastNotatedIndex) {
                        $bitsWritten = $unusedBitCount;
                        $byte = ($char_map[$encodedString[$c]] ^ ($newBits << $unusedBitCount)) << 8 - $bitsWritten;
                    }
                }
            } elseif ($strict) {
                return NULL;
            }
        }
        return $rawString;
    }
}
