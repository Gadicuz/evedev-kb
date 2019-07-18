<?php
/**
 * $Date$
 * $Revision$
 * $HeadURL$
 * @package EDK
 */

/*
 * @package EDK
 */
class pLocked extends pageAssemblyEx
{

    function __construct()
    {
        parent::__construct();

        $this->queue("start");
        $this->queue("content");
    }

    function start()
    {
        $this->page = new Page("Locked");
    }

    function content()
    {
        global $smarty;
        return $smarty->fetch(get_tpl("locked"));
    }

}

$locked = new pLocked();
$locked->generate("locked");
