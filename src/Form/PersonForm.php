<?php


namespace App\Form;


use App\Entity\Person;
use App\Services\Constants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonForm
{
    private $pwd2;
    private $id;
    private $email;
    private $name;
    private $password;
    private $agree = null;
    private $songCount = 5;

    /**
     * @return int
     */
    public function getSongCount(): int
    {
        return $this->songCount;
    }

    /**
     * @param int $songCount
     */
    public function setSongCount(int $songCount): void
    {
        $this->songCount = $songCount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function isValid(): int
    {
        if ($this->agree == null)
            return Constants::AGREEMENT_FALSE;
        if ($this->name == null || strlen($this->name) <= 1 || strlen($this->name) > 30)
            return Constants::NAME_SIZE_ERROR;
        if (!$this->password == $this->pwd2)
            return Constants::PWDS_NOT_EQUALS;
        if (strlen($this->password) < 6)
            return Constants::PWD_SIZE_SHORT;
        if (strlen($this->password) > 30)
            return Constants::PWD_SIZE_LONG;
        if (!$this->validate($this->email))
            return Constants::EMAIL_INCORRECT;
        return Constants::SUCCESS;
    }

    public function validate(string $emailStr):bool {
        return filter_var($emailStr, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @return mixed
     */
    public function getPwd2()
    {
        return $this->pwd2;
    }

    /**
     * @param mixed $pwd2
     */
    public function setPwd2($pwd2): void
    {
        $this->pwd2 = $pwd2;
    }

    public function getAgree()
    {
        return $this->agree;
    }

    /**
     * @param null $agree
     */
    public function setAgree($agree): void
    {
        $this->agree = $agree;
    }
}
