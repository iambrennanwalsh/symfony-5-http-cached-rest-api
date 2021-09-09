<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as Orm;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A blog category.
 *
 * @Orm\Entity()
 * @UniqueEntity("slug")
 */
class Category extends BaseEntity {
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
	 * @Assert\Length(max=255)
	 */
	protected $slug;

	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}

	public function getName(): ?string {
		return $this->name;
	}

	public function setSlug(string $slug): self {
		$this->slug = $slug;
		return $this;
	}

	public function getSlug(): ?string {
		return $this->slug;
	}
}
