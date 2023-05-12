<?php

namespace CBR;

use DateTime;
use Exception;
use SimpleXMLElement;

class CurrencyPeriod extends Resource
{
    /** @var string */
    private $date_from;

    /** @var string */
    private $date_to;

    /** @var string */
    private $currency;

    /**
     * Установка фильтра по датам.
     *
     * @param string $date_from
     * @param string $date_to
     * @return $this
     */
    public function setInterval($date_from, $date_to)
    {
        $datetime_from = DateTime::createFromFormat($this->date_format, $date_from);
        $datetime_to = DateTime::createFromFormat($this->date_format, $date_to);

        $this->date_from = $datetime_from->format('d/m/Y');
        $this->date_to = $datetime_to->format('d/m/Y');

        return $this;
    }

    /**
     * Установка фильтра по валюте.
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getURL()
    {
        return 'http://www.cbr.ru/scripts/XML_dynamic.asp';
    }

    /**
     * @inheritDoc
     */
    protected function getQuery()
    {
        return [
            'date_req1' => $this->date_from,
            'date_req2' => $this->date_to,
            'VAL_NM_RQ' => $this->currency
        ];
    }

    /**
     * Получение обработанного результата.
     *
     * @return array<string, array{Nominal: int, Value: float}>
     * @throws Exception
     */
    public function getResult()
    {
        libxml_use_internal_errors(true);
        $xml = new SimpleXMLElement($this->result);
        $xpath = $xml->xpath('Record');

        $result = [];
        foreach ($xpath as $element) {
            $date = str_replace('.0', '.', (string)$element->attributes()['Date']);
            $key = (new DateTime())->setTimestamp(strtotime($date))->format($this->date_format);

            $result[$key] = [
                'Nominal' => (int)$element->Nominal,
                'Value' => (float)(str_replace(',', '.', $element->Value))
            ];
        }

        return $result;
    }
}
