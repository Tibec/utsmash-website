<?php

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 28/07/2016
 * Time: 15:32
 */
class Registration_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function create($user, $events, $brings)
    {
        $this->db->insert('registrations', array(
            'user' => $user
        ));
        $regId= $this->db->insert_id();

        $b = $e = array();
        foreach($events as $eid)
        {
            $e[] = array(
                'regId' => $regId,
                'eventId' => $eid
            );
        }
        if(!empty($brings)) {

            foreach ($brings as $eid) {
                $b[] = array(
                    'regId' => $regId,
                    'bringId' => $eid
                );
            }
        }

        if($b != array())
            $this->db->insert_batch('registration_bring', $b);
        $this->db->insert_batch('registration_event', $e);

    }

    public function userRegistered($uid, $tid)
    {
        $r = $this->db->where('user', $uid)
            ->where('tournament', $tid)
            ->join('registration_event', 'registration_event.regId = registrations.id')
            ->join('event', 'registration_event.eventId = event.id')
            ->get('registrations')
            ->row_array();

        if($r == array())
            return false;
        else
            return true;
    }

    public function getUserRegistrations($uid)
    {
        $r = $this->db
            ->select('*, tournaments.name AS tname ,event.name AS ename, tournament_bringable.name as bname, registrations.id as regId, tournaments.id as tournamentId')
            ->where('user', $uid)
            ->join('registration_event', 'registration_event.regId = registrations.id')
            ->join('event', 'registration_event.eventId = event.id')
            ->join('tournaments', 'tournaments.id = event.tournament')
            ->join('registration_bring', 'registration_bring.regId = registrations.id', 'left')
            ->join('tournament_bringable', 'registration_bring.bringId = tournament_bringable.id', 'left')
            ->order_by('tournaments.date', 'DESC')
            ->get('registrations')
            ->result();
        return $r;
    }

    public function update($id, $events, $brings)
    {
        $b=$e=array();

        foreach($events as $eid)
        {
            $e[] = array(
                'regId' => $id,
                'eventId' => $eid
            );
        }

        if(!empty($brings)) {
            foreach($brings as $eid)
            {
                $b[] = array(
                    'regId' => $id,
                    'bringId' => $eid
                );
            }
        }

        $this->db
            ->where('regId', $id)
            ->delete('registration_event');
        $this->db
            ->where('regId', $id)
            ->delete('registration_bring');
        if($b != array())
            $this->db->insert_batch('registration_bring', $b);
        $this->db->insert_batch('registration_event', $e);
    }

    public function delete($id)
    {
        $this->db
            ->where('regId', $id)
            ->delete('registration_event');
        $this->db
            ->where('regId', $id)
            ->delete('registration_bring');
        $this->db
            ->where('regId', $id)
            ->delete('registration_event');
        $this->db
            ->where('id', $id)
            ->delete('registrations');
    }
}