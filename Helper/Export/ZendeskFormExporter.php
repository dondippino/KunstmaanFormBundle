<?php

namespace Kunstmaan\FormBundle\Helper\Export;


use Gedmo\Exception\FeatureNotImplementedException;
use Kunstmaan\FormBundle\Helper\Export\FormExportableInterface;
use Kunstmaan\FormBundle\Helper\Export\FormExporterInterface;
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

        foreach ($submission->getFieldsForExport() as $field) {
            // TODO: Convert every field to a datastructure known by ZenDesk.
            //       We can either use Symfony's builtin conversion interface or do something else.
            //       The conversion should happen outside of this class so it can be re-used.

            // TODO: Convert the fields to a new Ticket.
            // TODO: Try and save the ticket.
        }

        // TODO: Write tests for the entire class.
        throw new FeatureNotImplementedException;
    }

    public function getName()
    {
        return 'zendesk';
    }
}
