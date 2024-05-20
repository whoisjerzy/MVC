<?php

namespace BARTENDER\Controllers;

use BARTENDER\Classes\View;

class PageController
{
    private $view;

    public function __construct()
    {
        // Initialize the View class with the path to your view files
        $this->view = new View(__DIR__ . '/../Views');
    }

    // public function index()
    // {
    //     $components = [
    //         'modals' => ['modals/view-drink-modal'], // Include the modal component
    //         // Add more components as needed
    //     ];
    //     // Render the 'index' view file
    //     $this->view->setLayout('main'); // Set the layout
    //     return $this->view->render('index', ['title' => 'Homepage'], true);
    // }

    public function index()
    {
        $components = [
            'modals/view-drink-modal' => [],

            // Add more components as needed
        ];
        // Render the 'index' view file with components and layout
        return $this->view->getViewWithComponentsAndLayout('index', ['title' => 'Homepage'], $components, 'main');
    }


    public function login()
    {
        $this->view->setLayout('auth'); // Set the layout
        return $this->view->render('auth/login', ['title' => 'Login'], true);
    }

    public function register()
    {
        $this->view->setLayout('auth'); // Set the layout
        return $this->view->render('auth/register', ['title' => 'Register'], true);
    }

    public function admin()
    {
        $components = [
            'modals/view-drink-modal' => [],

            // Add more components as needed
        ];
        // Render the 'index' view file with components and layout
        return $this->view->getViewWithComponentsAndLayout('admin/index', ['title' => 'Homepage'], $components, 'root');
    }
}
