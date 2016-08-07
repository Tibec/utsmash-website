<?php

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 28/07/2016
 * Time: 15:24
 */
class Ranking_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getRanking($period, $game)
    {
        return $this->db->select('rank, characters, pseudo, firstname, surname, gametag')
            ->order_by('rank', 'ASC')
            ->join('users', 'users.id = ranking.user')
            ->join('ranking_games', 'ranking.game = ranking_games.id')
            ->get_where('ranking', array(
                'period' => $period,
                'shortname' => $game,
            ))->result();
    }

    public function getRankingWithoutExt($period, $game)
    {
        $r = $this->db->select('userId')->get('user_ext')->result_array();
        $exts = array();
        foreach($r as $id)
            $exts[] = $id['userId'];

        $this->db->select('rank, characters, pseudo, firstname, surname, gametag')
            ->order_by('rank', 'ASC')
            ->where('period', $period)
            ->where('shortname', $game);
        if(!empty($exts))
            $this->db->where_not_in('user', $exts);
        return $this->db
            ->join('ranking_games', 'ranking.game = ranking_games.id')
            ->join('users', 'users.id = ranking.user')
            ->get('ranking')
            ->result();

    }

    public function getUserRanking($userId)
    {
        return $this->db->select('period, rank, characters, shortname')
            ->join('ranking_games', 'ranking.game = ranking_games.id')
            ->get_where('ranking', array(
            'user' => $userId
            ))->result();
    }

    public function getGameList()
    {
        return $this->db->get('ranking_games')->result();
    }

}