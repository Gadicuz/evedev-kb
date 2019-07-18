<?php
/**
 * $Date$
 * $Revision$
 * $HeadURL$
 * @package EDK
 */
require_once('common/includes/trait.contextmenu.php');

/*
 * @package EDK
 */
class pInvtype extends pageAssemblyEx
{
    use contextMenu;

    /** @var integer */
    public $typeID;
        
    function __construct()
    {
        parent::__construct();

        $this->queue("start");
        $this->queue("details");
    }

    function start()
    {
        $this->typeID = edkURI::getArg('id', 1);
        $this->page = new Page('Item Details');

    }
        
        /**
     *  Reset the assembly object to prepare for creating the context.
     */
    function context()
    {       
                parent::__construct();
                $item = new dogma($this->typeID);

        if (!$item->isValid())
        {
            $this->page->setTitle('Error');
            return 'This ID is not a valid dogma ID.';
        }
               
                // display context menu only for ships
        if ($item->get('itt_cat') == 6)
                {
                    
                    $this->queue("menuSetup");
                    $this->queue("menu");
                }
    }
        
        /**
     * Set up the menu.
     *
     *  Prepare all the base menu options.
     */
    function menuSetup()
    {
        $args = array();
        $args[] = array('id', $this->typeID, true);

        $this->addMenuItem("link","Description", edkURI::build($args));
        $this->addMenuItem("link","Kills", edkURI::build($args, array('view', 'kills', true)));
        $this->addMenuItem("link","Losses", edkURI::build($args, array('view', 'losses', true)));
        return "";
    }

    function details()
    {
        global $smarty;
        $item = new dogma($this->typeID);

        if (!$item->isValid())
        {
            $this->page->setTitle('Error');
            return 'This ID is not a valid dogma ID.';
        }

        $this->page->setTitle('Item details - '.$item->get('typeName'));
        $this->page->addHeader('<meta name="robots" content="noindex, nofollow" />');
        $smarty->assignByRef('item', $item);

        if ($item->get('itt_cat') == 6)
        {
            //we have a ship, so get it from the db
            $ship = Ship::getByID($item->get('typeID'));
            $smarty->assign('shipImage', $ship->getImage(64));
                        $smarty->assign('traits', $ship->getTraitsHtml());
                        
                        $view = edkURI::getArg('view', 2);
                        $killList = '';
                        if($view == 'kills')
                        {
                            $list = new KillList();
                            $list->setOrdered(true);
                            $list->addInvolvedShipType($this->typeID);
                            $list->setPageSplit(config::get('killcount'));
                            $pagesplitter = new PageSplitter($list->getCount(), config::get('killcount'));
                            $table = new KillListTable($list);
                            $table->setDayBreak(false);
                            $html = $smarty->fetch(get_tpl('invtype_ship_killlist'));
                            
                            $smarty->assign('splitter',$pagesplitter->generate());
                            $smarty->assign('kills', $table->generate());
                            $html .= $smarty->fetch(get_tpl('detail_kl_kills'));
                        }
                        
                        else if($view == 'losses')
                        {
                            $list = new KillList();
                            $list->setOrdered(true);
                            $list->addVictimShipType($this->typeID);
                            $list->setPageSplit(config::get('killcount'));
                            $pagesplitter = new PageSplitter($list->getCount(), config::get('killcount'));

                            $table = new KillListTable($list);
                            $table->setDayBreak(false);
                            $html = $smarty->fetch(get_tpl('invtype_ship_killlist'));
                            
                            $smarty->assign('splitter',$pagesplitter->generate());
                            $smarty->assign('losses', $table->generate());
                            $html .= $smarty->fetch(get_tpl('detail_kl_losses'));
                        }
                        
                        else
                        {
                            $smarty->assign('armour', array('armorHP','armorEmDamageResonance',
                                    'armorExplosiveDamageResonance','armorKineticDamageResonance',
                                    'armorThermalDamageResonance'));
                            $smarty->assign('shield', array('shieldCapacity','shieldRechargeRate',
                                    'shieldEmDamageResonance','shieldExplosiveDamageResonance',
                                    'shieldKineticDamageResonance','shieldThermalDamageResonance'));
                            $smarty->assign('propulsion', array('maxVelocity','agility','droneCapacity',
                                    'capacitorCapacity','rechargeRate'));
                            $smarty->assign('fitting', array('hiSlots','medSlots','lowSlots','rigSlots',
                                    'upgradeCapacity','droneBandwidth','launcherSlotsLeft','turretSlotsLeft',
                                    'powerOutput','cpuOutput'));
                            $smarty->assign('targetting', array('maxTargetRange','scanResolution',
                                    'maxLockedTargets','scanRadarStrength','scanLadarStrength',
                                    'scanMagnetometricStrength','scanGravimetricStrength','signatureRadius'));
                            $smarty->assign('miscellaneous', array('techLevel','propulsionFusionStrength',
                                    'propulsionIonStrength','propulsionMagpulseStrength',
                                    'propulsionPlasmaStrength'));
                            
                            $html = $smarty->fetch(get_tpl('invtype_ship'));
                            
                        }

        }
        else
        {
            $i = new Item($this->typeID);
            $smarty->assign('itemImage', $i->getIcon(64, false));
            $html = $smarty->fetch(get_tpl('invtype_item'));
        }
        return $html;
    }
    
    function getTypeID() 
    {
        return $this->typeID;
    }

}


$invtype = new pInvtype();
$invtype->assemble("invtype");
