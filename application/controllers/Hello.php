<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hello extends BaseController {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    public function index()
    {
		$this->load->model('News_model', 'newsMgr');
        $this->twig->display('accueil', array(
			'news' => $this->newsMgr->getLastOne(),
		));
    }

	public function information()
	{
		$this->twig->display('info');
	}
	public function contact()
	{
		$this->load->library('form_validation');
		$s=false;
		$this->form_validation->set_rules('name', 'Nom', 'required');
		$this->form_validation->set_rules('mail', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('content', 'Contenu', 'required');

		if($this->form_validation->run())
		{
			$s = true;
			$this->load->library('email');
			$this->email->set_newline("\r\n");

			$this->email->from($this->input->post('mail'), $this->input->post('name'));
			$this->email->to('utsmash.asso@gmail.com');

			$this->email->subject('Message depuis le site');
			$this->email->message($this->input->post('content'));

			$this->email->send();
		}


		$this->twig->display('contact', array(
			'success' => $s,
			'err' => validation_errors(),
		));
	}
}
