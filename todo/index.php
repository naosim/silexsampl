<?php
require_once 'index_require.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Provider\SessionServiceProvider;
use Task\AuthRepository;
use Task\TaskRepository;

$app = new Silex\Application();

$sessionConfig = [
  'cookie_lifetime' => 30 * 24 * 60 * 60,//one month
];
$app->register(new SessionServiceProvider(new NativeSessionStorage($sessionConfig)));
$app['debug'] = true;

$app->get('/api', function() {
    return 'hello';
});

function convertDateOptional($stringDate = null) {
  return $stringDate != null && strlen($stringDate) > 0 ? $stringDate : null;
}

function createAuthRepository() {
  global $app;
  return new AuthRepository($app);
}

function createTaskRepository() {
  $token = createAuthRepository()->getToken();
  return new TaskRepository($token);
}

$apis = [
  get(
    'タスク取得',
    '/api/tasks',
    [],
    function($params, $request) {
      $taskRepository = createTaskRepository();
      return createOkResult($taskRepository->getRecentTaskList()->toArray());
    }
  ),

  post(
    'タスク追加',
    '/api/tasks/add',
    ['task_name', 'task_due_date_optional'],
    function($params, $request) {
      $taskRepository = createTaskRepository();
      return createOkResult($taskRepository->addTask($params['task_name'], convertDateOptional($params['task_due_date_optional']))->toArray());
    }
  ),

  put(
    'タスク完了',
    '/api/tasks/{task_id}/complete',
    ['task_id'],
    function($params, $request) {
      $taskRepository = createTaskRepository();
      return createOkResult($taskRepository->complete($params['task_id'])->toArray());
    }
  ),

  deleteRequest(
    'タスク削除',
    '/api/tasks/{task_id}/delete',
    ['task_id'],
    function($params, $request) {
      $taskRepository = createTaskRepository();
      return createOkResult($taskRepository->delete($params['task_id'])->toArray());
    }
  ),


  put(
    'タスク更新',
    '/api/tasks/{task_id}/update',
    ['task_id', 'task_name', 'task_due_date_optional'],
    function($params, $request) {
      $taskRepository = createTaskRepository();
      return createOkResult($taskRepository->update(
        $params['task_id'],
        $params['task_name'],
        convertDateOptional($params['task_due_date_optional'])
      )->toArray());
    }
  ),

  get(
    '認証URL取得',
    '/api/auth/geturl',
    [],
    function($params, $request) use($app) {
      $url = createAuthRepository()->createAuthUrl();
      // return createOkResult(['url' => $url]);
      return $url;
    }
  ),

  get(
    'トークン取得',
    '/api/auth/settoken',
    [],
    function($params, $request) use($app) {
      $authRepository = createAuthRepository();
      $result = $authRepository->setupToken();
      return createOkResult($result);
    }
  ),

  get(
    'ユーザ情報取得(要auth)',
    '/api/user',
    [],
    function($params, $request) use($app) {
      return createOkResult($app['session']->get('user'));
    }
  ),

  get(
    'セッションテスト',
    '/api/session',
    [],
    function() {
      global $app;
      $session = $app['session'];
      var_dump($session);
      $count = $app['session']->get('count');
      if($count == null) {
        $count = 0;
      }
      $count++;
      $app['session']->set('count', $count);
      return $count;
    }
  ),

  get(
    'test',
    '/apis/{id}',
    ['id'],
    function($params, $request) {
      return json_encode($params);
    }
),
];

$app->get('/apis', function(Request $request) use($apis) {
  return json_encode($apis);
});

$app->error(function (\Exception $e, $code) {
  if(strpos($e -> getMessage(), 'Invalid auth token') !== false) {
    return new Response(createNgResult(400, 'Invalid auth token'), 400);
  }
  return new Response(createNgResult(400, $e->getMessage()), 400);
});

$app->run();

function createOkResult($obj) {
  return createResult(200, 'ok', $obj);
}

function createNgResult($httpStatusCode, $errorMessage, $obj = array()) {
  return createResult($httpStatusCode, 'ng', $obj, $errorMessage);
}

function createResult($code, $status, $obj = array(), $errorMessage = null) {
  return json_encode([
    'header' => [
      'code' => $code,
      'status' => $status,
      'error_message' => $errorMessage,
    ],
    'body' => $obj
  ]);
}
