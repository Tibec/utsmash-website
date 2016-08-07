<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 25/07/2016
 * Time: 17:16
 */
class Tournament extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tournament_model', 'tournamentMgr');
        $this->load->model('Registration_model', 'registrationMgr');
        $this->load->helper('tournament');
    }


    public function index()
    {
        $this->twig->display('tournament/index', array(
            'upcoming' => $this->tournamentMgr->getUpcomingTournament(),
            'past' => $this->tournamentMgr->getPastTournament(),
        ));
    }
    public function register($id)
    {
        $this->requireFakeAuth();

        $this->load->library('form_validation');
        $this->load->helper('password');
        $user = $this->userMgr->getUserdata($this->session->user_id);
        $tournament = $this->tournamentMgr->getTournament($id);

        // Si déja inscrit = erreur
        if($this->registrationMgr->userRegistered($this->session->user_id, $id))
        {
            $this->twig->display('tournament/register_already', array(
                'tournament' => $tournament,
            ));
            return;
        }

        if($this->session->user_id < 0 || $user->ext)
        {
            $this->form_validation->set_rules('firstname', 'Nom', 'required');
            $this->form_validation->set_rules('surname', 'Prénom', 'required');
            $this->form_validation->set_rules('mail', 'Mail', 'required|valid_email|is_unique[users.mail]');
        }
        $this->form_validation->set_rules('events[]', 'Evenement', 'callback_checkevent');
        $this->form_validation->set_rules('pseudo', 'Pseudo', 'required');
        $this->form_validation->set_rules('phone', 'Tel', 'callback_checknum');

        if($this->form_validation->run())
        {
            $events = $this->input->post('events[]');
            $brings = $this->input->post('brings[]');

            if( tournament_contain_bring($tournament, $brings) and
            tournament_contain_event($tournament, $events))
            {
                $pass="";
                $mail ='';
                if($this->session->user_id < 0)
                {
                    $pass = generate_password();
                    $this->session->user_id = $this->userMgr->createExtUser(
                        $pass,
                        $this->input->post('firstname'),
                        $this->input->post('surname'),
                        $this->input->post('pseudo'),
                        $this->input->post('mail'),
                        $this->input->post('phone'));
                }
                $this->registrationMgr->create($this->session->user_id, $events, $brings);
                $eventsname = "";
                foreach($events as $e)
                {
                    foreach($tournament['events'] as $ed)
                    {
                        if($ed['id'] == $e)
                        {
                            $eventsname .= $ed['name'].' ';
                        }
                    }
                }

                // Envoyer le mail
                $this->load->library('email');
                $this->email->set_newline("\r\n");

                $this->email->from("utsmash.asso@gmail.com", "UT'Smash");
                $this->email->to($this->input->post('mail') =="" ? $user->mail : $this->input->post('mail'));
                $this->email->subject('Confirmation d\'inscription');
                $this->email->message(
                    generate_mail($this->input->post('pseudo'),
                        $this->input->post('mail'),
                        $tournament['name'],
                        date('d/m/Y', strtotime($tournament['date'])),
                        date('H\hi', strtotime($tournament['date'])),
                        $eventsname,
                        $pass));

                if(!$this->email->send())
                {echo 'sukks';}

                $this->twig->display('tournament/register_success', array(
                    'tournament' => $tournament,
                ));
            }
            else
            {
                // err
            }

        }
        else
        {
            $this->twig->display('tournament/register', array(
                'tournament' => $tournament,
                'userdata' => $user,
                'error' => validation_errors(),
            ));
        }
    }

    public function show($id)
    {
        $this->twig->display('tournament/show', array(
            'tournament' => $this->tournamentMgr->getTournament($id),
        ));

    }

    // FORMS CALLBACK

    public function checknum($num)
    {

        $bring = count($this->input->post('brings')) > 0;


        if($bring and strlen($num)< 10 )
        {
            $this->form_validation->set_message('checknum', 'Si vous amenez du matériel vous devez indiquer
            un numero valide');
            return false;
        }

        return true;
    }

    public function checkevent($ev)
    {
        if(count($ev) == 0)
        {
            $this->form_validation->set_message('checkevent', 'Vous devez vous inscrire à au moins un évenement');
            return false;
        }
        else
            return true;
    }

};