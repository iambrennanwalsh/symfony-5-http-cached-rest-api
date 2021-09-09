<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as Orm;
use JMS\Serializer\Annotation as Jms;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Doctrine\UserListener;

/**
 * A user.
 *
 * @Orm\Entity()
 * @Orm\Table(name="users")
 * @Orm\EntityListeners({UserListener::class})
 * @Jms\ExclusionPolicy("none")
 * @UniqueEntity("email")
 */
class User extends BaseEntity implements UserInterface {
	/**
	 * @var string
	 * @Orm\Column(type="string")
	 * @Assert\NotBlank()
	 * @Assert\Length(max=255)
	 */
	protected $name;

	/**
	 * @var string
	 * @Orm\Column(type="string", unique=true)
	 * @Assert\NotBlank()
	 * @Assert\Email()
	 * @Assert\Length(max=255)
	 */
	protected $email;

	/**
	 * @var string
	 * @Orm\Column(type="string")
	 * @Jms\Exclude()
	 * @Assert\NotBlank()
	 * @Assert\Length(max=255)
	 */
	protected $password;

	/**
	 * @var array
	 * @Orm\Column(type="json")
	 * @Assert\NotBlank()
	 */
	protected $roles;

	/**
	 * @var bool
	 * @Orm\Column(type="boolean")
	 * @Assert\NotNull()
	 */
	protected $confirmationStatus = false;

	/**
	 * @var string
	 *
	 * @Orm\Column(type="string", nullable=true)
	 * @Jms\Exclude()
	 * @Assert\Length(max=255)
	 */
	protected $apiToken;

	public function getName(): ?string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}

	public function getEmail(): ?string {
		return $this->email;
	}

	public function setEmail(string $email): self {
		$this->email = $email;
		$this->gravatar =
			'https://www.gravatar.com/avatar/' . md5($email) . '?s=500';
		return $this;
	}

	public function getPassword(): ?string {
		return $this->password;
	}

	public function setPassword(string $password): self {
		$this->password = $password;
		return $this;
	}

	public function getRoles(): ?array {
		$roles = $this->roles;
		$roles[] = 'ROLE_USER';
		return array_unique($roles);
	}

	public function setRoles(array $roles): self {
		$this->roles = $roles;
		return $this;
	}

	public function getConfirmationStatus(): bool {
		return $this->confirmationStatus;
	}

	public function setConfirmationStatus(bool $confirmationStatus): self {
		$this->confirmationStatus = $confirmationStatus;
		return $this;
	}

	public function getGravatar(): ?string {
		return $this->gravatar;
	}

	public function getApiToken(): string {
		return $this->apiToken;
	}

	public function setApiToken(string $apiToken): self {
		$this->apiToken = $apiToken;
		return $this;
	}

	public function getUsername(): ?string {
		return $this->email;
	}

	public function getSalt() {
	}

	public function eraseCredentials() {
	}
}
