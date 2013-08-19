<?php

namespace Kunstmaan\FormBundle\Helper\Zendesk;


use Buzz\Browser;
use Buzz\Client\FileGetContents;
use Buzz\Listener\BasicAuthListener;
use Buzz\Message\Response;
use JMS\Serializer\Serializer;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\Ticket;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\User;
use Symfony\Component\Locale\Exception\NotImplementedException;


class ZendeskApiClient
{

    protected $apiKey;

    protected $domain;

    protected $login;

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

        $this->setLogin($userEmail);

        try {
            $function($this);
        } catch (\Exception $e) {
            $this->setLogin($originalLogin);
            throw new \Exception(sprintf('Error executing code while imporsonating user \'%s\'', $userEmail), 0, $e);
        }

        return true;
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
        $browser->addListener(new BasicAuthListener($this->login . '/token', $this->apiKey));

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
        $response = $browser->get($this->createUrl('?query=' . urlencode($user->getEmail())));

        $users = $this->deserialize($response);

        if (empty($users)) {
            // Create the new user.
            $browser = $this->getBrowser('users');

            // TODO: Add Content-Type: application/json
            $result = $browser->post($this->createUrl(), array('Content-Type' => 'application/json'), $this->serializeObject($user, 'users'));
            // TODO: Update ID.
            var_dump($result);

            return $user;
        }
    }


    private function serializeObject($object, $pluralResourceName)
    {
        $newObj = array();
        $newObj[ZendeskApiClient::$RESOURCES[$pluralResourceName]] = $object;
        return $this->serializer->serialize($newObj, 'json');
    }

    public static $TYPE_JSON = 'json';

    public static $RESOURCES = array('users' => 'user');

    /**
     * Throw an exception on error. When empty, return empty array.
     *
     * @param $response
     */
    private function deserialize(Response $response)
    {
        $rawResponse = json_decode($response->getContent(), true, 3);

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
                return $this->deserializeObjectResponse($resourcesHavingValue[0], $value);
                //return
            } elseif (count($resourcesHavingValue) > 1) {
                throw new \LogicException('Check ZendeskApiClient::$RESOURCES. Should not have duplicates for the values.');
            }

            // return null;
        }
    }

    private function deserializeArrayResponse($resource, array $decodedArrayPayload)
    {
        $ret = array();
        foreach ($decodedArrayPayload as $nestedResource => $value) {
            $ret[] = $this->deserializeObjectResponse($nestedResource, json_encode($resource));
        }
        return $ret;
    }

    private function deserializeObjectResponse($resource, $jsonEncodedObjectPayload)
    {
        return $this->serializer->deserialize($jsonEncodedObjectPayload, ZendeskApiClient::$TYPE_JSON, $this->getTypeForResource($resource));
    }


    private function getTypeForResource($resource, $isSingular = false)
    {
        if (!$isSingular) {
            $resource = substr_replace($resource, "", -1);
        }
        return '\Kunstmaan\FormBundle\Helper\Zendesk\Model\\' . ucfirst($resource);
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
        throw new NotImplementedException('nop');

    }


    /**
     * @param $email
     *
     * @return null|User
     */
    public function findUserByEmail($email)
    {
        throw new NotImplementedException('nop');
    }
}