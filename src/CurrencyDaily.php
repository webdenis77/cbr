<?php

namespace CBR;

use DateTime;
use Exception;
use RuntimeException;
use SimpleXMLElement;

class CurrencyDaily extends Resource
{
    /** @var string */
    private $date;

    /** @var string[] */
    private $currencies = [];

    /** @var DateTime */
    private $result_date;

    /**
     * Установка фильтра по дате.
     *
     * @param string $date
     * @return $this
     */
    public function setDate($date)
    {
        $datetime = DateTime::createFromFormat($this->date_format, $date);

        $this->date = $datetime->format('d/m/Y');

        return $this;
    }

    /**
     * Установка фильтра по валютам.
     *
     * @param string[] $currencies
     * @return $this
     */
    public function setCurrencies($currencies)
    {
        $this->currencies = $currencies;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getURL()
    {
        return 'http://www.cbr.ru/scripts/XML_daily.asp';
    }

    /**
     * @inheritDoc
     */
    protected function getQuery()
    {
        return (empty($this->date))
            ? []
            : ['date_req' => $this->date];
    }

    /**
     * Получение обработанного результата.
     *
     * @return array<string, array{ID: string, NumCode: string, CharCode: string, Nominal: int, Name: string, Value: float}>
     * @throws Exception
     */
    public function getResult()
    {
        libxml_use_internal_errors(true);
        $xml = new SimpleXMLElement($this->result);

        $date = str_replace('.0', '.', (string)$xml->attributes()['Date']);
        $this->result_date = (new DateTime())->setTimestamp(strtotime($date));

        $xpath = $xml->xpath($this->getCurrencyXpath());
        $result = [];
        foreach ($xpath as $element) {
            $key = $this->getElementKey($element);

            $result[$key] = [
                'ID' => (string)$element->attributes()['ID'],
                'NumCode' => (string)$element->NumCode,
                'CharCode' => (string)$element->CharCode,
                'Nominal' => (int)$element->Nominal,
                'Name' => (string)$element->Name,
                'Value' => (float)(str_replace(',', '.', $element->Value))
            ];
        }

        return $result;
    }

    /**
     * Получение акутальной даты обновления из ответа.
     *
     * @return string
     */
    public function getResultDate()
    {
        return $this->result_date->format($this->date_format);
    }

    /**
     * Получение XPATH-апроса для фильтра по валютам.
     *
     * @return string
     */
    private function getCurrencyXpath()
    {
        $codes_count = count($this->currencies);

        if ($codes_count < 1) {
            return 'Valute';
        }

        switch ($this->key_format) {
            case Resource::KEY_CHAR:
                $key = 'CharCode';
                break;
            case Resource::KEY_NUM:
                $key = 'NumCode';
                break;
            case Resource::KEY_ID:
                $key = '@ID';
                break;
            default:
                throw new RuntimeException("Неопознанный тип ключа: $this->key_format");
        }

        $filter = array_map(function ($row) use ($key) {
            return sprintf('%s="%s"', $key, $row);
        }, $this->currencies);

        $query = implode(' or ', $filter);

        return "Valute[$query]";
    }

    /**
     * Получение названия ключа элемента.
     *
     * @param SimpleXMLElement $element
     * @return string
     */
    private function getElementKey($element)
    {
        switch ($this->key_format) {
            case self::KEY_CHAR:
                return (string)$element->CharCode;
            case self::KEY_NUM:
                return (string)$element->NumCode;
            case self::KEY_ID:
                return (string)$element->attributes()['ID'];
            default:
                throw new RuntimeException("Неопознанный тип ключа: $this->key_format");
        }
    }
}
