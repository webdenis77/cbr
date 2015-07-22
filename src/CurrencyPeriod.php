<?php

namespace CBR;

class CurrencyPeriod
{
	private $date_from;
	private $date_to;
	private $currency;
	private $result;

	public function setDateFrom($date)
	{
		$this->date_from = $date;

		return $this;
	}

	public function setDateTo($date)
	{
		$this->date_to = $date;

		return $this;
	}

	public function setCurrency($currency)
	{
		$this->currency = $currency;

		return $this;
	}

	public function request()
	{
		$this->result = (new Request(Request::URL_CUR_PERIOD, [
			'date_req1' => $this->date_from,
			'date_req2' => $this->date_to,
			'VAL_NM_RQ' => $this->currency
		]))->request();

		return $this;
	}

	public function getResult($format = 'Y-m-d')
	{
		libxml_use_internal_errors(true);
		$xml = new \SimpleXMLElement($this->result);
		$xpath = $xml->xpath('Record');

		$result = [];
		foreach ($xpath as $element) {
			$date = str_replace('.0', '.', (string)$element->attributes()['Date']);
			$k = (new \DateTime())->setTimestamp(strtotime($date))->format($format);

			$result[$k] = [
				'Nominal' => (int)$element->Nominal,
				'Value' => (float)(str_replace(',', '.', $element->Value))
			];
		}

		return $result;
	}

	public function getResultXML()
	{
		return $this->result;
	}
}
