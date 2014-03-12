<!DOCTYPE>
<?php
    $tokenTall=$_GET['token'];
    $token=$_GET['tokenTall'];
		?>	

<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="format-detection" content="telephone=no" />
<link rel="stylesheet" href="css/shake.css" type="text/css">
<meta http-equiv="x-rim-auto-match" content="none"/>
<title>摇一摇</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
</head>
<body  onload="loadFirst()">
<div class="">
 <script>
// DeviceOrientation将底层的方向传感器和运动传感器进行了高级封装，提供了DOM事件的支持。
// 这个特性包括两个事件：
// 1、deviceOrientation：封装了方向传感器数据的事件，可以获取手机静止状态下的方向数据（手机所处的角度、方位和朝向等）。
// 2、deviceMotion：封装了运动传感器的事件，可以获取手机运动状态下的运动加速度等数据。
// 使用这两个事件，可以很能够实现重力感应、指南针等有趣的功能。

// 现在在很多Native应用中有一个非常常见而时尚的功能 —— 摇一摇，摇一摇找人、摇一摇看新闻、摇一摇找金币。。。
// 也许在android或者ios的客户端上对这个功能你已经很了解了，但是现在，我将告诉你如何在手机网页上实现摇一摇的功能。

// 先来让我们了解一下设备运动事件 —— DeviceMotionEvent:返回设备关于加速度和旋转的相关信息，其中加速度的数据包含以下三个方向：
// x：横向贯穿手机屏幕；
// y：纵向贯穿手机屏幕；
// z：垂直手机屏幕。
// 鉴于有些设备没有排除重力的影响，所以该事件会返回两个属性：
// 1、accelerationIncludingGravity(含重力的加速度)
// 2、acceleration(排除重力影响的加速度)


// 首先在页面上要监听运动传感事件 
function init(){
　　if (window.DeviceMotionEvent) {
　　　　// 移动浏览器支持运动传感事件
　　　　window.addEventListener('devicemotion', deviceMotionHandler, false);
　　　　$("#yaoyiyaoyes").show();
　　} else{
　　　　// 移动浏览器不支持运动传感事件
　　　　$("#yaoyiyaono").show();
　　} 
}

// 那么，我们如何计算用户是否是在摇动手机呢？可以从以下几点进行考虑：
// 1、其实用户在摇动手机的时候始终都是以一个方向为主进行摇动的；
// 2、用户在摇动手机的时候在x、y、z三个方向都会有相应的想速度的变化；
// 3、不能把用户正常的手机运动行为当做摇一摇（手机放在兜里，走路的时候也会有加速度的变化）。
// 从以上三点考虑，针对三个方向上的加速度进行计算，间隔测量他们，考察他们在固定时间段里的变化率，而且需要确定一个阀值来触发摇一摇之后的操作。

// 首先，定义一个摇动的阀值
var SHAKE_THRESHOLD = 1000;
var GAME_OVER = false;
//总长度
var TotalShake = 500000;
var TotalScore = 0;
var totalSpeed = 0;
// 定义一个变量保存上次更新的时间
var last_update = 0;
// 紧接着定义x、y、z记录三个轴的数据以及上一次出发的时间
var x;
var y;
var z;
var last_x;
var last_y;
var last_z;


var count = 0;

var xmlHttp;
function createXMLHttp(){
    if (window.XMLHttpRequest)
    {
      xmlHttp = new XMLHttpRequest();
    }
    else if (window.ActiveXObject)
    {
      try
      {
        xmlHttp = new ActiveXObject('Msxml2.XMLHTTP');
      }
      catch(e)
      {
        try
        {
          xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
        }
        catch(e) {};
      }
    }
  }
function start(steps){
	createXMLHttp();
	var sendStr = "c_name=" + steps;
        var sendStr2 = encodeURI(sendStr);
       xmlHttp.open('GET','/Wall/vote/saveData.php?tokenTall=<?=$tokenTall?>&token=<?=$token?>&' + sendStr2,true);
       xmlHttp.onreadystatechange = doSomething;   
       xmlHttp.send(null); 
  }
  
  function doSomething()
  {
    
    if (xmlHttp.readyState == 4)
    {
      if (xmlHttp.status == 200)
      {
    var t2 = document.createTextNode(xmlHttp.responseText);
        document.getElementById('showtime').appendChild(t2);
    //document.getElementById('b').innerHTML = xmlHttp.responseText;
      }
    }
  }
function deviceMotionHandler(eventData) {
    
　　// 获取含重力的加速度
　　var acceleration = eventData.accelerationIncludingGravity; 

　　// 获取当前时间
　　var curTime = new Date().getTime(); 
　　var diffTime = curTime -last_update;
　　// 固定时间段
　　if (diffTime > 100) {
　　　　last_update = curTime; 

　　　　x = acceleration.x; 
　　　　y = acceleration.y; 
　　　　z = acceleration.z / 1.5; 

　　　　var speed = Math.abs(x - last_x + y - last_y + z - last_z) / diffTime * 10000; 
	
　　　　if (speed > SHAKE_THRESHOLD) { 
	   if(GAME_OVER == true){
	       alert("游戏已经结束");
	       TotalScore = 0;
	       return;
	    }
　　　　　　// TODO:在此处可以实现摇一摇之后所要进行的数据逻辑操作steps
　　　　　　count++;
	   totalSpeed = speed;
　　　　　　//$("#yaoyiyaoyes").hide();
　　　　　　$("#yaoyiyaoresult").show();
	   var steps = parseInt(totalSpeed / 1000);
	   var Intspeed = parseInt(totalSpeed / 1000);
	   //if(TotalScore>=100){GAME_OVER=true;TotalScore=100;}
	   start(5);
	   //TotalScore = TotalScore + 1;
           //Loading(Intspeed, 'http://www.bestchoice88.com/tpl/Wap/default/common/images/ding.jpg', 0, 0, TotalScore,curTime);
           state1();
      
　　　　　　//$("#yaoyiyaoresult").html("你的速度慢了，你才摇了" + Intspeed + "个bite！");
　　　　}

　　　　last_x = x; 
　　　　last_y = y; 
　　　　last_z = z; 
　　} 
} 


function state1(){
	$(".shakebgimg").removeClass("r2"); 
	$(".shakebgimg").addClass("r1");
	 setTimeout(state2,100);
	}
function state2(){	   
	$(".shakebgimg").removeClass("r1");
	$(".shakebgimg").addClass("r2"); 
	 setTimeout(state1,100);
	}

</script>
<div id="yaoyiyaoyes" style="font-size:15px;margin:10px;line-height:30px;display:none;">
　　
</div>
<div id="loading_div" ></div>
<div id="yaoyiyaoresult" style="font-size:10px;margin:10px;line-height:30px;display:none;"></div>
<script>
$(document).ready(function(){
init();
});
</script>
<script>
 var frequency = 50; 
var startTime = new Date().getTime(); 
var loadingBgcolor = "http://www.bestchoice88.com/tpl/Wap/default/common/images/ding2.jpg";
var totalWidth = 500;
/*
*参数说明:
*content：显示内容，可以为空；
*imageURL：将引用JS文件的路径设置即可；
*left：进度条显示位置left
*top：进度条显示位置top
*/
function Loading(content,  imageURL, left, top, step,curTime) 
{ 
  
 LoadTable(content, imageURL, left, top);
 showimage.style.display="";
 //window.setInterval("RefAct();", frequency); 
 //RefAct(step,curTime);
}  

function RefAct(step,curTime)
{  
 imgAct.width += step;
 if(imgAct.width > totalWidth-11)
 {
  var totalTime = (curTime - startTime) / 1000;
  GAME_OVER = true;
  var c_ne = getCookie("c_name"); 
  if(c_ne != ""){
      delCookie("c_name");
      }
  $("#yaoyiyaoresult").html("恭喜哦，你总共花了" + totalTime + "s！");
 }
}
function loadFirst(){
  //Loading('0', 'http://www.bestchoice88.com/tpl/Wap/default/common/images/ding.jpg', 0,0,0,startTime);
}
function LoadTable(content, imageURL, left, top)
{
 var strLoading;
 strLoading = ""; 
 strLoading += "<div id=\"showimage\" style=\"DISPLAY:none;color:white;width:100%;Z-INDEX:100;LEFT:" + left+ "px;POSITION:absolute;TOP:" + top+ "px;\" align=\"center\">";
 strLoading += "您目前的分数：" + TotalScore ;
 strLoading += "</div>";

 document.getElementById("loading_div").innerHTML = strLoading;
} 
</script>
<style>
  #imgAct{padding-left:6px;}
</style>
</div>
<div id="demo" class="shakebgimg">
 <img src="images/shankImg.png"/>
</div>
</body>
</html>