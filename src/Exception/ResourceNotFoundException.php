<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ResourceNotFoundException extends Exception
{
  public function __construct(string $resource, string $id)
  {
    $message = "A matching $resource resource with id $id was not found.";
    $code = Response::HTTP_NOT_FOUND;
    parent::__construct($message, $code);
  }
}
