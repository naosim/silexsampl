<?php
require_once 'index_require.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Task\TaskRepository;

$app = new Silex\Application();
$app['debug'] = true;

$app->get('/', function() {
    return 'hello';
});

// $app->get('/tasks', function(Request $request) {
//   $taskRepository = new TaskRepository(getParam($request, 'token'));
//   return $taskRepository->getRecentTaskList()->toJson();
// });

function convertDateOptional($stringDate = null) {
  return $stringDate != null && strlen($stringDate) > 0 ? $stringDate : null;
}

$apis = [
  get(
    'タスク取得',
    '/tasks',
    ['token'],
    function($params, $request) {
      $taskRepository = new TaskRepository($params['token']);
      return createOkResult($taskRepository->getRecentTaskList()->toArray());
    }
  ),

  get(
    'タスク追加',
    '/tasks/add',
    ['token', 'task_name', 'task_due_date_optional'],
    function($params, $request) {
      $taskRepository = new TaskRepository($params['token']);
      return createOkResult($taskRepository->addTask($params['task_name'], convertDateOptional($params['task_due_date_optional']))->toArray());
    }
  ),

  get(
    'タスク完了',
    '/tasks/complete',
    ['token', 'task_id'],
    function($params, $request) {
      $taskRepository = new TaskRepository($params['token']);
      return createOkResult($taskRepository->complete($params['task_id'])->toArray());
    }
  ),

  get(
    'タスク削除',
    '/tasks/delete',
    ['token', 'task_id'],
    function($params, $request) {
      $taskRepository = new TaskRepository($params['token']);
      return createOkResult($taskRepository->delete($params['task_id'])->toArray());
    }
  ),

  get(
    'タスク更新',
    '/tasks/update',
    ['token', 'task_id', 'task_name', 'task_due_date_optional'],
    function($params, $request) {
      $taskRepository = new TaskRepository($params['token']);
      return createOkResult($taskRepository->update(
        $params['task_id'],
        $params['task_name'],
        convertDateOptional($params['task_due_date_optional'])
      )->toArray());
    }
  )
];

$app->get('/apis', function(Request $request) use($apis) {
  $html = '<h1>API一覧</h1>';
  foreach($apis as $apiIndex => $api) {
    $input = '';
    foreach($api['params'] as $i => $value) {
      $input .= sprintf('%s: <input type="text" name="%s" /></br>', $value, $value);
    }
    $format = '<h2>%s</h2><div>%s</div><form action=".%s" method="get">%s<input type="submit" /></form><hr/>';
    $html .= sprintf($format, $api['apiName'], $api['path'], $api['path'], $input);
  }
  return $html;
});

$app->get('/hello/{name}', function($name, Request $request) use($app) {
    $arr = [
      'name' => $app->escape($name),
      'k1' => 10,
      'k2' => 15,
      'k3' => 20,
    ];
    return json_encode($arr);
});

$app->get('/public/{path}', function ($path) use ($app) {
  $filePath = './public/' . $path;
    if (!file_exists($filePath)) {
        $app->abort(404);
    }

    return $app->sendFile($filePath);
});

$app->error(function (\Exception $e, $code) {
  if(strpos($e -> getMessage(), 'Invalid auth token') !== false) {
    return new Response("Invalid auth token", 400);
  }
  return new Response($e -> getMessage() . $code, 500);
});

$app->run();

function get($apiName, $path, $params, $actionWithParamsAndRequest) {
  global $app;
  $app->get($path, function(Request $request) use($params, $actionWithParamsAndRequest) {
    $p = array();
    foreach($params as $index => $value) {
      $p[$value] = $request->query->get($value);
    }
    return $actionWithParamsAndRequest($p, $request);
  });
  return [
    'apiName' => $apiName,
    'path' => $path,
    'params' => $params,
  ];
}

function getParam($request, $key) {
  return $request->query->get($key);
}

function createOkResult($obj) {
  return createResult(200, 'ok', $obj);
}
function createResult($code, $status, $obj) {
  return json_encode([
    'header' => [
      'code' => $code,
      'status' => $status,
    ],
    'body' => $obj
  ]);
}
