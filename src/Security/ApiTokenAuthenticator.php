<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenAuthenticator extends AbstractAuthenticator {
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
	}

	public function supports(Request $request): ?bool {
		return $request->getMethod() !== 'GET';
	}

	public function authenticate(Request $request): PassportInterface {
		$apiToken = $request->headers->get('X-AUTH-TOKEN');
		if ($apiToken) {
			$user = $this->entityManager
				->getRepository(User::class)
				->findOneBy(['apiToken' => $apiToken]);
			if ($user) {
				return new SelfValidatingPassport(new UserBadge($user->getEmail()), []);
			}
		}
		throw new CustomUserMessageAuthenticationException(
			'Invalid API token provided. To perform that operation, please provide a valid API token.'
		);
	}

	public function onAuthenticationSuccess(
		Request $request,
		TokenInterface $token,
		string $firewallName
	): ?Response {
		return null;
	}

	public function onAuthenticationFailure(
		Request $request,
		AuthenticationException $exception
	): ?Response {
		$data = [
			'message' => strtr(
				$exception->getMessageKey(),
				$exception->getMessageData()
			)
		];

		return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
	}
}
