<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as Orm;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Doctrine\MarkdownListener;

/**
 * An article.
 *
 * @Orm\Entity()
 * @Orm\EntityListeners({MarkdownListener::class})
 * @UniqueEntity("slug")
 */
class Article extends BaseEntity {
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
	 * @var bool
	 * @Orm\Column(type="boolean")
	 * @Assert\NotNull()
	 */
	protected $featured = false;

	/**
	 * @var Category
	 * @Orm\ManyToOne(targetEntity="Category", fetch="EAGER")
	 * @Assert\NotNull()
	 */
	protected $category;

	/**
	 * @var Tag[]|ArrayCollection
	 *
	 * @Orm\ManyToMany(targetEntity="Tag", cascade={"persist"}, fetch="EAGER")
	 * @Orm\OrderBy({"name": "ASC"})
	 */
	protected $tags;

	public function __construct() {
		parent::__construct();
		$this->tags = new ArrayCollection();
	}

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

	public function getImage(): ?string {
		return $this->image;
	}

	public function setImage(string $image) {
		$this->image = $image;
	}

	public function getFeatured(): bool {
		return $this->featured;
	}

	public function setFeatured(bool $featured): self {
		$this->featured = $featured;
		return $this;
	}

	public function getCategory(): Category {
		return $this->category;
	}

	public function setCategory(Category $category): self {
		$this->category = $category;
		return $this;
	}

	public function getTags(): Collection {
		return $this->tags;
	}

	public function addTag(Tag $tag): self {
		if (!$this->tags->contains($tag)) {
			$this->tags->add($tag);
		}
		return $this;
	}

	public function removeTag(Tag $tag): self {
		$this->tags->removeElement($tag);
		return $this;
	}

	public function __toString(): string {
		return $this->getTitle();
	}
}
