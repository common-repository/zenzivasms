function getObject(obj) {
  var theObj;
  if(document.all) {
    if(typeof obj=="string") {
      return document.all(obj);
    } else {
      return obj.style;
    }
  }
  if(document.getElementById) {
    if(typeof obj=="string") {
      return document.getElementById(obj);
    } else {
      return obj.style;
    }
  }
  return null;
}

function toCount(entrance,maxlength) { 
	var entranceObj=getObject(entrance);
  var msgcount=0;
  var character=0;
  var length=0 + entranceObj.value.length;  
  jmlsms=1;
    
  if(length > maxlength) {
    jmlsms=1+parseInt(entranceObj.value.length/maxlength);
  } 		
  character = length;
  document.getElementById('CharCounter').innerHTML = "<a href='#' style='text-decoration: none; color: #646464; font-size: 10px;'>" + character+" karakter, "+jmlsms+" SMS</a>";
}