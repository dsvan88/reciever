<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/messages.class.php';

$messages = new Messages();
$allMessages = $messages->getMessages();

$output['{HEADER_TITLE}'] = 'Simple personal mail system!';
$output['{MAIN_CONTENT}'] = '';

$messagesCount = count($allMessages);

if ($messagesCount === 0){
  $output['{MAIN_CONTENT}'] = '<h1>Nothing to show!</h1>';
}
else{
  for($x=0;$x<$messagesCount;$x++){
    $output['{MAIN_CONTENT}'] .= "
      <div class='messages__item'>
        <div class='messages__author'>
          <div class='messages__author-name'>
            <label>Name:</label>{$allMessages[$x]['name']}
          </div>
          <div class='messages__author-contact'>
            <label>Contact:</label>{$allMessages[$x]['contact']}
          </div>
          <div class='messages__author-email'>
            <label>E-mail:</label>{$allMessages[$x]['email']}
          </div>
        </div>
        <div class='messages__text'>
          {$allMessages[$x]['message']}
        </div>
      </div>
    ";
  };
};
$output['{MAIN_CONTENT}'] = '<main class="main messages">'.$output['{MAIN_CONTENT}'].'</main>';