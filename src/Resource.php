<?php

namespace CBR;

abstract class Resource
{
    const KEY_CHAR = 'CHAR';
    const KEY_NUM = 'NUM';
    const KEY_ID = 'ID';

    /** @var string */
    protected $date_format = 'd/m/Y';

    /** @var string */
    protected $key_format;

    /** @var string */
    protected $result;

    /**
     * Устновка формата даты.
     *
     * @param string $format
     * @return $this
     */
    public function setDateFormat($format)
    {
        $this->date_format = $format;

        return $this;
    }

    /**
     * Установка формата ключа.
     *
     * @param $format
     * @return $this
     */
    public function setKeyFormat($format)
    {
        $this->key_format = $format;

        return $this;
    }

    /**
     * Получение адреса запроса.
     *
     * @return string
     */
    abstract protected function getURL();

    /**
     * Получение параметров запроса.
     *
     * @return array<string, string>
     */
    abstract protected function getQuery();

    /**
     * Выполнение запроса.
     *
     * @return $this
     */
    public function request()
    {
        $this->result = (new Request($this->getURL(), $this->getQuery()))->request();

        return $this;
    }

    /**
     * Получение необработанного результата.
     *
     * @return string
     */
    public function getXMLResult()
    {
        return $this->result;
    }

    abstract public function getResult();
}
