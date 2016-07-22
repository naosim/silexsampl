var TaskRepository = function() {
  var urlbase = '..';

  var isAuthedEstimated = function() {
    return monster.get('PHPSESSID') != null;
  };

  var clearAuth = function(vue) {
    monster.remove('PHPSESSID');
    model.isAuthed = false;
    // Vue.set(model, 'isAuthed', false);
    // vue.clearAuth();
  }

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
      clearAuth();
      error(response);
    }
  }

  var convertStringDateToDate = (v) => {
    if(v.task_due_date_optional) {
      v.task_due_date_optional = new Date(v.task_due_date_optional)
    }
    if(v.task_completed_date_optional) {
      v.task_completed_date_optional = new Date(v.task_completed_date_optional)
    }
    return v;
  };

  var getAuthUrl = function(vue, success, error) {
    vue.$http.get(`${urlbase}/auth/geturl`).then(
      success, error
    )
  };

  var setupToken = function(vue, success, error) {
    vue.$http.get(`${urlbase}/auth/settoken`).then(
      (data)=> {
        model.isAuthed = true;
        success(data);
      }, error
    )
  };

  var getTaskList = function(vue, token, success, error) {
    if(!isAuthedEstimated()) {
      clearAuth();
      return;
    }
    vue.$http.get(`${urlbase}/tasks`).then(
      (data, status, request) => handleResponse(data, (data) => success(data.map(convertStringDateToDate)), error),
      (data, status, request) => handleResponse(data, null, error)
    );
  };

  var addTask = function(vue, token, name, dueDateOptional, success, error) {
    vue.$http.get(`${urlbase}/tasks/add?task_name=${name}&task_due_date_optional=${dueDateOptional}`).then(
      (data, status, request) => handleResponse(data, (data) => success(convertStringDateToDate(data)), error),
      (data, status, request) => handleResponse(data, null, error)
    );
  };

  var updateTask = function(vue, token, taskId, name, dueDateOptional, success, error) {
    vue.$http.get(`${urlbase}/tasks/update?task_id=${taskId}&task_name=${name}&task_due_date_optional=${dueDateOptional}`).then(
      (data, status, request) => handleResponse(data, (data) => success(convertStringDateToDate(data)), error),
      (data, status, request) => handleResponse(data, null, error)
    );
  };

  var complete = function(vue, token, taskId, success, error) {
    console.log(taskId);
    vue.$http.get(`${urlbase}/tasks/complete?task_id=${taskId}`).then(
      (data, status, request) => handleResponse(data, (data) => success(convertStringDateToDate(data)), error),
      (data, status, request) => handleResponse(data, null, error)
    );
  }

  var deleteTask = function(vue, token, taskId, success, error) {
    console.log(taskId);
    vue.$http.get(`${urlbase}/tasks/delete?task_id=${taskId}`).then(
      (data, status, request) => handleResponse(data, (data) => success(convertStringDateToDate(data)), error),
      (data, status, request) => handleResponse(data, success, error)
    );
  };

  var sortedTaskList = function(taskList) {
    var getTargetDate = (task) => {
      if(!task.task_due_date_optional && !task.task_completed_date_optional) {
        return null;
      }

      if(task.task_due_date_optional && !task.task_completed_date_optional) {
        return task.task_due_date_optional;
      }

      if(!task.task_due_date_optional && task.task_completed_date_optional) {
        return task.task_completed_date_optional;
      }

      return task.task_due_date_optional.getTime() < task.task_completed_date_optional.getTime() ? task.task_due_date_optional : task.task_completed_date_optional;
    }
    var sortTask = (a, b) => {// 下ならプラスを返す
      var aTargetDate = getTargetDate(a);
      var bTargetDate = getTargetDate(b);
      if(aTargetDate && !bTargetDate) {
        return -1;
      }
      if(!aTargetDate && bTargetDate) {
        return 1;
      }
      if(!aTargetDate && !bTargetDate) {
        if(a.task_id < b.task_id) return -1;
        if(a.task_id == b.task_id) return 0;
        if(a.task_id > b.task_id) return 1;
      }
      return aTargetDate.getTime() - bTargetDate.getTime();
    };

    return taskList.filter((v) => {
      if(!v.task_completed_date_optional) {
        return true;
      }
      // 24時間以上過去は切る
      return Date.now() - 24 * 60 * 60 * 1000 - v.task_completed_date_optional.getTime() < 0;
    })
    .sort(sortTask);
  }

  return {
    getAuthUrl: getAuthUrl,
    setupToken: setupToken,
    getTaskList: getTaskList,
    addTask: addTask,
    complete: complete,
    deleteTask: deleteTask,
    updateTask: updateTask,
    sortedTaskList: sortedTaskList
  };
};
