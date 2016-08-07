<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 25/07/2016
 * Time: 17:16
 */
class Blog extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('News_model', 'newsMgr');
    }

    public function page($page = 1)
    {
        $newsPerPage = 3;

        $this->twig->display('blog/index', array(
            'news' => $this->newsMgr->getLast($newsPerPage*($page-1), $newsPerPage),
            'cur_page' => $page,
            'nb_page' => ceil($this->newsMgr->count()/$newsPerPage),
        ));
    }

    public function article($id)
    {
        $this->twig->display('blog/page', array(
            'news' => $this->newsMgr->getById($id)
        ));
    }
};