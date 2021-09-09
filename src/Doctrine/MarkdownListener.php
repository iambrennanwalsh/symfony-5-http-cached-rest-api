<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use \Parsedown;

class MarkdownListener {
	/** @PreUpdate */
	public function preUpdate($entity, PreUpdateEventArgs $args) {
		if (!$args->hasChangedField('markdown')) {
			return;
		}
		$markdown = $args->getNewValue('markdown');
		$html = $this->convertMarkdownToHtml($markdown);
		$entity = $args->getEntity();
		$entity->setHtml($html);
	}

	/** @PrePersist */
	public function prePersist($entity, LifecycleEventArgs $args) {
		$entity = $args->getEntity();
		$markdown = $entity->getMarkdown();
		$html = $this->convertMarkdownToHtml($markdown);
		$entity->setHtml($html);
	}

	/**
	 * @param string $markdown
	 * @return string
	 */
	private function convertMarkdownToHtml(string $markdown) {
		$parsedown = new Parsedown();
		return $parsedown->text($markdown);
	}
}
