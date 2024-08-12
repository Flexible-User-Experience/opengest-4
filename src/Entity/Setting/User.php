<?php

namespace App\Entity\Setting;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\Enterprise;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class User.
 *
 * @category Entity
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Setting\UserRepository")
 * @ORM\Table(name="admin_user")
 * @Vich\Uploadable()
 */
class User extends AbstractBase implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const DEFAULT_ROLE = 'ROLE_USER';
    public const ADMIN_ROLE = 'ROLE_SUPER_ADMIN';

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="user", fileNameProperty="mainImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif"}
     * )
     * @Assert\Image(minWidth=1200)
     */
    private $mainImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $mainImage;

    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise")
     */
    private $defaultEnterprise;

    /**
     * @var array
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Enterprise\Enterprise", inversedBy="users")
     * @ORM\JoinTable(name="enterprises_users")
     */
    private $enterprises;

    /**
     * @ORM\Column(name="email", type="string", unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="email_canonical", type="string", unique=true)
     */
    private $emailCanonical;

    /**
     * @ORM\Column(name="username", type="string", unique=true)
     */
    private $username;

    /**
     * @ORM\Column(name="username_canonical", type="string", unique=true)
     */
    private $usernameCanonical;

    /**
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(name="plain_password", type="string", nullable=true)
     */
    private $plainPassword;

    /**
     * @ORM\Column(name="salt", type="string", nullable=true)
     */
    private $salt;

    /**
     * @ORM\Column(name="roles", type="json", nullable=true)
     */
    private $roles = [self::DEFAULT_ROLE];

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @ORM\Column(name="firstname", type="string", nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(name="lastname", type="string", nullable=true)
     */
    private $lastname;

    /**
     * Methods.
     */

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->enterprises = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getMainImageFile(): null|File|UploadedFile
    {
        return $this->mainImageFile;
    }

    /**
     * @return User
     *
     * @throws \Exception
     */
    public function setMainImageFile(File $mainImageFile = null): User
    {
        $this->mainImageFile = $mainImageFile;
        if ($mainImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getMainImage(): string
    {
        return $this->mainImage;
    }

    /**
     * @param string $mainImage
     *
     * @return User
     */
    public function setMainImage($mainImage): User
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * @return Enterprise
     */
    public function getDefaultEnterprise(): Enterprise
    {
        return $this->defaultEnterprise;
    }

    /**
     * Get session user logged enterprise.
     *
     * @return Enterprise
     */
    public function getLoggedEnterprise(): Enterprise
    {
        return $this->defaultEnterprise;
    }

    /**
     * @param Enterprise $defaultEnterprise
     *
     * @return User
     */
    public function setDefaultEnterprise($defaultEnterprise): User
    {
        $this->defaultEnterprise = $defaultEnterprise;

        return $this;
    }

    public function getEnterprises(): Collection
    {
        return $this->enterprises;
    }

    /**
     * @param array $enterprises
     *
     * @return User
     */
    public function setEnterprises($enterprises): User
    {
        $this->enterprises = $enterprises;

        return $this;
    }

    /**
     * @return $this
     */
    public function addEnterprise(Enterprise $enterprise): static
    {
        $this->enterprises->add($enterprise);

        return $this;
    }

    /**
     * @return $this
     */
    public function removeEnterprise(Enterprise $enterprise): static
    {
        $this->enterprises->removeElement($enterprise);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmailCanonical(): mixed
    {
        return $this->emailCanonical;
    }

    /**
     * @param mixed $emailCanonical
     *
     * @return User
     */
    public function setEmailCanonical($emailCanonical): User
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsernameCanonical(): mixed
    {
        return $this->usernameCanonical;
    }

    /**
     * @param mixed $usernameCanonical
     *
     * @return User
     */
    public function setUsernameCanonical($usernameCanonical): User
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstname(): mixed
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     *
     * @return User
     */
    public function setFirstname($firstname): User
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastname(): mixed
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     *
     * @return User
     */
    public function setLastname($lastname): User
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullname(): string
    {
        return $this->getLastname().', '.$this->getFirstname();
    }

    /**
     * @return string
     */
    public function getNaturalName(): string
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        $this->usernameCanonical = strtolower($username);

        return $this;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;

        return $this;
    }

    public function setPlainPassword(string $plainPassword = null)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setRoles(array $roles)
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole($role)
    {
        if (!$role) {
            return $this;
        }

        $role = strtoupper($role);

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->roles, true);
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
        $this->emailCanonical = strtolower($email);

        return $this;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function setLastLogin(DateTime $lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password) = unserialize($serialized);
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function __toString()
    {
        return $this->getUserIdentifier();
    }
}
