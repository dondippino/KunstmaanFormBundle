<?php

namespace Kunstmaan\FormBundle\Helper\Export;


use Gedmo\Exception\FeatureNotImplementedException;
use Kunstmaan\FormBundle\Helper\Export\FormExportableInterface;
use Kunstmaan\FormBundle\Helper\Export\FormExporterInterface;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\Request;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\Ticket;
use Kunstmaan\FormBundle\Helper\Zendesk\Model\User;
use Kunstmaan\FormBundle\Helper\Zendesk\ZendeskApiClient;


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


        // How t oget email? WTF :x


        $message = $name = $email = null;

        // TODO: Fetch the fields via a new mechanism that allows aliases to be set to formfields.
        // The FormExportableInterface will then be able to read out these aliased fields
        // and will complain if one isn't found.

        $message = "testing!\nnew like!";
        $name = "Vincent API Test";
        $email = "vincent+zendesk_api_test_1@supervillain.be";
        $subject = 'Customer Feedback';

        foreach ($submission->getFieldsForExport() as $field) {
            // TODO: Convert every field to a datastructure known by ZenDesk.
            //       We can either use Symfony's builtin conversion interface or do something else.
            //       The conversion should happen outside of this class so it can be re-used.

            // TODO: Convert the fields to a new Ticket.
            // TODO: Try and save the ticket.
            //$ticket = new Ticket();
            //$ticket->set;

            // TEMP: Code to test that how API actually works using a temporary library.
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

        $this->apiClient->createTicket($ticket);

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
}
