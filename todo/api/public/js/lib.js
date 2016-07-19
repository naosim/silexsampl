var formatDate = function(format, date) {
  var year = date.getFullYear();
  var month = ("0" + (date.getMonth() + 1)).slice(-2);
  var dayofmonth = ("0" + date.getDate()).slice(-2);
  return format.split('YYYY').join(year).split('MM').join(month).split('DD').join(dayofmonth);
}
var day = (num) => new Date(Date.now() + 24 * 60 * 60 * 1000 * num);
var formatedDay = (num) => formatDate('MM/DD', day(num));
