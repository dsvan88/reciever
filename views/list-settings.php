<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$array=['email'=>'-','email-password'=>'-','tg_bot_token'=>'-'];
foreach($array as $key=>$value){
    if (isset($_POST[$key]))
        $array[$key] = urldecode(htmlspecialchars(trim($_POST[$key])));
}
if ($array['name'] === '-' && $array['email'] === '-' && $array['contact'] === '-' && $array['message'] === '-'){
    die('{"error":"1","title":"Error!","text":"Error: Nothing to send."}');
}

if ($messagesCount === 0){
  $output['{MAIN_CONTENT}'] = '<h1>Nothing to show!</h1>';
}
else {
  $output['{MAIN_CONTENT}'] = '
	<form class="settings-form" method="POST" action="/?view=list-settings">
		<picture>
			<source srcset="../images/logo.webp" type="image/webp" />
			<img title="My logo" alt="My logo" src="./images/logo.png" />
		</picture>
		<input type="email" name="email" placeholder="E-mail" />
		<input type="password" name="email-password" placeholder="E-mail password" />
		<input type="text" name="tg_bot_token" placeholder="Telegram BotToken" />
		<button type="button" data-action="re-set-main-tech-data">Re-set main tech data</button>
	</form>
';
};

$output['{MAIN_CONTENT}'] = '<main class="main settings main-section">'.$output['{MAIN_CONTENT}'].'</main>';