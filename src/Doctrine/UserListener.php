<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping as Orm;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Factory\TokenFactory;

class UserListener {
	/** @var UserPasswordEncoderInterface */
	private $encoder;

	/**
	 * @param UserPasswordEncoderInterface $encoder
	 */
	public function __construct(UserPasswordEncoderInterface $encoder) {
		$this->encoder = $encoder;
	}

	/** @Orm\PrePersist */
	public function prePersist(User $user, LifecycleEventArgs $args) {
		$password = $user->getPassword();
		$password = $this->encodePassword($user, $password);
		$user->setPassword($password);
		$token = TokenFactory::factory();
		$user->setApiToken($token);
	}

	/** @Orm\PreUpdate */
	public function preUpdate(User $user, PreUpdateEventArgs $args) {
		if ($args->hasChangedField('password')) {
			$password = $args->getNewValue('password');
			$password = $this->encodePassword($user, $password);
			$user->setPassword($password);
		}
		if ($args->hasChangedField('email')) {
			$user->setConfirmationStatus(false);
		}
	}

	private function encodePassword(User $user, string $password) {
		return $this->encoder->encodePassword($user, $password);
	}
}
