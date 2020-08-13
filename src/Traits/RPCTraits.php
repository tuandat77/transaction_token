<?php
namespace Ezdefi\Poc\Traits;

trait RPCTraits
{
    private $unitMap = [
        'poc' => '1',
        'wei' => '1000000000000000000',
    ];

	public function refactorData($data)
	{
	    $data1 = substr($data, 0, 10);
	    $data2 = '000000000000000000000000';
	    $data3 = substr($data, 10);
	    $data  = $data1 . $data2 . $data3;
		return $data;
	}

	public function toWei(string $amount, string $unit = 'wei')
	{
		if ($unit == 'poc') {
		    return $amount;
        }

		return $this->getBCMUL($amount, $this->getValueOfUnit($unit));
	}

    public function bcdechex($dec)
    {
        $hex = '';
        do {
            $last   = bcmod($dec, 16);
            $hex    = dechex($last).$hex;
            $dec    = bcdiv(bcsub($dec, $last), 16);
        } while($dec > 0);
        return $hex;
	}

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

    public function readFileJson($file)
    {
        $dataJson = file_get_contents($file);
        return json_decode($dataJson);
    }
}