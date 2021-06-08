<?php

namespace App\Entity;



use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Bridge\Symfony\Validator\Validator;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 *      normalizationContext={"groups"={"user:read"}},
 *      denormalizationContext={"groups"={"user:write"}}
 * );
 * @UniqueEntity("email", message="Cet email est déjà utilisé")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("user:write", "user:read")
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @SerializedName("password")
     * @Groups("user:write")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 8,
     *      max = 30,
     *      minMessage = "Votre mot de passe doit conenir au mmoins {{ limit }} caractères",
     *      maxMessage = "Votre mot de passe ne doit pas dépasser {{ limit }} caractères"
     * )
     * @Assert\Regex(
     *  "/^.*(?=.{8,})((?=.*[!@#$%^&*()\-_=+{};:,<.>]){1})(?=.*\d)((?=.*[a-z]){1})((?=.*[A-Z]){1}).*$/",
     *  message = "Votre message doit contenir une majuscule, une minuscule, un chiffre et un caractère spécial"
     * )
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isEnabled;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tokenValidation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $tokenValidationExpireAt;

    public function __construct()
    {
        $this->isEnabled = false;
        $this->generateValidationToken();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getTokenValidation(): ?string
    {
        return $this->tokenValidation;
    }

    public function setTokenValidation(string $tokenValidation): self
    {
        $this->tokenValidation = $tokenValidation;

        return $this;
    }

    public function getTokenValidationExpireAt(): ?\DateTimeInterface
    {
        return $this->tokenValidationExpireAt;
    }

    public function setTokenValidationExpireAt(\DateTimeInterface $tokenValidationExpireAt): self
    {
        $this->tokenValidationExpireAt = $tokenValidationExpireAt;

        return $this;
    }

    public function generateValidationToken()
    {
        $expirationDate = new  \DateTime('+ 1day');
        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'));

        $this->setTokenValidation($token);
        $this->setTokenValidationExpireAt($expirationDate);
    }
}