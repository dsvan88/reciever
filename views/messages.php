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
    $output['{MAIN_CONTENT}'] .= '
      <div class="messages__item">
        <div class="messages__author">
          <div class="messages__time">
            '.date('d.m.Y H:i:s', $allMessages[$x]['time'])."
          </div>
          <div class='messages__author-name'>
            {$allMessages[$x]['name']}
          </div>
          <div class='messages__author-contact'>
            <a href='tel:{$allMessages[$x]['contact']}'>{$allMessages[$x]['contact']}</a>
          </div>
          <div class='messages__author-email'>
            <a href='mailto:{$allMessages[$x]['email']}'>{$allMessages[$x]['email']}</a>
          </div>
        </div>
        <div class='messages__text'>
          {$allMessages[$x]['message']}
        </div>
        <div class='messages__dashboard'>
          <button><i class='fa fa-eye'></i></button>
          <button><i class='fa fa-trash'></i></button>
          <button><i class='fa fa-file-archive-o'></i></button>
          <button><i class='fa fa-pencil-square-o'></i></button>
        </div>
      </div>
    ";
  };
};
$output['{MAIN_CONTENT}'] = '<main class="main messages">'.$output['{MAIN_CONTENT}'].'</main>';