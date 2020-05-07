<?php defined('BASEPATH') OR exit('No direct script access allowed.');


class Sendemail {

	protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

	public function enviarEmailRecuperarSenha($nome, $to_email, $hash){
		$nome_fantasia = $this->CI->config->item('nome_fantasia');
    	$link = base_url("user/forgot/$hash");
    	$html = 
		"<!DOCTYPE html>
		<html lang=\"pt-br\">
		  <head>
		  </head>
		  <body> 
		    <h3><b>Olá,  {$nome}.</b></h3>
		    <p>Você solicitou a recuperação de senha do <b>{$nome_fantasia}</b>?</p>
		    <p>
		    Se foi você quem solicitou a recuperação de senha por favor <a href='{$link}'>Clique Aqui</a> para registrar uma nova senha. <br>
		    Se não foi você quem solicitou a recuperação de senha por favor <a href='{$link}'>Clique Aqui</a> para desfazer este pedido. <br>
		    Obrigado!
		    </p>
		    <br/>
		    <p><smal>**Por favor, não responder para este e-mail</smal></p>
		  </body>
		</html>";

	    $this->CI->load->library('email');
	    $this->CI->email
	    	->from('communications@matilab.com.br', 'MatiLab')
	    	->to($to_email)
	    	->subject("MatiLab - Pedido de Recuperação de Senha.")
	    	->message($html)
	    	->send();
    }
}