<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 25/07/2016
 * Time: 18:47
 */

class MY_Controller extends  CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        //$this->output->enable_profiler(TRUE);

        $this->load->helper('url');

        $this->load->library('session');
        $this->load->library('twig');

        $this->load->model('User_model', 'userMgr');
        $this->load->model('Tournament_model', 'tournamentMgr');

        if($this->session->user_id == null)
            $this->session->user_id = 0;

        if($this->session->user_id > 0)
        {
            $this->session->user = $this->userMgr->getMenuData($this->session->user_id);
        }

        $this->twig->addGlobal('user_id', $this->session->user_id);
        $this->twig->addGlobal('user', $this->session->user);
        $this->twig->addGlobal('upcoming_nav', $this->tournamentMgr->getUpcomingTournamentName());

        $this->twig->addGlobal('current_url', $this->router->class.'/'.$this->router->method);
    }

    public function requireAuth()
    {
        if($this->session->user_id <= 0) {
            $this->session->redirect_url=$this->uri->uri_string();
            redirect('user/login');
        }
    }

    public function requireFakeAuth()
    {
        if($this->session->user_id == 0) {
            $this->session->redirect_url=$this->uri->uri_string();
            redirect('user/login');
        }
    }

    public function requireNoAuth()
    {
        if($this->session->user_id != 0) {
            redirect('/');
        }
    }
}

class BaseController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();


    }
}

class AdminController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if($this->session->adminFlag == null)
            redirect('error/403');

    }
}