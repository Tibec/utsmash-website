<?php

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 28/07/2016
 * Time: 15:24
 */
class News_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getLast($count, $offset = 0)
    {
        return $this->db->limit($count, $offset)
                        ->order_by('date', 'DESC')
                        ->get('news')
                        ->result();
    }

    public function getLastOne()
    {
        $l =  $this->getLast(1, 0);
        return $l[0];
    }

    public function count()
    {
        return $this->db->count_all_results('news');
    }

    public function getById($id)
    {
        return $this->db->where('id', $id)
                        ->get('news')
                        ->row();
    }

    public function add($titre, $contenu, $userId)
    {
        $this->db->insert('news', array(
            'title' => $titre,
            'content' => $contenu,
            'author' => $userId,
        ));
    }

    public function edit($id, $titre, $contenu)
    {
        $this->db->update('news', array(
                     'title' => $titre,
                     'content' => $contenu
                 ), array('id' => $id));
    }

    public function delete($id)
    {
        $this->db->delete('news', array('id'=>$id));
    }
}