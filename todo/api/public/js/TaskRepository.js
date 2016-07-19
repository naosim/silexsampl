var TaskRepository = function() {
  var urlbase = '..';

  var handleResponse = (data, success, error) => {
    if(!data) {
      error(null);
    }
    console.log(data);
    var response = JSON.parse(data.body);
    console.log(response);
    if(response.header.status == 'ok') {
      success(response.body);
    } else {
      error(response);
    }
  }

  var getTaskList = function(vue, token, success, error) {
    vue.$http.get(`${urlbase}/tasks?token=${token}`).then(
      (data, status, request) => handleResponse(data, success, error),
      (data, status, request) => handleResponse(data, success, error)
    );
  };

  var addTask = function(vue, token, name, dueDateOptional, success, error) {
    vue.$http.get(`${urlbase}/tasks/add?token=${token}&task_name=${name}&task_due_date_optional=${dueDateOptional}`).then(
      (data, status, request) => handleResponse(data, success, error),
      (data, status, request) => handleResponse(data, success, error)
    );
  };

  var complete = function(vue, token, taskId, success, error) {
    console.log(taskId);
    vue.$http.get(`${urlbase}/tasks/complete?token=${token}&task_id=${taskId}`).then(
      (data, status, request) => handleResponse(data, success, error),
      (data, status, request) => handleResponse(data, success, error)
    );
  }

  var deleteTask = function(vue, token, taskId, success, error) {
    console.log(taskId);
    vue.$http.get(`${urlbase}/tasks/delete?token=${token}&task_id=${taskId}`).then(
      (data, status, request) => handleResponse(data, success, error),
      (data, status, request) => handleResponse(data, success, error)
    );
  }

  return {
    getTaskList: getTaskList,
    addTask: addTask,
    complete: complete,
    deleteTask: deleteTask
  };
};
