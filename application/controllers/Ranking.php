<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 25/07/2016
 * Time: 17:16
 */
class Ranking extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Ranking_model', 'rankingMgr');
    }

    public function index($game = "ssb4" , $semester = "P16", $include_ext = false)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('game', 'Jeu', 'required');
        $this->form_validation->set_rules('period', 'Semestre', 'required');
        if ($this->form_validation->run())
        {
            if($this->input->post('ext'))
                redirect('ranking/'.$this->input->post('game').'/'.$this->input->post('period').'/with_ext');
            else
                redirect('ranking/'.$this->input->post('game').'/'.$this->input->post('period'));

        }
        else {
            $rk = $include_ext ? $this->rankingMgr->getRanking($semester, $game) : $this->rankingMgr->getRankingWithoutExt($semester, $game);
            $this->twig->display('ranking/index', array(
                'ranking' => $rk,
                'game' => $game,
                'gamelist' => $this->rankingMgr->getGameList(),
                'period' => $semester,
                'ext' => $include_ext,
            ));
        }
    }
};