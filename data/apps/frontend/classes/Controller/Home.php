<?php

class Controller_Home extends fvController
{

    /**
     * @route /
     */
    function indexAction()
    {
        $this->view()->assignParams([
            'navbar' => new Block_Navbar(),
            'slider' => new Block_Slider(),
            'products' => new Block_Products()
        ]);
    }

}