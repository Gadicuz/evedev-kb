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
class pAwards extends pageAssembly
{
	/** @var array */
	private $menuOptions = array();
	/** @var array */
	private $viewList = array();
	/** @var integer */
	protected $week;
	/** @var integer */
	protected $month;
	/** @var integer */
	protected $year;

	public $page;
	
	private $nmonth;
	private $pmonth;
	private $nyear;
	private $pyear;
	private $monthname;

	protected $view;

	/**
	 * Construct the Alliance Details object.
	 * Set up the basic variables of the class and add the functions to the
	 *  build queue.
	 */
	function __construct()
	{
		parent::__construct();

		$this->queue("start");
		$this->queue("awards");
	}
	/**
	 * Start constructing the page.
	 * Prepare all the shared variables.
	 *
	 */
	function start()
	{
		$this->page = new Page(Language::get('page_awards'));
		$this->page->addHeader('<meta name="robots" content="index, follow" />');

		$this->page->addHeader("<link rel='canonical' href='".edkURI::build(array('a', 'awards', true))."' />");

		$this->year = (int)edkURI::getArg('y', 1);
		$this->month = (int)edkURI::getArg('m', 2);
		if(!$this->month){
			$this->month = kbdate('m') - 1;
		}
		if(!$this->year) {
			$this->year = kbdate('Y');
		}

		if ($this->month == 0) {
			$this->month = 12;
			$this->year = $this->year - 1;
		}

		if ($this->month == 12) {
			$this->nmonth = 1;
			$this->nyear = $this->year + 1;
		} else {
			$this->nmonth = $this->month + 1;
			$this->nyear = $this->year;
		}
		if ($this->month == 1) {
			$this->pmonth = 12;
			$this->pyear = $this->year - 1;
		} else {
			$this->pmonth = $this->month - 1;
			$this->pyear = $this->year;
		}

		$this->monthname = kbdate("F", strtotime("2000-".$this->month."-2"));

		if (!edkURI::getArg('y', 1)) {
			$this->view = edkURI::getArg('view', 1);
		} else {
			$this->view = edkURI::getArg('view', 3);
		}

		$this->viewList = array();

		$this->menuOptions = array();

	}
	function awards()
	{
		global $smarty;
		$awardboxes = array();
		// top killers
		$tklist = new TopList_Kills();
		$tklist->setMonth($this->month);
		$tklist->setYear($this->year);
		involved::load($tklist,'kill');

		$tklist->generate();
		$tkbox = new AwardBox($tklist, Language::get('topkillers'), Language::get('kills'), "kills", "eagle");
		$awardboxes[] = $tkbox->generate();
		// top scorers
		if (config::get('kill_points'))
		{
			$tklist = new TopList_Score();
			$tklist->setMonth($this->month);
			$tklist->setYear($this->year);
			involved::load($tklist,'kill');

			$tklist->generate();
			$tkbox = new AwardBox($tklist, Language::get('topscorers'), Language::get('top_points'), "points", "redcross");
			$awardboxes[] = $tkbox->generate();
		}
		// top solo killers
		$tklist = new TopList_SoloKiller();
		$tklist->setMonth($this->month);
		$tklist->setYear($this->year);
		involved::load($tklist,'kill');

		$tklist->generate();
		$tkbox = new AwardBox($tklist, Language::get('top_solo'), Language::get('top_solo_desc'), "kills", "cross");
		$awardboxes[] = $tkbox->generate();
		// top damage dealers
		$tklist = new TopList_DamageDealer();
		$tklist->setMonth($this->month);
		$tklist->setYear($this->year);
		involved::load($tklist,'kill');

		$tklist->generate();
		$tkbox = new AwardBox($tklist, Language::get('top_damage'), Language::get('top_damage_desc'), "kills", "wing1");
		$awardboxes[] = $tkbox->generate();

		// top final blows
		$tklist = new TopList_FinalBlow();
		$tklist->setMonth($this->month);
		$tklist->setYear($this->year);
		involved::load($tklist,'kill');

		$tklist->generate();
		$tkbox = new AwardBox($tklist, Language::get('top_final'), Language::get('top_final_desc'), "kills", "skull");
		$awardboxes[] = $tkbox->generate();
		// top podkillers
		$tklist = new TopList_Kills();
		$tklist->setMonth($this->month);
		$tklist->setYear($this->year);
		involved::load($tklist,'kill');

		$tklist->addVictimShipClass(2); // pod

		$tklist->generate();
		$tkbox = new AwardBox($tklist, Language::get('top_podkill'), Language::get('top_podkill_desc'), "kills", "globe");
		$awardboxes[] = $tkbox->generate();
		// top griefers
		$tklist = new TopList_Kills();
		$tklist->setMonth($this->month);
		$tklist->setYear($this->year);
		involved::load($tklist,'kill');

		$tklist->addVictimShipClass(20); // freighter
		$tklist->addVictimShipClass(22); // exhumer
		$tklist->addVictimShipClass(7); // industrial
		$tklist->addVictimShipClass(12); // barge
		$tklist->addVictimShipClass(14); // transport
		$tklist->addVictimShipClass(39); // industrial command
		$tklist->addVictimShipClass(43); // exploration ship
		$tklist->addVictimShipClass(29); // capital industrial


		$tklist->generate();
		$tkbox = new AwardBox($tklist, Language::get('top_griefer'), Language::get('top_griefer_desc'), "kills", "star");
		$awardboxes[] = $tkbox->generate();
		// top capital killers
		$tklist = new TopList_Kills();
		$tklist->setMonth($this->month);
		$tklist->setYear($this->year);
		involved::load($tklist,'kill');

		$tklist->addVictimShipClass(20); // freighter
		$tklist->addVictimShipClass(19); // dread
		$tklist->addVictimShipClass(27); // carrier
		$tklist->addVictimShipClass(28); // mothership
		$tklist->addVictimShipClass(26); // titan
		$tklist->addVictimShipClass(29); // cap. industrial

		$tklist->generate();
		$tkbox = new AwardBox($tklist, Language::get('top_isk_kill'), Language::get('top_isk_kill_desc'), "kills", "wing2");
		$awardboxes[] = $tkbox->generate();

		$smarty->assignByRef('awardboxes', $awardboxes);
		$smarty->assign('month', $this->monthname);
		$smarty->assign('year', $this->year);
		$smarty->assign('boxcount', count($awardboxes));

		$smarty->assign('page_title', Language::get('page_awards_for')." ".$this->monthname." ".$this->year);
		return $smarty->fetch(get_tpl('awards'));
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
	 * Build the menu.
	 *  Additional options that have been set are added to the menu.
	 */
	function menu()
	{
		$menubox = new Box("Menu");
		$menubox->setIcon("menu-item.gif");
		foreach($this->menuOptions as $options)
		{
			if(isset($options[2]))
				$menubox->addOption($options[0],$options[1], $options[2]);
			else
				$menubox->addOption($options[0],$options[1]);
		}
		return $menubox->generate();
	}
	/**
	 * Set up the menu.
	 *
	 * Additional options that have been set are added to the menu.
	 */
	function menuSetup()
	{
		$this->addMenuItem("caption", "Navigation");
		$this->addMenuItem("link", "Previous month ", edkURI::build(array('y', $this->pyear, true), array('m', $this->pmonth, true)));
		if (! ($this->month == kbdate("m") - 1 && $this->year == kbdate("Y")))
			$this->addMenuItem("link", "Next month", edkURI::build(array('y', $this->nyear, true), array('m', $this->nmonth, true)));
	}
	/**
	 * Add an item to the menu in standard box format.
	 *
	 *  Only links need all 3 attributes
	 * @param string $type Types can be caption, img, link, points.
	 * @param string $name The name to display.
	 * @param string $url Only needed for URLs.
	 */
	function addMenuItem($type, $name, $url = '')
	{
		$this->menuOptions[] = array($type, $name, $url);
	}

	/**
	 * Add a type of view to the options.
	 *
	 * @param string $view The name of the view to recognise.
	 * @param mixed $callback The method to call when this view is used.
	 */
	function addView($view, $callback)
	{
		$this->viewList[$view] = $callback;
	}
}

$award = new pAwards();
event::call("award_assembling", $award);
$html = $award->assemble();
$award->page->setContent($html);

$award->context();
event::call("award_context_assembling", $award);
$context = $award->assemble();
$award->page->addContext($context);

$award->page->generate();