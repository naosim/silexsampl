<?php
namespace Task;

use lib\ToJson;

class Task implements ToJson {
  private $id;
  private $name;
  private $dueDate;
  private $completedDate;
  private $deletedDate;

  public function __construct($id, $name, $dueDate = null, $completedDate = null, $deletedDate = null) {
    $this->id = $id;
    $this->name = $name;
    $this->dueDate = $dueDate;
    $this->completedDate = $completedDate;
    $this->deletedDate = $deletedDate;
  }
  public function toArray() {
    return [
      'task_id' => $this->id,
      'task_name' => $this->name,
      'task_due_date_optional' => $this->dueDate,
      'task_completed_date_optional' => $this->completedDate,
      'task_deleted_date_optional' => $this->deletedDate,
    ];
  }
  public function toJson() {
    return json_encode($this->toArray());
  }

  public static function createId($listId, $taskseriesId, $taskId) {
    return $listId . '_' . $taskseriesId . '_' . $taskId;
  }

  public static function createIdSet($id) {
    $ary = split('_', $id);
    return [
      'list_id' => $ary[0],
      'taskseries_id' => $ary[1],
      'task_id' => $ary[2],
    ];
  }

  public static function convertRtmTaskToTask($listId, $rtmTask) {
    return new Task(
      Task::createId($listId, $rtmTask->get('id'), $rtmTask->get('id')),
      // $listId . '_' . $task->get('id') . '_' . $task->get('id'),
      $rtmTask->get('name'),
      $rtmTask->get('task')->get('due'),
      $rtmTask->get('task')->get('completed'),
      $rtmTask->get('task')->get('deleted')
    );
  }
}
