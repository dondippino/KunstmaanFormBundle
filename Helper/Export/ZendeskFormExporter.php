<?php

namespace Kunstmaan\FormBundle\Helper\Export;


use Gedmo\Exception\FeatureNotImplementedException;
use Kunstmaan\FormBundle\Helper\Export\FormExportableInterface;
use Kunstmaan\FormBundle\Helper\Export\FormExporterInterface;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\Request;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\Ticket;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\TicketField;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\User;
use Kunstmaan\FormBundle\Helper\Zendesk\ZendeskApiClient;


/**
 * Depends on the following field keys to exist.
 * - Either first_name & last_name or just name
 * - email
 * - message
 * - subject (todo: Could come from a config where you tell which node id has to have which values by default)
 */
class ZendeskFormExporter implements FormExporterInterface
{
    /**
     * @var ZendeskApiClient
     */
    protected $apiClient;

    /**
     * @param ZendeskApiClient $value
     *
     * @return $this
     */
    public function setApiClient($value)
    {
        $this->apiClient = $value;

        return $this;
    }

    protected $entityManager;

    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }


    public function export(FormExportableInterface $submission)
    {
        /**
         * Use the Zendesk REST API to add the forms.
         * http://developer.zendesk.com/documentation/rest_api/introduction.html
         *
         * General stuff to add are:
         * - ID of the page
         * - Language of the submission
         * - Name of the page
         * - Tag if possible
         * - Fields (create if needed)
         *
         * Try and make hook for adding custom properties of your submission.
         * A FormSubmission has a Node hooked to it.
         * Would be cool if we had an interface that it could implement to tell us what to export.
         *
         * Also check if we can update or not in Zentrick. If so we could also provide an update mechanism.
         */

        $fields = $submission->getFieldsForExport($this->entityManager);
        $message = $this->findInFields('message', $fields);
        $email = $this->findInFields('email', $fields);
        $firstName = $this->findInFields('first_name', $fields);
        $lastName = $this->findInFields('last_name', $fields);
        $name = $this->findInFields('name', $fields);
        $subject = $this->findInFields('subject', $fields);

        $customFields = array();
        foreach ($fields as $key => $value) {
            switch ($key) {
                case 'email':
                case 'message':
                case 'first_name':
                case 'last_name':
                case 'name':
                case 'subject':
                    break;
                default:
                    // Store to create a field for later.
                    $customFields[$key] = $value;
            }
        }

        if (empty($name)) {
            $name = $firstName.' '.$lastName;
        }

        if (empty($subject)) {
            $subject = mb_substr($message, 0, 50);
        }

        $user = new User();
        $user->setName($name)
            ->setRole('end-user')
            ->setEmail($email)
            ->setTags('do_not_email');

        // Don't do this as the impersonated user.
        $user = $this->apiClient->createUserIfNotPresent($user);

        $ticket = new Ticket();
        $ticket
            ->setSubject($subject)
            ->setDescription($message)
            ->setRequesterId($user->getId())
            ->setTags('do_not_email');

        $createdAt = $submission->getCreationDate();
        $import = ($createdAt < new \DateTime('-7 days'));

        $ticket = $this->apiClient->createTicket($ticket, $customFields, $import);

        if ((is_null($ticket)) || is_null($ticket->getId())) {
            return false;
        }

        return true;

        /*
        // TODO: Get imporsonation working for creating a Request.
        $this->apiClient->runAs($user->getEmail(), function(ZendeskApiClient $client) use ($name, $email, $subject, $message) {
            $request = new Request();
            $request->setSubject($subject)
                   ->setDescription($message);

            $client->createRequest($request);
        });
        */
    }


    public function getName()
    {
        return 'zendesk';
    }


    private function findInFields($name, array $fields)
    {
        if (array_key_exists($name, $fields)) {
            return $fields[$name];
        }

        return null;
    }
}
