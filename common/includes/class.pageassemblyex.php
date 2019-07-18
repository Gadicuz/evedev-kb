<?php

/**
 * $Date$
 * $Revision$
 * $HeadURL$
 * @package EDK
 */

/**
 * @package EDK
 */
class pageAssemblyEx extends pageAssembly
{
    /** @var Page The Page used to create this page.*/
    public $page = null;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Reset context for the page. Redeclare in a child class.
     *
     */
    protected function context()
    {
        //This resets the queue and queues context items.
        parent::__construct(); 
    }

    /**
     * Generate output HTML for the page.
     *
     * @param name $name The base name of event(s) to fire.
     */
    public function assemble($name)
    {
        event::call($name."_assembling", $this);
        $html = parent::assemble();
        $this->page->setContent($html);
         
        $this->context();
        event::call($name."_context_assembling", $this);
        $context = parent::assemble();
        if (strlen($context) > 0)
            $this->page->addContext($context);

        $this->page->generate();
    }

}
                         	