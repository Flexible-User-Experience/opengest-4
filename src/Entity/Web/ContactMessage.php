<?php

namespace App\Entity\Web;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ContactMessage.
 *
 * @category Entity
 *
 * @author   David Romaní <david@flux.cat>
 */
#[ORM\Table]
#[ORM\Entity(repositoryClass: \App\Repository\Web\ContactMessageRepository::class)]
class ContactMessage extends AbstractBase
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $answer;

    /**
     * @var string
     *
     * @Assert\Email(
     *     message = "El email '{{ value }}' no es un email válido."
     * )
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $phone;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text', length: 4000)]
    private $message;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private $checked = false;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private $answered = false;

    /**
     * @var bool
     */
    private $privacy;

    /**
     * Methods.
     */

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getAnswer(): string
    {
        return $this->answer;
    }

    /**
     * @param string $answer
     *
     * @return ContactMessage
     */
    public function setAnswer($answer): ContactMessage
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param bool $checked
     *
     * @return $this
     */
    public function setChecked($checked): static
    {
        $this->checked = $checked;

        return $this;
    }

    /**
     * @return bool
     */
    public function getChecked(): bool
    {
        return $this->checked;
    }

    /**
     * @param bool $answered
     *
     * @return $this
     */
    public function setAnswered($answered): static
    {
        $this->answered = $answered;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAnswered(): bool
    {
        return $this->answered;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPrivacy(): bool
    {
        return $this->privacy;
    }

    /**
     * @param bool $privacy
     *
     * @return $this
     */
    public function setPrivacy($privacy): static
    {
        $this->privacy = $privacy;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getCreatedAt()->format('d/m/Y').' · '.$this->getEmail() : '---';
    }
}
