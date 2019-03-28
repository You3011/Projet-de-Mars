<?php


 /*Connexion simple à la base de données via PDO !*/
 
$db = new PDO('mysql:host=localhost;dbname=chat;charset=utf8', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

/**
 analyser la demande faite via l'URL (GET)
 */
$task = "list";

if(array_key_exists("task", $_GET)){
  $task = $_GET['task'];
}

if($task == "write"){
  postMessage();
} else {
  getMessages();
}


 /*récupérer avec JSON*/
 
function getMessages(){
  global $db;

  //sortir les 20 derniers messages
  $resultats = $db->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 20");
  //résultats
  $messages = $resultats->fetchAll();
  //données sous forme de JSON
  echo json_encode($messages);
}

function postMessage(){
  global $db;
  //Analyse les paramètres passés en POST (author, content)
  
  if(!array_key_exists('author', $_POST) || !array_key_exists('content', $_POST)){

    echo json_encode(["status" => "error", "message" => "One field or many have not been sent"]);
    return;

  }

  $author = $_POST['author'];
  $content = $_POST['content'];

  //requête qui permettra d'insérer ces données
  $query = $db->prepare('INSERT INTO messages SET author = :author, content = :content, created_at = NOW()');

  $query->execute([
    "author" => $author,
    "content" => $content
  ]);

  //succes ou erreur au format JSON
  echo json_encode(["status" => "success"]);
}
