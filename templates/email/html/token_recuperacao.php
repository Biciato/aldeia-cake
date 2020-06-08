    <p>
    	Olá, uma mudança de senha para seu usuário foi requisitada. Se foi você quem a fez, <a href="<?php echo $this->Url->build(
    	  [
    	  	'controller' => 'login',
    	  	'action'     => 'confirm-token',
    	  	$user->id,
    	  	$token->token
    	  ], true); ?>" target="_blank">clique aqui</a>. <br/><br/>
    	Se não foi você quem fez essa solicitação, ignore esse email.
    </p>