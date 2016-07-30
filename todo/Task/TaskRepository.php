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

  private function getTaskService() {
    return $this->rtm->getService(Rtm::SERVICE_TASKS);
  }
  private function createTimeline() {
    return $this->rtm->getService(Rtm::SERVICE_TIMELINES)->create();
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

  public function addTask($name, $dueDateOptional = null) {
    $timeline = $this->createTimeline();
    $taskService = $this->rtm->getService(Rtm::SERVICE_TASKS);
    $addResult = $taskService->add($name, null, null, $timeline);
    if($addResult->get('stat') != 'ok') {
      return;// TODO error
    }

    $listId = $addResult->get('list')->get('id');
    $taskSeriesId = $addResult->get('list')->get('taskseries')->get('id');
    $taskId = $addResult->get('list')->get('taskseries')->get('task')->get('id');

    if($dueDateOptional == null) {
      return Task::convertRtmTaskToTask($listId, $addResult->get('list')->get('taskseries'));
    }
    //$taskId, $listId, $taskSeriesId, $due = null, $hasDueTime = null, $parse = null, $timeline = null
    $dueResult = $taskService->setDueDate($taskId, $listId, $taskSeriesId, $dueDateOptional, null, null, $timeline);
    return $this->handleOneTaskListResult($dueResult);
  }

  public function update($taskId, $name, $dueDateOptional = null) {
    if($this->updateName($taskId, $name) == null) {
      return null;
    }
    return $this->updateDueDate($taskId, $dueDateOptional);
  }

  public function updateName($taskId, $name) {
    $taskService = $this->rtm->getService(Rtm::SERVICE_TASKS);
    $taskIdSet = Task::createIdSet($taskId);
    $result = $taskService->setName(
      $taskIdSet['task_id'],
      $taskIdSet['list_id'],
      $taskIdSet['taskseries_id'],
      $name,
      $this->createTimeline()
    );
    return $this->handleOneTaskListResult($result);
  }

  public function updateDueDate($taskId, $dueDateOptional = null) {
    $taskService = $this->rtm->getService(Rtm::SERVICE_TASKS);
    $taskIdSet = Task::createIdSet($taskId);
    $result = $taskService->setDueDate(
      $taskIdSet['task_id'],
      $taskIdSet['list_id'],
      $taskIdSet['taskseries_id'],
      $dueDateOptional,
      null, //hasduetime
      null, //parse
      $this->createTimeline()
    );
    return $this->handleOneTaskListResult($result);
  }

  public function complete($taskId) {
    $taskIdSet = Task::createIdSet($taskId);

    $result = $this->getTaskService()->complete(
      $taskIdSet['task_id'],
      $taskIdSet['list_id'],
      $taskIdSet['taskseries_id'],
      $this->createTimeline()
    );
    return $this->handleOneTaskListResult($result);
  }

  public function delete($taskId) {
    $taskIdSet = Task::createIdSet($taskId);
    // public function complete($taskId, $listId, $taskSeriesId, $timeline = null)

    $result = $this->getTaskService()->delete(
      $taskIdSet['task_id'],
      $taskIdSet['list_id'],
      $taskIdSet['taskseries_id'],
      $this->createTimeline()
    );
    return $this->handleOneTaskListResult($result);
  }

  private function handleOneTaskListResult($result) {
    if($result->get('stat') != 'ok') {
      return null;// TODO error
    }
    return Task::convertRtmTaskToTask(
      $result->get('list')->get('id'),
      $result->get('list')->get('taskseries'),
      $this->createTimeline()
    );
  }

}
