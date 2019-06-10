<?php declare(strict_types=1);

namespace App\WebApiV1Bundle\Endpoints\User;

use App\WebApiV1Bundle\Endpoints\Endpoint;
use App\WebApiV1Bundle\Response\HttpResponseFactory;
use App\WebApiV1Bundle\Response\JsonSuccessResponse;
use App\WebApiV1Bundle\Schema\EndpointSchema;
use App\WebApiV1Bundle\Schema\RequestMethod;
use App\WebApiV1Bundle\Schema\UrlFragments;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class CreateUserEndpoint implements Endpoint
{
    private $httpResponseFactory;

    public function __construct(HttpResponseFactory $httpResponseFactory)
    {
        $this->httpResponseFactory = $httpResponseFactory;
    }

    public function handle(): HttpResponse
    {
        $request = Request::createFromGlobals();
        $apiResponse = JsonSuccessResponse::fromData([]);
        return $this->httpResponseFactory->create($apiResponse, $request);
    }

    public static function getSchema(): EndpointSchema
    {
        $urlFragments = UrlFragments::fromStrings(['user']);
        $endpointSchema = EndpointSchema::create(RequestMethod::post(), $urlFragments);
        $endpointSchema = $endpointSchema->setSummary('Create a new user.');
        $endpointSchema = $endpointSchema->setTags(['UserManagement']);
        return $endpointSchema;
    }
}
