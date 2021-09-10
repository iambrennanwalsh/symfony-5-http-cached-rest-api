<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Hydrator\ReflectionHydrator;
use App\Exception\ResourceNotFoundException;

/**
 * The rest api routes.
 */
class RestController extends AbstractFOSRestController {
	/**
	 * @var EntityManagerInterface
	 */
	private $manager;

	public function __construct(EntityManagerInterface $manager) {
		$this->manager = $manager;
	}

	/**
	 * Returns a resource with a matching id.
	 *
	 * @param string $resource
	 * @param string $document
	 * @return View
	 * @throws ResourceNotFoundException
	 * @Rest\Get("/{resource}/{id}")
	 */
	public function getOne(string $resource, string $id): View {
		$repository = $this->manager->getRepository($resource);
		$entity = $repository->findOneBy(['id' => $id]);
		if (!$entity) {
			throw new ResourceNotFoundException($resource, $id);
		}
		return View::create($entity, Response::HTTP_OK);
	}

	/**
	 * Returns the result of a query.
	 *
	 * @param string $resource The name of the resource.
	 * @param Request $request
	 * @return View
	 * @Rest\Get("/{resource}")
	 */
	public function getMany(string $resource, Request $request): View {
		$repository = $this->manager->getRepository($resource);
		if ($query = $request->query->all()) {
			$args = ['order' => null, 'limit' => null, 'offset' => null];
			$query = array_merge($args, $query);
			$args = array_intersect_key($query, $args);
			$query = array_diff_key($query, $args);
			$resource = $repository->findBy($query, ...$args);
		} else {
			$resource = $repository->findAll();
		}
		return View::create($resource, Response::HTTP_OK);
	}

	/**
	 * Inserts a resource and returns the result.
	 *
	 * @param string $resource
	 * @param Request $request
	 * @return View
	 * @Rest\Post("/{resource}")
	 */
	public function post(string $resource, Request $request): View {
		$args = $request->request->all();
		if (!$args) {
			$args = (array) json_decode($request->getContent());
		}
		$entity = new $resource();
		$hydrator = new ReflectionHydrator();
		$hydrator->hydrate($args, $entity);
		$this->manager->persist($entity);
		$this->manager->flush();
		return View::create($entity, Response::HTTP_OK);
	}

	/**
	 * Replaces a resource with another, and returns the new one.
	 *
	 * @param string  $resource
	 * @param string  $id
	 * @param Request $request
	 * @return View
	 * @Rest\Put("/{resource}/{id}")
	 */
	public function put(string $resource, string $id, Request $request): View {
		$repository = $this->manager->getRepository($resource);
		$entity = $repository->findOneBy(['id' => $id]);
		if (!$entity) {
			throw new ResourceNotFoundException($resource, $id);
		}
		$args = $request->request->all();
		if (!$args) {
			$args = (array) json_decode($request->getContent());
		}
		$hydrator = new ReflectionHydrator();
		$hydrator->hydrate($args, $entity);
		$this->manager->flush();
		return View::create($entity, Response::HTTP_OK);
	}

	/**
	 * Deletes and returns a resource.
	 *
	 * @param string $resource
	 * @param string $id
	 * @return View
	 * @Rest\Delete("/{resource}/{id}")
	 */
	public function delete(string $resource, string $id): View {
		$repository = $this->manager->getRepository($resource);
		$entity = $repository->findOneBy(['id' => $id]);
		if (!$entity) {
			throw new ResourceNotFoundException($resource, $id);
		}
		$this->manager->remove($entity);
		$this->manager->flush();
		return View::create($entity, Response::HTTP_OK);
	}
}
