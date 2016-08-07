<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 25/07/2016
 * Time: 17:16
 */
class User extends BaseController
{
    public function index()
    {
        $this->requireAuth();
        $this->load->model('Ranking_model', 'rankingMgr');
        $this->load->model('Registration_model', 'registrationMgr');
        $this->load->helper('tournament');


        $s=false;
        if($this->session->success)
        {
            $s = true;
            $this->session->success = false;
        }

        $this->twig->display('user/account', array(
            'ranking' => $this->rankingMgr->getUserRanking($this->session->user_id),
            'userdata' => $this->userMgr->getUserdata($this->session->user_id),
            'registration' => parse_reg($this->registrationMgr->getUserRegistrations($this->session->user_id)),
            'success' => $s,
        ));
    }
    public function edit()
    {
        $this->requireAuth();
        $this->load->library('form_validation');

        $this->load->model('Ranking_model', 'rankingMgr');
        $this->load->model('Registration_model', 'registrationMgr');
        $this->load->helper('tournament');

        $userdata =$this->userMgr->getUserdata($this->session->user_id);
        $err_upload="";
        if($userdata->ext)
        {
            $this->form_validation->set_rules('firstname', 'Nom', 'required');
            $this->form_validation->set_rules('surname', 'Prénom', 'required');
            $this->form_validation->set_rules('mail', 'Mail', 'required|valid_email');
        }
        $this->form_validation->set_rules('pseudo', 'Pseudo', 'required');

        if($this->form_validation->run())
        {


            // Process eventual avatar upload
            if($_FILES['file']['name'] != "")
            {
                $config['upload_path'] = './assets/img/avatar/temp';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = 2000;
                $config['max_width'] = 500;
                $config['max_height'] = 500;
                $config['overwrite'] = true;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('file')) {
                    $err_upload = array('error' => $this->upload->display_errors());
                } else {
                    $path = $this->upload->data("full_path");
                    $output = './assets/img/avatar/' . $this->session->user_id;
                    imagepng(imagecreatefromstring(file_get_contents($path)), $output . '.png');
                    unlink($path);
                }
            }

            $this->userMgr->update($this->session->user_id, $this->input->post('pseudo'), $this->input->post('gametag'), $this->input->post('phone'));
            $this->session->success = true;
            redirect('/user');
        }

        $this->twig->display('user/account_edit', array(
            'ranking' => $this->rankingMgr->getUserRanking($this->session->user_id),
            'userdata' => $userdata,
            'registration' => parse_reg($this->registrationMgr->getUserRegistrations($this->session->user_id)),
            'err' => validation_errors(),
            'err_file' => $err_upload,
        ));

    }

    public function edit_registration($id)
    {

        $this->requireAuth();
        $this->load->library('form_validation');

        $this->load->model('Ranking_model', 'rankingMgr');
        $this->load->model('Registration_model', 'registrationMgr');
        $this->load->model('Tournament_model', 'tournamentMgr');
        $this->load->helper('tournament');

        $registration = parse_reg($this->registrationMgr->getUserRegistrations($this->session->user_id));
        $registration = $registration[0]; // PHP 5.3 :|
        $tournament = $this->tournamentMgr->getTournament($registration['tournamentId']);
        $userdata = $this->userMgr->getUserdata($this->session->user_id);

        if($registration['regId'] == $id) {
            if(strtotime($registration['date'])<= time()) {
                redirect('/user');
            }
        }


        $this->form_validation->set_rules('events[]', 'Evenement', 'callback_checkevent');

        if($this->form_validation->run())
        {
            $this->registrationMgr->update($registration['regId'], $this->input->post('events'), $this->input->post('brings'));
            $this->session->success = true;
            redirect('/user');
        }

        $this->twig->display('user/account_edit_registration', array(
            'registration' => $registration,
            'tournament' => $tournament,
            'userdata' => $userdata,
            'err' => validation_errors(),
        ));

    }

    public function delete_registration($id)
    {
        $this->load->helper('tournament');
        $this->load->model('Registration_model', 'registrationMgr');
        $registration = parse_reg($this->registrationMgr->getUserRegistrations($this->session->user_id));
        $registration = $registration[0]; // PHP 5.3 :|

        if($registration['regId'] == $id) {
            if(strtotime($registration['date'])<= time()) {
                redirect('/user');
            }
        }

        $this->registrationMgr->delete($id);
        $this->session->success = true;
        redirect('/user');
    }

    public function login($fake = false)
    {
        $this->requireNoAuth();
        $ok = strpos($this->session->redirect_url, 'tournament/register') !== FALSE;
        if($fake && $ok)
        {
            $this->session->user_id=-1;
            $redirect_url = $this->session->redirect_url;
            unset($_SESSION['redirect_url']);
            if ($redirect_url == "")
                $redirect_url = "/";

            redirect($redirect_url);
        }

        $this->twig->display('user/login_select', array('fake'=>$ok));
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/');
    }

    public function login_ext()
    {
        $this->requireNoAuth();
        // TODO:
    }

    public function login_cas()
    {
        $this->load->library('cas');
        $this->cas->force_auth();
        $mail = $this->cas->user()->attributes['mail'];
        $id = $this->userMgr->getUserId($mail);
        if($id == null)
        {
            var_dump($this->cas->user());
            $id = $this->userMgr->createCasUser($mail, $this->cas->user()->attributes['sn'], $this->cas->user()->attributes['givenName'], $this->cas->user()->userlogin);
        }
        $this->session->user_id = $id;
        $redirect_url = $this->session->redirect_url;
        unset($_SESSION['redirect_url']);
        if ($redirect_url == "")
            $redirect_url = "/";

        redirect($redirect_url);
    }

    public function register()
    {
        $this->requireNoAuth();
        $this->output->enable_profiler(TRUE);
    }


    // FORMS CALLBACK
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