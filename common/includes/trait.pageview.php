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
trait pageView
{
    /** @var string The selected view. */
    protected $view = null;
    /** @var array The list of views and their callbacks. */
    private $viewList = array();

    function processView()
    {
        $callback = $this->viewList[$this->view];
        if (isset($callback)) return call_user_func_array($callback, array(&$this));
        return null;
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

    /**
     * Return the set view.
     * @return string
     */
    function getView()
    {
        return $this->view;
    }

}