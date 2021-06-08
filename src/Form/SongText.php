<?php


namespace App\Form;


class SongText
{
    private $id;
    private $name;
    private $text;

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getText() : string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }


    public function toJson() {
        return json_encode([
            'id' => $this->id,
            'name' => $this->name,
            'text' => $this->text
        ], JSON_UNESCAPED_UNICODE);
    }
}
