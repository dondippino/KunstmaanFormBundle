<?php

namespace Kunstmaan\FormBundle\Helper\Zendesk;


use Buzz\Browser;
use Buzz\Client\FileGetContents;
use Buzz\Message\Response;
use JMS\Serializer\Serializer;
use Kunstmaan\FormBundle\Helper\Buzz\Listener\LoggerListener;
use Kunstmaan\FormBundle\Helper\Buzz\Listener\TokenAuthListener;
use Kunstmaan\FormBundle\Helper\Exceptions\ClientSideException;
use Kunstmaan\FormBundle\Helper\Exceptions\NotAuthorizedException;
use Kunstmaan\FormBundle\Helper\Exceptions\ServerSideException;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\Request;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\Ticket;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\User;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Locale\Exception\NotImplementedException;


class ZendeskApiClient
{

    protected $apiKey;

    protected $domain;

    protected $login;

    /** @var Logger */
    protected $logger;

    private $url;

    public function setApiKey($value)
    {
        $this->apiKey = $value;

        return $this;
    }

    public function setDomain($value)
    {
        $this->domain = $value;

        return $this;
    }

    public function setLogin($value)
    {
        $this->login = $value;

        return $this;
    }

    public function setLogger($value)
    {
        $this->logger = $value;

        return $this;
    }

    /**
     * @var Serializer
     */
    protected $serializer;

    public function setSerializer($value)
    {
        $this->serializer = $value;

        return $this;
    }


    /**
     * @param $userEmail
     * @param callable $function The callable that is executed. Will pass the ZendeskApiClient that imporsonates the user.
     */
    public function runAs($userEmail, $function)
    {
        $originalLogin = $this->login;
        $this->logger->info('imporsonating: '. $userEmail);
        $this->setLogin($userEmail);

        try {
            $function($this);
        } catch (\Exception $e) {
            $this->restoreOriginalLogin($originalLogin);

            throw new \Exception(sprintf('Error executing code while imporsonating user \'%s\'', $userEmail), 0, $e);
        }

        $this->restoreOriginalLogin($originalLogin);

        return true;
    }

    private function restoreOriginalLogin($original)
    {
        $this->setLogin($original);
        $this->logger->info('returning to original credentials');
    }


    /** @var Browser */
    private $browser;

    /**
     * @param string $endpoint The name of the resource you want to interact with.
     * @param string $action Stuff like 'search' to search users etc.
     * @param integer|null $id The ID of the resource for updating or deleting. When doing get/post this isn't needed.
     * @return Browser
     */
    private function getBrowser($endpoint, $action = '', $id = null)
    {
        // TODO: Refactor. I really don't like the way we rebuild the browser when the URL changes. Should only rebuild when the user has changed.
        if (!is_null($id)) {
            $id = '/' . $id;
        }
        if (!is_null($action) && (!empty($action))) {
            $action = '/' . $action;
        }

        $newUrl = 'https://' . $this->domain . '.zendesk.com/api/v2/' . $endpoint . $action . $id . '.json';
        $this->url = $newUrl;

        // TODO: Only refresh browser when the login has changed.

        $client = new FileGetContents();
        $browser = new Browser($client);
        $browser->addListener(new TokenAuthListener($this->login, $this->apiKey));

        $logger = $this->logger;
        $doLog = function($text) use ($logger) {
            $logger->addInfo($text);
;       };

        $browser->addListener(new LoggerListener($doLog));

        $this->browser = $browser;

        return $browser;
    }

    private function createUrl($toAppend = '')
    {
        return $this->url . $toAppend;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function createUserIfNotPresent(User $user)
    {
        // Actually call the API.
        $browser = $this->getBrowser('users', 'search');

        // Find the user with this email.
        $users = $this->getCall($browser, $this->createUrl('?query=' . urlencode($user->getEmail())));

        if (empty($users)) {
            return $this->createCall('users', $user);
        }

        return $users[0];
    }

    /**
     * @param string $resource Resource in plural form.
     * @param object $object The object you want to create.
     *
     * @return object The new object with its ID filled in.
     */
    private function createCall($resource, $object)
    {
        $browser = $this->getBrowser($resource);

        // TODO: Add Content-Type: application/json
        $result = $browser->post($this->createUrl(), $this->requestheaders, $this->serializeObject($object, $resource));
        // TODO: Update ID.
        var_dump($result);

        $resultingObject = $this->handleResponse($result);

        return $resultingObject;
    }

    private $requestheaders = array('Content-Type' => 'application/json');

    private function getCall(Browser $browser, $url)
    {
        $response = $browser->get($url, $this->requestheaders);
        $result = $this->handleResponse($response);

        return $result;
    }


    private function serializeObject($object, $pluralResourceName)
    {
        $newObj = array();
        $newObj[ZendeskApiClient::$RESOURCES[$pluralResourceName]] = $object;
        return $this->serializer->serialize($newObj, 'json');
    }

    public static $TYPE_JSON = 'json';

    public static $RESOURCES = array(
        'users' => 'user',
        'tickets' => 'ticket',
        'requests' => 'request',
    );

    /**
     * Throw an exception on error. When empty, return empty array.
     *
     * @param $response
     */
    private function handleResponse(Response $response)
    {
        $rawResponse = json_decode($response->getContent(), true);

        // If 401, unauthorized.
        if ($response->getStatusCode() == 401) {
            throw new NotAuthorizedException($rawResponse['error']);
        }

        if ($response->isClientError()) {
            throw new ClientSideException($response->getStatusCode() . ':' . $rawResponse['error']);
        }

        if ($response->isServerError()) {
            throw new ServerSideException($response->getStatusCode() . ':' . $rawResponse['error']);
        }

        foreach ($rawResponse as $resource => $value) {
            if (array_key_exists($resource, ZendeskApiClient::$RESOURCES)) {
                return $this->deserializeArrayResponse(ZendeskApiClient::$RESOURCES[$resource], $value);
            }

            $resourcesHavingValue = array_filter(ZendeskApiClient::$RESOURCES, function($v) use ($resource) {
                if ($resource == $v) {
                    return true;
                }
                return false;
            });

            if (count($resourcesHavingValue) == 1) {
                return $this->deserializeObjectResponse(reset($resourcesHavingValue), json_encode($value));
                //return
            } elseif (count($resourcesHavingValue) > 1) {
                throw new \LogicException('Check ZendeskApiClient::$RESOURCES. Should not have duplicates for the values.');
            }
        }

        // Probably an exception then.
        var_dump('exception end');
        var_dump($rawResponse);

        die('deserialize');
    }

    private function deserializeArrayResponse($resource, array $decodedArrayPayload)
    {
        $ret = array();
        foreach ($decodedArrayPayload as $i => $value) {
            $ret[] = $this->deserializeObjectResponse($resource, json_encode($value));
        }
        return $ret;
    }

    private function deserializeObjectResponse($resource, $jsonEncodedObjectPayload)
    {
        return $this->serializer->deserialize(
            $jsonEncodedObjectPayload,
            $this->getTypeForResource($resource, true),
            ZendeskApiClient::$TYPE_JSON
        );
    }


    private function getTypeForResource($resource, $isSingular = false)
    {
        if (!$isSingular) {
            $resource = substr_replace($resource, "", -1);
        }
        return 'Kunstmaan\FormBundle\Helper\Zendesk\Model\\' . ucfirst($resource);
    }

    /**
     * Checks if the field has to be created or updated. If so, perform the API call.
     *
     * Keeps an internal storage of the state on the API. This way we don't hit the rate limiter so soon.
     *
     * @param $fieldData
     */
    public function createOrUpdateField($fieldData)
    {
        throw new NotImplementedException('nop');
    }

    /**
     * Create the ticket with the data. All fields need to exist already.
     *
     * @param $ticketData
     */
    public function createTicket(Ticket $ticketData)
    {
        return $this->createCall('tickets', $ticketData);
    }

    public function createRequest(Request $requestData)
    {
        return $this->createCall('requests', $requestData);
    }

}