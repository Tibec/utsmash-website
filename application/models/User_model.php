<?php

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 28/07/2016
 * Time: 15:24
 */
class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // retourne toutes les infos de l'utilisateur
    public function getUserdata($userId)
    {
        return $this->db
            ->where('users.id', $userId)
            ->join(
                '(SELECT userId, password, 1 as ext FROM user_ext) ext'
            , 'users.id = ext.userId', 'left')
            ->get('users')
            ->row();
    }

    // Retourne pseudo + avatar
    public function getMenuData($userId)
    {
        return $this->db->select('id, pseudo, avatar, mail')
                        ->where('id', $userId)
                        ->get('users')
                        ->row();
    }

    public function checkPassword($mail, $password)
    {
        return $this->db->select('mail, password')
                        ->where('mail', $mail)
                        ->where('SHA1(CONCAT('.$password.', salt))')
                        ->join('user_ext', 'user_ext.userId = users.id')
                        ->get('users')
                        ->num_rows() == 1;
    }

    public function createCasUser($mail, $fname, $sname, $pseudo)
    {
        $this->db->insert('users',array(
            'firstname' => $fname,
            'surname' => $sname,
            'pseudo' => $pseudo,
            'mail' => $mail,
        ));
        return $this->db->insert_id();
    }

    public function createExtUser($password, $fname, $sname, $pseudo, $mail, $tel)
    {
        $id = $this->createCasUser($mail, $fname, $sname, $pseudo, $tel);

        $salt = sha1(time());
        $this->db->insert('user_ext',array(
            'salt' => $salt,
            'password' => sha1($password.$salt),
            'userId' => $id,
        ));

        return $id;
    }

    public function getUserId($mail)
    {
        return $this->db->select('id')
                        ->where('mail', $mail)
                        ->get('users')
                        ->row()
                        ->id;

    }

    public function update($uid, $pseudo, $tag, $phone)
    {
        $this->db->where('id', $uid)
                ->update('users', array(
                   'pseudo' => $pseudo,
                    'gametag' => $tag,
                    'phone' => $phone
                ));
    }

    public function updateExt($uid, $fname, $sname, $mail, $password)
    {

    }
}