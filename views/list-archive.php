<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.messages.php';

$messages = new Messages($_SESSION['role'] === 'admin' ? [] : ['uid'=>$_SESSION['id']]);
$messagesCount = $messages->getArchivedMessagesCount();

if ($messagesCount === 0){
  $output['{MAIN_CONTENT}'] = '<h1>Nothing to show!</h1>';
}
else {
  require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.notes.php';
  
  $notesAction = new Notes();

  $page = 0;
  if (isset($_GET['page']))
    $page = (int) $_GET['page'];
  $allMessages = $messages->getArchivedMessages($page);
  $output['{MAIN_CONTENT}'] = '';
  for($x=0;$x<count($allMessages);$x++){
      
    $notesCount = $notesAction->getNotesCount($allMessages[$x]['id']);
    $notesButton = '';
    if ($notesCount)
        $notesButton = '
          <h3 class="messages__notes-toogle" data-action="notes-block-toggle">< Show notes ></h3>
        ';

    $time = date('d.m.Y H:i:s', $allMessages[$x]['time']);
    $output['{MAIN_CONTENT}'] .= "
      <div class='messages__item' data-message-id='{$allMessages[$x]['id']}'>
        <div class='messages__author'>
          <div class='messages__time'>
            $time
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
          <h4>Send from < {$allMessages[$x]['source']} ></h4>
          {$allMessages[$x]['message']}
          <div class='messages__notes'>
          </div>
          $notesButton
        </div>
        
        <div class='messages__dashboard'>
          <button type='button' data-action='message-edit-form'><i class='fa fa-pencil-square-o'></i></button>
          <button type='button' data-action='message-delete'><i class='fa fa-trash'></i></button>
        </div>
      </div>
    ";
  };
  
  if ($messagesCount > CFG_MESSAGE_PER_PAGE){
    $pagesCount = ceil($messagesCount/CFG_MESSAGE_PER_PAGE);
    for($x=0;$x<$pagesCount;$x++){
      $pagesLinks .= '<a href="/?page='.$x.'"'.($x==$page ? ' class="active"' : '').'>'.($x+1).'</a>';
    }
    if ($page > 0){
      $pagesLinks = '<a href="/?page='.($page-1).'"><i class="fa fa-angle-left"></i></a>'.$pagesLinks;
    }
    else{
      $pagesLinks = "<a><i class='fa fa-angle-left'></i></a>$pagesLinks";
    }
    if ($page > 5){
      $pagesLinks = '<a href="/?page=0"><i class="fa fa-angle-double-left"></i></a>'.$pagesLinks;
    }


    if ($page != ($pagesCount-1)){
      $pagesLinks .= '<a href="/?page='.($page+1).'"><i class="fa fa-angle-right"></i></a>';
    }
    else{
      $pagesLinks .= '<a><i class="fa fa-angle-right"></i></a>';
    }
    if ($pagesCount-1 - $page > 5){
      $pagesLinks .= '<a href="/?page='.($pagesCount-1).'"><i class="fa fa-angle-double-right"></i></a>';
    }


    $output['{MAIN_CONTENT}'] .= "<div class='messages__links'>$pagesLinks</div>";
  }
};

$output['{MAIN_CONTENT}'] = '<main class="main messages main-section">
  <div class="messages__common-dashboard">
    <form action="/">
      <input type="hidden" name="view" value="archive">
      <input type="text" name="search" placeholder="search...">
      <button><i class="fa fa-search" aria-hidden="true"></i></button>
    </form>
  </div>
  '.$output['{MAIN_CONTENT}'].'
  </main>';