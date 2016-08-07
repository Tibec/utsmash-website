<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 25/07/2016
 * Time: 17:16
 */
class Dashboard extends AdminController
{
    public function index()
    {
        $this->output->enable_profiler(TRUE);
    }
};