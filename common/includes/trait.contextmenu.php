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
trait contextMenu
{
    /** @var array The list of menu options to display. */
    protected $menuOptions = array();

    /**
     * Build the menu.
     *
     *  Add all preset options to the menu.
     */
    function menu()
    {
        $menubox = new box("Menu");
        $menubox->setIcon("menu-item.gif");
        foreach($this->menuOptions as $options)
            call_user_func_array(array($menubox, 'addOption'), $options);
        return $menubox->generate();
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
    * Removes the menu item with the given name
    * 
    * @param string $name the name of the menu item to remove
    */
   function removeMenuItem($name)
   {
       foreach((array)$this->menuOptions AS $menuItem)
       {
           if(count($menuItem) > 1 && $menuItem[1] == $name)
           {
               unset($this->menuOptions[key($this->menuOptions)]);
           }
       }
   }

}