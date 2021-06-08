<?php


namespace App\Form;


class ChatForm
{
    private $id;
    private $chatName;

    /**
     * ChatForm constructor.
     * @param $id
     * @param $chatName
     */
    public function __construct($id, $chatName)
    {
        $this->id = $id;
        $this->chatName = $chatName;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getChatName()
    {
        return $this->chatName;
    }

    /**
     * @param mixed $chatName
     */
    public function setChatName($chatName): void
    {
        $this->chatName = $chatName;
    }


}
