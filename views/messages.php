<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.messages.php';

$output['{HEADER_TITLE}'] = 'Simple personal mail system!';

$messages = new Messages();
$messagesCount = $messages->getMessagesCount();

if ($messagesCount === 0){
  $output['{MAIN_CONTENT}'] = '<h1>Nothing to show!</h1>';
}
else {
  $page = 0;
  if (isset($_GET['page']))
    $page = (int) $_GET['page'];
  $allMessages = $messages->getMessages($page);
  $output['{MAIN_CONTENT}'] = '';
  for($x=0;$x<count($allMessages);$x++){
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
  if ($messagesCount > CFG_MESSAGE_PER_PAGE){
    $pagesCount = ceil($messagesCount/CFG_MESSAGE_PER_PAGE);
    for($x=0;$x<$pagesCount;$x++){
      $pagesLinks .= '<a href="/?page='.$x.'">'.($x+1).'</a>';
    }
    $output['{MAIN_CONTENT}'] .= "<div class='messages__links'>$pagesLinks</div>";
  }
};

$output['{MAIN_CONTENT}'] = '<main class="main messages">'.$output['{MAIN_CONTENT}'].'</main>';