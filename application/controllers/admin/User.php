<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 25/07/2016
 * Time: 17:16
 */
class User extends AdminController
{
    public function index()
    {
        $this->output->enable_profiler(TRUE);
    }
    public function edit($id)
    {
        $this->output->enable_profiler(TRUE);
    }

    public function create()
    {

    }

    public function delete($id)
    {

    }
};