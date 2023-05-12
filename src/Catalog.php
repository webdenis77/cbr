<?php

namespace CBR;

use Exception;
use SimpleXMLElement;

class Catalog extends Resource
{
    const TYPE_DAILY = 0;
    const TYPE_MONTHLY = 1;

    /** @var int */
    private $type = self::TYPE_DAILY;

    /**
     * Установка фильтра по типу валюты.
     *
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getURL()
    {
        return 'http://www.cbr.ru/scripts/XML_valFull.asp';
    }

    /**
     * @inheritDoc
     */
    protected function getQuery()
    {
        return ['d' => $this->type];
    }

    /**
     * Получение обработанного результата.
     *
     * @return array<int, array{ID: string, NumCode: string, CharCode: string, Nominal: int, Name: string}>
     * @throws Exception
     */
    public function getResult()
    {
        libxml_use_internal_errors(true);
        $xml = new SimpleXMLElement($this->result);

        $xpath = $xml->xpath('Item');
        $result = [];
        foreach ($xpath as $element) {
            $result[] = [
                'ID' => (string)$element->attributes()['ID'],
                'NumCode' => (string)$element->ISO_Num_Code,
                'CharCode' => (string)$element->ISO_Char_Code,
                'Nominal' => (int)$element->Nominal,
                'Name' => (string)$element->Name,
            ];
        }

        return $result;
    }
}
