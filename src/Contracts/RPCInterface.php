<?php

interface RPCInterface
{
	public function getTransactionData();

	public function sendRawTransaction();
}