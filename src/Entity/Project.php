<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as Orm;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Doctrine\MarkdownListener;

/**
 * A project.
 *
 * @Orm\Entity()
 * @Orm\EntityListeners({MarkdownListener::class})
 * @UniqueEntity("slug")
 */
class Project extends BaseEntity {
	/**
	 * @var string
	 * @Orm\Column(type="string")
	 * @Assert\NotBlank()
	 * @Assert\Length(max=255)
	 */
	protected $title;

	/**
	 * @var string
	 * @Orm\Column(type="string", unique=true)
	 * @Assert\NotBlank()
	 * @Assert\Length(max=255)
	 */
	protected $slug;

	/**
	 * @var string
	 * @Orm\Column(type="string")
	 * @Assert\NotBlank()
	 * @Assert\Length(max=255)
	 */
	protected $summary;

	/**
	 * @var string
	 * @Orm\Column(type="string", nullable=true)
	 */
	protected $image;

	/**
	 * @var string
	 * @Orm\Column(type="text", nullable=true)
	 */
	protected $markdown;

	/**
	 * @var string
	 * @Orm\Column(type="text", nullable=true)
	 */
	protected $html;

	/**
	 * @var ProjectCategory
	 * @Orm\ManyToOne(targetEntity="ProjectCategory", fetch="EAGER")
	 * @Assert\NotBlank()
	 */
	protected $category;

	public function getTitle(): ?string {
		return $this->title;
	}

	public function setTitle(string $title): self {
		$this->title = $title;
		return $this;
	}

	public function getSlug(): ?string {
		return $this->slug;
	}

	public function setSlug(string $slug): self {
		$this->slug = $slug;
		return $this;
	}

	public function getSummary(): ?string {
		return $this->summary;
	}

	public function setSummary(string $summary): self {
		$this->summary = $summary;
		return $this;
	}

	public function getImage(): ?string {
		return $this->image;
	}

	public function setImage($image): self {
		$this->image = $image;
		return $this;
	}

	public function getMarkdown(): ?string {
		return $this->markdown;
	}

	public function setMarkdown(string $markdown): self {
		$this->markdown = $markdown;
		return $this;
	}

	public function getHtml(): ?string {
		return $this->html;
	}

	public function setHtml(string $html): self {
		$this->html = $html;
		return $this;
	}

	public function getCategory(): ?ProjectCategory {
		return $this->category;
	}

	public function setCategory(ProjectCategory $category): self {
		$this->category = $category;
		return $this;
	}

	public function __toString(): string {
		return $this->getTitle();
	}
}
