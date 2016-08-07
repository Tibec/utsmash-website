<?php

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 28/07/2016
 * Time: 15:24
 */
class Tournament_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getUpcomingTournamentName()
    {
        return $this->db
            ->select('id, name')
            ->where('date >', date('Y-m-d H:i:s', time()))
            ->get('tournaments')
            ->result();
    }

    public function getTournament($id)
    {
        $r = $this->db
            ->select('id, name, date, venueFee, picture, location, ruleset, date < now() as finished')
            ->where('id ', $id)
            ->get('tournaments')
            ->row_array();

        $r['events'] = $this->db
            ->select('id, name, cap, entryFee, count(regId) as inscrit')
            ->where('tournament', $id)
            ->join('registration_event', 'event.id = registration_event.eventId', 'left')
            ->group_by('id')
            ->get('event')
            ->result_array();

        $r['bringable'] = $this->db
            ->where('tournament', $id)
            ->get('tournament_bringable')
            ->result_array();

        return $r;
    }

    public function getUpcomingTournament()
    {
        $ret =  $this->db
            ->select('tournaments.id, date, picture, event.id as eventId, tournaments.name as tname, event.name as ename')
            ->where('date >', date('Y-m-d H:i:s', time()))
            ->join('event', 'event.tournament = tournaments.id')
            ->get('tournaments')
            ->result();

        $ret_t = array();
        foreach ($ret as $r)
        {
            if(count($ret_t) > 0 && $r->id == $ret_t[count($ret_t)-1]['id'])
            { // Add event
                $ret_t[count($ret_t)-1]['events'][] = array (
                    'name' => $r->ename,
                    'vod' => $r->vod,
                    'picture' => $r->eventPicture,
                    'bracket' => $r->bracket
                );
            }
            else { // New tournament
                $ret_t[] = array(
                    'id' => $r->id,
                    'name' => $r->tname,
                    'date' => $r->date,
                    'picture' => $r->picture,
                    'events' => array(array(
                        'name' => $r->ename,
                    ))
                );
            }
        }

        return $ret_t;

    }
    public function getPlayersForEvent($evId)
    {
        return $this->db
            ->join('registration_event', 'event.id = registration_event.eventId')
            ->join('registrations', 'registrations.id = registration_event.regId')
            ->join('users', 'registrations.user = users.id')
            ->get('event')
            ->result();
    }

    public function getPastTournament()
    {
        $ret =  $this->db
            ->select('tournaments.id, date, picture, eventId, pictures as eventPicture, tournaments.name as tname, event.name as ename, vod , bracket')
            ->where('date <',date('Y-m-d H:i:s', time()))
            ->join('event', 'event.tournament = tournaments.id')
            ->join('event_old', 'event.id = event_old.eventId')
            ->order_by('date', 'DESC')
            ->get('tournaments')
            ->result();

        $ret_t = array();
        foreach ($ret as $r)
        {
            if(count($ret_t) > 0 && $r->id == $ret_t[count($ret_t)-1]['id'])
            { // Add event
                $ret_t[count($ret_t)-1]['events'][] = array (
                    'name' => $r->ename,
                    'vod' => $r->vod,
                    'picture' => $r->eventPicture,
                    'bracket' => $r->bracket
                );
            }
            else { // New tournament
                $ret_t[] = array(
                    'id' => $r->id,
                    'name' => $r->tname,
                    'date' => $r->date,
                    'picture' => $r->picture,
                    'events' => array(array(
                        'name' => $r->ename,
                        'vod' => $r->vod,
                        'picture' => $r->eventPicture,
                        'bracket' => $r->bracket
                    ))
                );
            }
        }


        return $ret_t;
    }
}