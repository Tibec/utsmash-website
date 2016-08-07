<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 25/07/2016
 * Time: 17:16
 */
class Ranking extends AdminController
{
    public function index($semester = "", $include_ext = false)
    {
        $this->output->enable_profiler(TRUE);
    }
};