<?php
/**
 * $Date$
 * $Revision$
 * $HeadURL$
 * @package EDK
 */
require_once('common/includes/trait.contextmenu.php');
require_once('common/includes/trait.pageview.php');

$page = new Page('Campaigns');
/*
 * @package EDK
 */
class pCampaignList extends pageAssemblyEx
{
    use contextMenu;
    use pageView;

    /**
     * Construct the Contract Details object.
     * Set up the basic variables of the class and add the functions to the
     *  build queue.
     */
    function __construct()
    {
        parent::__construct();
        $this->queue("start");
        $this->queue("listCampaigns");

    }
    /**
     *  Reset the assembly object to prepare for creating the context.
     */
    function context()
    {
        parent::__construct();
        $this->queue("menuSetup");
        $this->queue("menu");
    }
    /**
     * Start constructing the page.
     * Prepare all the shared variables such as dates and check alliance ID.
     *
     */
    function start()
    {
        $this->page = new Page();

        $this->view = preg_replace('/[^a-zA-Z0-9_-]/','', edkURI::getArg('view', 1));
    }
    /**
     *  Show the list of campaigns.
     */
    function listCampaigns()
    {
        $v = $this->processView();
        if ( isset($v) ) return $v;

        $pageNum = (int)edkURI::getArg('page');

        switch ($this->view)
        {
            case '':
                $activelist = new ContractList();
                $activelist->setActive('yes');
                $this->page->setTitle('Active campaigns');
                $table = new ContractListTable($activelist);
                $table->paginate(10, $pageNum);
                return $table->generate();
                break;
            case 'past':
                $pastlist = new ContractList();
                $pastlist->setActive('no');
                $this->page->setTitle('Past campaigns');
                $table = new ContractListTable($pastlist);
                $table->paginate(10, $pageNum);
                return $table->generate();
                break;
        }
        return $html;
    }
    /**
     * Set up the menu.
     *
     *  Prepare all the base menu options.
     */
    function menuSetup()
    {
        $this->addMenuItem('link', 'Active campaigns', KB_HOST.'/?a=campaigns');
        $this->addMenuItem('link', 'Past campaigns', KB_HOST.'/?a=campaigns&amp;view=past');
        return "";
    }

}

$campaignList = new pCampaignList();
$campaignList->assemble("campaignList");
