<?php

namespace Kunstmaan\FormBundle\Helper\Export;


/**
 * This interface allows for something to be disabled for a period of time.
 */
interface TimeoutableInterface
{
    public function removeTimeout();

    public function setTimeoutPeriodInSeconds($periodInSeconds);

    public function isInTimeout();
}
