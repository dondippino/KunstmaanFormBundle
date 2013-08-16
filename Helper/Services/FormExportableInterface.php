<?php

namespace Kunstmaan\FormBundle\Helper\Services;


interface FormExportableInterface
{

    /**
     * The identifier that's used to track if the form has already been exported or not.
     *
     * @return mixed
     */
    public function getIdentifier();

    /**
     * Returns a list of exporters as string. This is empty by default in the AbstractFormPage.
     *
     * @return string[]
     */
    public function getExportServices();

}