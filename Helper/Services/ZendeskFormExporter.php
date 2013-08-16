<?php

namespace Kunstmaan\FormBundle\Helper\Services;


use Gedmo\Exception\FeatureNotImplementedException;
use Kunstmaan\FormBundle\Entity\FormSubmission;

class ZendeskFormExporter implements FormExporterInterface
{
    protected $apiKey;
    protected $domain;

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setApiKey($value)
    {
        $this->apiKey = $value;

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setDomain($value)
    {
        $this->domain = $value;

        return $this;
    }

    public function export(FormSubmission $submission)
    {
        /**
         * Use the Zendesk REST API to add the forms.
         * http://developer.zendesk.com/documentation/rest_api/introduction.html
         *
         * General stuff to add are:
         * - ID of the page
         * - Language of the submission
         * - Fields (create if needed)
         *
         * Try and make hook for adding custom properties of your submission.
         * A FormSubmission has a Node hooked to it.
         * Would be cool if we had an interface that it could implement to tell us what to export.
         *
         * Also check if we can update or not in Zentrick. If so we could also provide an update mechanism.
         */
        throw new FeatureNotImplementedException;
    }
}
