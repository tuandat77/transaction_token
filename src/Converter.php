<?php

namespace Ezdefi\Poc;


class Converter
{
    private $unitMap = [
        'poc' => '1',
        'wei' => '1000000000000000000',
    ];

    public function toWei(string $amount, string $unit = 'wei')
    {
        if ($unit == 'poc') {
            return $amount;
        }

        return $this->getBCMUL($amount, $this->getValueOfUnit($unit));
    }

    /**
     * @param $amount
     * @param $valueOfUnit
     * @return string
     */
    protected function getBCMUL($amount, $valueOfUnit)
    {
        return bcmul($amount, $valueOfUnit);
    }

    public function getValueOfUnit(string $unit = 'wei')
    {
        if (!isset($this->unitMap[$unit])) {
            throw new \UnexpectedValueException(
                sprintf('A unit "%s" doesn\'t exist, please use the one of the following units: %s', $unit, implode(', ', array_keys($this->unitMap)))
            );
        }

        return $this->unitMap[$unit];
    }
}