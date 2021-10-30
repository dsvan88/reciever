<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.messages.php';

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
          <div class='messages__time'>
            ".date('d.m.Y H:i:s', $allMessages[$x]['time'])."
          </div>
          <div class='messages__author-name'>
            {$allMessages[$x]['name']}
          </div>
          <div class='messages__author-contact'>
            {$allMessages[$x]['contact']}
          </div>
          <div class='messages__author-email'>
            {$allMessages[$x]['email']}
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