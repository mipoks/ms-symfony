<?php

namespace App\Util;

class Alert {

    public static $COLOR_SUCCESS = "success";
    public static $HEAD_DANGER = "Произошла ошибка!";
    public static $COLOR_DANGER = "danger";

    private $body;
    private $color = "light";
    private $head;

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * @param mixed $head
     */
    public function setHead($head): void
    {
        $this->head = $head;
    }


}
