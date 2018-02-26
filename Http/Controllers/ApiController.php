<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Illuminate\Http\Response as IlluminateResponse;

/**
 * @property Manager fractal
 */
class ApiController extends Controller
{

    protected $statusCode = 200;

    const CODE_WRONG_ARGS = 'GEN-FUBARGS';
    const CODE_NOT_FOUND = 'GEN-LIKETHEWIND';
    const CODE_INTERNAL_ERROR = 'GEN-AAAGGH';
    const CODE_UNAUTHORIZED = 'GEN-MAYBGTFO';
    const CODE_FORBIDDEN = 'GEN-GTFO';
    const CODE_INVALID_MIME_TYPE = 'GEN-UMWUT';

    /**
     * ApiController constructor.
     */
    public function __construct()
    {

    }

    /**
     * Getter for statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Generate an Item from collection with a specific given transformer
     *
     * @param $item
     * @param $callback
     * @return mixed
     */
    protected function respondWithItem($item, $callback)
    {
        $resource = new Item($item, $callback);

        $fractal = new Manager();

        $rootScope = $fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Generate an collection from a laravel collection with a specific given transformer
     *
     * @param $collection
     * @param $callback
     * @param $type
     * @param bool $paginated
     * @param null $metaData
     * @return mixed
     */
    protected function respondWithCollection($collection, $callback, $paginated = false, $metaData = NULL)
    {
        $resource = new Collection($collection, $callback);

        $fractal = new Manager();

        if ($paginated) {
//            $request = app('Illuminate\Http\Request');
//            $queryParams = array_diff_key($request->all(), array_flip(['page']));
//            $collection->appends($queryParams);
            $resource->setPaginator(new IlluminatePaginatorAdapter($collection));
        }

        $rootScope = $fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Generate an array from collection with a specific given transformer
     *
     * @param array $array
     * @param array $headers
     * @return mixed
     */
    protected function respondWithArray(array $array, array $headers = [])
    {
        $mimeTypeRaw = Input::server('HTTP_ACCEPT', '*/*');

        // If its empty or has */* then default to JSON
        if ($mimeTypeRaw === '*/*')
        {
            $mimeType = 'application/json';
        } else
        {
            // You'll probably want to do something intelligent with charset if provided
            $mimeParts = (array) explode(',', $mimeTypeRaw);
            $mimeType = strtolower($mimeParts[0]);
        }

        switch ($mimeType)
        {
            case 'application/json':
                $contentType = 'application/json';
                $content = json_encode($array);
                break;

            default:
                $contentType = 'application/json';
                $content = json_encode([
                    'error' => [
                        'code'      => static::CODE_INVALID_MIME_TYPE,
                        'http_code' => 415,
                        'message'   => sprintf('Content of type %s is not supported.', $mimeType),
                    ]
                ]);
        }

        $response = Response::make($content, $this->statusCode, $headers);
        $response->header('Content-Type', $contentType);

        return $response;
    }

    /**
     * Generate a Response with a specific HTTP error code and given message
     * @param $message
     * @param $errorCode
     * @return mixed
     */
    protected function respondWithError($message, $errorCode)
    {
        if ($this->statusCode === 200)
        {
            trigger_error(
                "You better have a really good reason for error on a 200...",
                E_USER_WARNING
            );
        }

        return $this->respondWithArray([
            'error' => [
                'code'      => $errorCode,
                'http_code' => $this->statusCode,
                'message'   => $message,
            ]
        ]);
    }

    /**
     * Generates a Response with a 403 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)
            ->respondWithError($message, self::CODE_FORBIDDEN);
    }

    /**
     * Generates a Response with a 500 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)
            ->respondWithError($message, self::CODE_INTERNAL_ERROR);
    }

    /**
     * Generates a Response with a 404 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)
            ->respondWithError($message, self::CODE_NOT_FOUND);
    }

    /**
     * Generates a Response with a 401 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)
            ->respondWithError($message, self::CODE_UNAUTHORIZED);
    }

    /**
     * Generates a Response with a 400 HTTP header and a given message.
     *
     * @param string $message
     * @return Response
     */
    public function errorWrongArgs($message = 'Wrong Arguments')
    {
        return $this->setStatusCode(400)
            ->respondWithError($message, self::CODE_WRONG_ARGS);
    }

    /**
     * returns unauthorized response if user not authorized
     * @param string $message
     * @return response with error code and message
     */
    public function respondUnauthorizedError($message = 'Unauthorized!')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)->respondWithError($message);
    }


    /**
     * response with Forbidden error code.
     * @param string $message
     * @return mixed
     */
    public function respondForbiddenError($message = 'Forbidden!')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_FORBIDDEN)->respondWithError($message);
    }


    /**
     * response with Not Found error code.
     *
     * @param string $message
     * @return mixed
     */
    public function respondNotFound($message = 'Not Found')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->respondWithError($message, self::CODE_NOT_FOUND);
    }


    /**
     * Response with internal error code.
     *
     * @param string $message
     * @return mixed
     */
    public function respondInternalError($message = 'Internal Error!')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
    }


    /**
     * Response with a service unavailable code.
     *
     * @param string $message
     * @return mixed
     */
    public function respondServiceUnavailable($message = "Service Unavailable!")
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_SERVICE_UNAVAILABLE)->respondWithError($message);
    }


    /**
     * returns a generic response
     * @param $data Data to be used in response
     * @param array $headers response headers
     * @return mixed return the json response
     */
    public function respond($data, $headers = [])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * Response with record created code.
     * @param $message
     * @return mixed
     */
    public function respondCreated($message)
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_CREATED)
            ->respond([
                'message'    => $message,
                'statusCode' => $this->getStatusCode()
            ]);
    }


    /**
     * Response with record created code.
     * @param $message
     * @param $id
     * @return mixed
     */
    public function respondCreatedWithId($message, $id)
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_CREATED)
            ->respond([
                'id'         => $id,
                'message'    => $message,
                'statusCode' => $this->getStatusCode()
            ]);
    }

    /**
     * when validation fail response with unprocessed entity triggers.
     * @param $message
     * @return mixed
     */
    public function respondUnprocessableEntity($message)
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->respondWithError($message);
    }
}

