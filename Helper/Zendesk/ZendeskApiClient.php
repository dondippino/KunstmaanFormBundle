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
use Kunstmaan\FormBundle\Helper\Exceptions\RateLimitExceededException;
use Kunstmaan\FormBundle\Helper\Exceptions\ServerSideException;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\Request;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\Ticket;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\TicketField;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\TicketFieldEntry;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\User;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Locale\Exception\NotImplementedException;


// TODO: Split out actual call & serialize logic.

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
        $this->createUrl($endpoint, $action, $id);

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

    private function createUrl($endpoint, $action = '', $id = null)
    {
        if (!is_null($id)) {
            $id = '/' . $id;
        }
        if (!is_null($action) && (!empty($action))) {
            $action = '/' . $action;
        }

        $newUrl = 'https://' . $this->domain . '.zendesk.com/api/v2/' . $endpoint . $action . $id . '.json';
        $this->url = $newUrl;

        return $this->url;
    }

    private function expandUrl($toAppend = '')
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
        $users = $this->getCall($browser, $this->expandUrl('?query=' . urlencode($user->getEmail())));

        if (empty($users)) {
            return $this->createCall('users', $user);
        }

        return $users[0];
    }

    /**
     * @param string $pluralResource Resource in plural form.
     * @param object $object The object you want to create.
     * @param string $url If provided this URL will be used.
     *
     * @return object The new object with its ID filled in.
     */
    private function createCall($pluralResource, $object, $url = '')
    {
        $browser = $this->getBrowser($pluralResource);

        $singularResource = $this->getSingularResourceNameFromPluralResourceName($pluralResource);

        if (empty($url)) {
            $url = $this->expandUrl();
        }
        $result = $browser->post($url, $this->requestheaders, $this->serializeObject($object, $singularResource));

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


    private function serializeObject($object, $singularResourceName)
    {
        $newObj = array();
        $newObj[$singularResourceName] = $object;
        return $this->serializer->serialize($newObj, 'json');
    }

    public static $TYPE_JSON = 'json';

    /**
     * Maps the online resource name to the actual class name. For plural aka collections.
     * @var array
     */
    public static $RESOURCES_PLURAL = array(
        'users' => 'User',
        'tickets' => 'Ticket',
        'requests' => 'Request',
        'ticket_fields' => 'TicketField',
    );

    /**
     * Maps the online resource name to the actual class name. For singular aka single objects.
     * @var array
     */
    public static $RESOURCES_SINGULAR = array(
        'user' => 'User',
        'ticket' => 'Ticket',
        'request' => 'Request',
        'ticket_field' => 'TicketField',
    );

    /**
     * @param string $pluralResourceName
     * @return string
     */
    private function getSingularResourceNameFromPluralResourceName($pluralResourceName)
    {
        $className = ZendeskApiClient::$RESOURCES_PLURAL[$pluralResourceName];
        return array_search($className, ZendeskApiClient::$RESOURCES_SINGULAR);
    }

    private static function startsWith($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

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

        // If 429, too many requests.
        if ($response->getStatusCode() == 429) {
            // TODO: Parse Retry-After header. Contains time to wait for in seconds.
            $headers = $response->getHeaders();
            $timeoutInSeconds = 3600;
            foreach ($headers as $header) {
                if (ZendeskApiClient::startsWith($header, 'Retry-After')) {
                    $split = explode(':', $header);
                    $headerValue = end($split);
                    $timeoutInSeconds = (integer)$headerValue;
                }
            }
            throw new RateLimitExceededException($rawResponse['error'], 'zendesk', $timeoutInSeconds);
        }

        if ($response->isClientError()) {
            throw new ClientSideException($response->getStatusCode() . ':' . $rawResponse['error']);
        }

        if ($response->isServerError()) {
            throw new ServerSideException($response->getStatusCode() . ':' . $rawResponse['error']);
        }

        // Here we should have some sort of standardized response. Either a collection of resources or a single resource.
        // Detection is done by the key of the hash. if its singular, deserialize the value as a single object.
        // Otherwise deserialize all objects in the value as this type of resource.
        foreach ($rawResponse as $resource => $value) {
            if (array_key_exists($resource, ZendeskApiClient::$RESOURCES_PLURAL)) {
                return $this->deserializeArrayResponse($resource, $value);
            }

            if (array_key_exists($resource, ZendeskApiClient::$RESOURCES_SINGULAR)) {
                return $this->deserializeObjectResponse($resource, json_encode($value));
            }
        }
    }

    /**
     * @param $resource
     * @param array $decodedArrayPayload has to be decoded since we're looping over all of them.
     *
     * @return object[]
     */
    private function deserializeArrayResponse($pluralResourceName, array $decodedArrayPayload)
    {
        $ret = array();
        $singularResourceName = $this->getSingularResourceNameFromPluralResourceName($pluralResourceName);
        foreach ($decodedArrayPayload as $value) {
            $ret[] = $this->deserializeObjectResponse($singularResourceName, json_encode($value));
        }
        return $ret;
    }



    /**
     * @param $resource
     * @param $jsonEncodedObjectPayload
     *
     * @return object
     */
    private function deserializeObjectResponse($singularResourceName, $jsonEncodedObjectPayload)
    {
        return $this->serializer->deserialize(
            $jsonEncodedObjectPayload,
            $this->getTypeForResource($singularResourceName, true),
            ZendeskApiClient::$TYPE_JSON
        );
    }


    private function getTypeForResource($resource, $isSingular = false)
    {
        if ($isSingular) {
            $class = ZendeskApiClient::$RESOURCES_SINGULAR[$resource];
        } else {
            $class = ZendeskApiClient::$RESOURCES_PLURAL[$resource];
        }
        return 'Kunstmaan\FormBundle\Helper\Zendesk\Model\\' . $class;
    }

    /**
     * Create the ticket with the data. All fields need to exist already.
     *
     * @param $ticketData
     * @param array $customFields
     * @param boolean $isImport When true it'll access a different endpoint specifically for import.
     *
     * @return Ticket The newly created Ticket instance.
     */
    public function createTicket(Ticket $ticketData, array $customFields, $isImport = false)
    {
        foreach ($customFields as $key => $value) {
            // Assure we have fields for everything.
            $field = $this->findOrCreateTicketFieldFor($key);

            $fieldEntry = new TicketFieldEntry();
            $fieldEntry->setId($field->getId());
            $fieldEntry->setValue($value);

            $ticketData->addCustomField($fieldEntry);
        }

        $url = '';
        if ($isImport) {
            $url = $this->createUrl('imports', 'tickets');
        }
        return $this->createCall('tickets', $ticketData, $url);
    }

    public function createRequest(Request $requestData)
    {
        return $this->createCall('requests', $requestData);
    }



    /**
     * @param string $ticketKey
     *
     * @return TicketField
     */
    public function findOrCreateTicketFieldFor($ticketKey)
    {
        $field = $this->findTicketFieldForKey($ticketKey);

        if (is_null($field)) {
            $field = $this->createTicketFieldForKey($ticketKey);
        }

        return $field;
    }


    /** @var TicketField[] */
    private $ticketFields;
    public function getAllTicketFields()
    {
        if (is_null($this->ticketFields)) {
            $browser = $this->getBrowser('ticket_fields');
            $this->ticketFields = $this->getCall($browser, $this->expandUrl());
        }

        return $this->ticketFields;
    }

    private function findTicketFieldForKey($key)
    {
        foreach ($this->getAllTicketFields() as $field) {
            if ($field->title == $key) {
                return $field;
            }
        }

        return null;
    }

    private function createTicketFieldForKey($key)
    {
        $new = new TicketField();
        $new->title = $key;
        $new->titleInPortal = $this->titleizeKey($key);
        // TODO: Based on the value we could do something fancier here.
        // Or perhaps even pass in an array with some options.
        $new->type = 'text';

        return $this->createCall('ticket_fields', $new);
    }

    /**
     * Converts opt_in to Opt In for example.
     *
     * @param $key
     */
    private function titleizeKey($key)
    {
        $key = str_replace('_', ' ', $key);
        $key = ucwords($key);
        return $key;
    }
}