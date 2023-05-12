<?php

namespace CBR;

abstract class Resource
{
    const KEY_CHAR = 'CHAR_CODE';
    const KEY_NUM = 'NUM_CODE';
    const KEY_ID = 'ID';

    protected $result;

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

    abstract public function getResult($key_format);
}
