<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

function get($apiName, $path, $params, $actionWithParamsAndRequest) {
  global $app;
  $app->get($path, function(Request $request) use($params, $actionWithParamsAndRequest) {
    $p = array();
    foreach($params as $index => $value) {
      $v = null;
      $v = $request->attributes->get($value);
      if($v == null) {
        $v = $request->query->get($value);
      }

      $p[$value] = $v;
    }
    return $actionWithParamsAndRequest($p, $request);
  });
  return [
    'apiName' => $apiName,
    'path' => $path,
    'params' => $params,
    'method' => 'get',
  ];
}

function post($apiName, $path, $params, $actionWithParamsAndRequest) {
  global $app;
  $app->post($path, function(Request $request) use($params, $actionWithParamsAndRequest) {
    $p = array();
    foreach($params as $index => $value) {
      $v = null;
      $v = $request->attributes->get($value);
      if($v == null) {
        $v = $request->get($value);
      }
      $p[$value] = $v;

    }
    return $actionWithParamsAndRequest($p, $request);
  });
  return [
    'apiName' => $apiName,
    'path' => $path,
    'params' => $params,
    'method' => 'post',
  ];
}

function put($apiName, $path, $params, $actionWithParamsAndRequest) {
  global $app;
  $app->put($path, function(Request $request) use($params, $actionWithParamsAndRequest) {
    $p = array();
    foreach($params as $index => $value) {
      $v = null;
      $v = $request->attributes->get($value);
      if($v == null) {
        $v = $request->get($value);
      }
      $p[$value] = $v;

    }
    return $actionWithParamsAndRequest($p, $request);
  });
  return [
    'apiName' => $apiName,
    'path' => $path,
    'params' => $params,
    'method' => 'put',
  ];
}

function deleteRequest($apiName, $path, $params, $actionWithParamsAndRequest) {
  global $app;
  $app->delete($path, function(Request $request) use($params, $actionWithParamsAndRequest) {
    $p = array();
    foreach($params as $index => $value) {
      $v = null;
      $v = $request->attributes->get($value);
      if($v == null) {
        $v = $request->get($value);
      }
      $p[$value] = $v;

    }
    return $actionWithParamsAndRequest($p, $request);
  });
  return [
    'apiName' => $apiName,
    'path' => $path,
    'params' => $params,
    'method' => 'delete',
  ];
}

function getParam($request, $key) {
  return $request->query->get($key);
}
