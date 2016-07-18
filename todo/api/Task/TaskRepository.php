<?php

namespace Task;
use Rtm\Rtm;
use RtmConfig\RtmConfig;
use lib\JsonArray;
class TaskRepository {
  private $rtm;
  public function __construct($token) {
    $rtmConfig = new RtmConfig();
    $rtm = new Rtm;
    $rtm->setApiKey($rtmConfig->getApiKey());
    $rtm->setSecret($rtmConfig->getSecret());
    $rtm->setAuthToken($token);

    $this->rtm = $rtm;
  }

  public function getRecentTaskList(){
    $taskList = array();
    $rtmResult = $this->rtm->getService(Rtm::SERVICE_TASKS)->getList('(status:incomplete)or(completedAfter:25/06/2016)')->getIterator();
    foreach ($rtmResult as $i => $list) {
      $listId = $list -> get('id');
      foreach($list -> get('taskseries') as $j => $rtmTask) {
        $task = Task::convertRtmTaskToTask($listId, $rtmTask);
        array_push($taskList, $task);
      }
    }
    return new JsonArray($taskList);
  }



}
