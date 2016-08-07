<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 25/07/2016
 * Time: 17:16
 */
class Blog extends AdminController
{
    public function page($page = 0)
    {
        $this->output->enable_profiler(TRUE);
    }

    public function article($id)
    {
        $this->output->enable_profiler(TRUE);
    }
};