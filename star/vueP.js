function is_mobile() {return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));}
if(is_mobile()){alert("мобильные запрещены"); window.location.href="category2.php";}
function tpaTBFirst() {
             let resultTest1;
              let tbNomer=getCookie("id_tpa");
              let datasend = JSON.stringify({tbNomer, "teor":"prac" });   
              var request = new XMLHttpRequest();
              request.open('POST','tpaTBFirst.php',true);
              request.setRequestHeader('Content-Type', 'application/json')
              request.send(datasend);  
              request.onreadystatechange = function(){
      if (request.readyState===4 && request.status === 200 ){	
             resultTest1 = this.responseText;
             if (resultTest1==1){alert("Тест можно пройти только один раз в день");window.location.href="category2.php"};	
           } 
                                  }  }
tpaTBFirst();

let second=0;
let level = getCookie("level")*1;	
var optionsD = {
	
	year: 'numeric',
	month: 'numeric',
	day: 'numeric',
  };
  
 let full=15+1;
 let timeAll = 1200;


let numQuestion=-1;
let answersAlls=[];
function handler(e) {
	let i=true;
let j=0;
let first=true;
let numJustify=0;
let result = 0;
let time=0;
let answer=0;
let questionH = document.getElementById("question");
let question;
let timers=document.getElementById("timers");
timers.style.top = -52+"px";
timers.style.right = -10+"px";

let container;
let answers=document.getElementById("answer");
let clocks=document.getElementById("clock");

let imageFon = document.getElementById("imageFon");

	
	startTime();
	shuffle(test1);
	test1=test1.slice(0, full);


function ansewrsAll(){
numQuestion++; 
imageFon.style.opacity=0.4;
imageFon.src = "images/"+randomInteger(1,36)+".JPG"; imageFon.style.opacity=0.7;
if(test1.length<=numQuestion){sendAnsw(result, second); return false;}
answers.style.font = "20px/2.8 normal, Verdana, sans-serif";
answers.style.padding = "0px 10px 16px 10px";

answers.innerHTML = `ОТВЕТЫ:<br> <div class="container" id="container" ></div>`;
container=document.getElementById("container");
	
if (!test1[numQuestion]['justify']){ questionH.innerHTML='ЗАДАНИЕ: '+numQuestion+' <br><div class=questions" id="questions"></div>';} 
else {questionH.innerHTML+='<div class=questions" id="questions"></div>';}
question=document.getElementById("questions");

question.innerHTML = test1[numQuestion]['question'];



function countAnswer(){
	let arrAnswer=[];
	arrAnswer[0]=0;
	
for (let j = 1; j< 7;j++)
{ 
	if(!test1[numQuestion]['answer'+j]){return arrAnswer;} else {arrAnswer[j]=j;}
}
}



let countAnswers=countAnswer();
shuffle (countAnswers);
let allAns=[];
console.clear();
console.log("true"+test1[numQuestion].true1);
console.log("true"+test1[numQuestion].true2);
for (let j = 1; j< countAnswers.length;j++){

let answer = document.createElement('div');
		answer.className = "submit";	
		answer.id = "answer"+countAnswers[j];	
		
		answer.innerHTML = "&#9898"+" "+test1[numQuestion]['answer'+countAnswers[j]];
		//console.log(answer.id);
		

        if(test1[numQuestion]['answer'+j])
		{
            container.append(answer); 
		
				
		answer.addEventListener('click', e =>  { 
		
		 if(!first){return false;}
		let numAns=answer.id;
		numAns = numAns.replace(/\D/g,'')*1;
		
        if(answer.innerHTML.indexOf('⚪')!=-1)
        {answer.innerHTML = "&#9899"+" "+test1[numQuestion]['answer'+countAnswers[j]];
        allAns[j]=numAns;}
        else{ allAns[j]=""; answer.innerHTML = "&#9898"+" "+test1[numQuestion]['answer'+countAnswers[j]]};
        

		if (numQuestion===0){delitAnswer();}
				
		})
		} 
	
	}

    if(numQuestion>0){
    let continion = document.createElement('div'); 
    continion.className = "submit";	
    continion.id = "continion";	
    continion.innerHTML = "Далее";
    container.append(continion); 
    continion.addEventListener('click', e =>  { let allAns2 = allAns.filter(element => element != "");
    if(allAns2.length==2){
    if(allAns2.includes(test1[numQuestion].true1)&&allAns2.includes(test1[numQuestion].true2))
    {
		result+=test1[numQuestion]["weight"]; //ansewrsAll();
	}
    else{wrong(numQuestion); }}
    else{wrong(numQuestion);}    
    

	if(first){ delitAnswer();}

    
    });    
    }
    let yAnswer = document.getElementById('answer').offsetHeight+60; 
    let yMain = document.getElementById('main').offsetHeight;
    //if(yAnswer>yMain)
	document.getElementById('main').style.height=yAnswer+60+"px";
	document.getElementById('main').scrollIntoView(top);
    //else {document.getElementById('main').style.height = 94+"%";}
}




ansewrsAll();

function wrong(x){ if(!first){first=true; return false;}
    if(level!=4){ return false;}
    newAlert("верные ответы:\n1 - "+test1[x]['answer'+test1[x].true1]
                +"\n2 - "+test1[x]['answer'+test1[x].true2]);
               
            }

function sendAnsw(results,time)
{ 
	
let datasend;
let resultTest;


		
function demo1() {
  console.log('Taking a break...');
 	
			let tbNomer=getCookie("id_tpa");
			let result = results/(full-1)*100; // считаем процент	
			datasend = JSON.stringify({result,time,tbNomer});   
			console.log(datasend);
			
			var request = new XMLHttpRequest();
			
			request.open('POST','tpaTBSend.php',true);
			request.setRequestHeader('Content-Type', 'application/json')
			request.onreadystatechange = function(){
	if (request.readyState===4 && request.status === 200 ){		
		resultTest = this.responseText;	
		clocks.innerHTML="";
        let styleColor = "<div style = 'color: red'>ТЕСТ НЕ ПРОЙДЕН: "+getCookie("cat")+" категория(практика)</div>";
        if (result>80){styleColor = "<div style = 'color: green'>ТЕСТ ПРОЙДЕН: "+getCookie("cat")+" категория(практика)</div>";}
		questionH.innerHTML = "Ваш результат: <div class='questions' id='questions'>" 
							  + Math.round(result)+"% верных ответов,<div>" +"время - "+time+" сек. </div>"+styleColor+"</div>";
		if(document.getElementById("answer")){document.getElementById("answer").remove();}
		createExit();


} else {questionH.innerHTML = "Ошибка записи - "+ request.status} };
			
		   request.send(datasend);
			
           console.log("Hello A");

}



function demo2() {
	
			
			 
          //document.location.href = "";			 
			 }	
						


		    
	var promise1 = new Promise(function(resolve, reject) {
    setTimeout(function a(){ demo1(); resolve(); }, 200);
})		
	
var promise2 = new Promise(function(resolve, reject) {
    setTimeout(function b(){ //"Данные успершно переданы"; 
	console.log("Hello B"); resolve();}, 150);
})

	
		var promise3 = new Promise(function(resolve, reject) {
    setTimeout(function c(){ console.log("C"); demo2(); resolve();}, 200);
})	
	 
}	
	
function delitAnswer()
{	first=true;
	if(intervalId) {clearInterval(intervalId);}
container.innerHTML="";  //--------------------------------
 

 ansewrsAll(); //--------------------------------
 document.body.style.background="rgb(251, 251, 240)";	
}
let intervalId;

function newAlert(){
	//answer.removeEventListener("click", e =>  {returns}); 
	
	let nameEl1 = "answer"+test1[numQuestion].true1;
	let nameEl2 = "answer"+test1[numQuestion].true2;

	let trueEl1 = document.getElementById(nameEl1);
	let trueEl2 = document.getElementById(nameEl2);
	
	document.querySelectorAll('.submit').forEach((el) => {
		//el.classList.remove('havesubchild')
		if(el.id!="continion"){
		el.style.backgroundImage="none";
		el.style.color="#300";
		el.style.backgroundColor="#FFCCCC";}
	
	  })
	trueEl2.style.backgroundColor="#D4FFD2";
	trueEl2.style.color="#030";
	trueEl1.style.backgroundColor="#D4FFD2";
	trueEl1.style.color="#030";
	first=false;
	imageFon.style.opacity=0;
	imageFon.src = "images/zloy.JPG";
	imageFon.style.opacity=1;
	//trueEl.onclick = function(){delitAnswer()};
	}

    function startTime() {
        second++; 
        let date = new Date();
        dataTime.innerHTML = date.toLocaleTimeString()+", "+date.toLocaleDateString("ru", optionsD);
        if (second>(timeAll-100)){document.body.style.background = "rgba(255, 0, 0, "+0.4+")";}	
        if (second>(timeAll-50)){document.body.style.background = "rgba(255, 0, 0, "+0.7+")";}
        if (second>timeAll) {sendAnsw(result, second); return false;}
        let timer = timeAll-second;
        const min=Math.floor(timer/60);
        let sec;
        if(timer%60<10) {sec ="0"+timer%60;} else {sec = timer%60;}
        
        timers.innerHTML=min+":"+sec;	
        setTimeout(startTime, 1000);  }
 
        
        videoOn(1);
    
    function videoOn(s){
        var video = document.getElementById('video');
        function popupVideo() { 
        if (window.location.protocol=="http:"){ return false;}
        // Получаем доступ к камере
        if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) { 
        navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 320, max: 640}, height: {ideal: 240, max: 360 }, frameRate: 60} }).then(function(stream) {
        video.srcObject = stream; //video.play();
                 }).catch(function(err) {alert("Для тестирования необходимо включить камеру"); 
                  document.location.href = "../index.php"; });} 
                  var popup = document.querySelector('.popup'),
                  overlay = document.querySelector('.overlay'),
                  //close = document.querySelector('.close'),
                  close = document.getElementById('close');
                  button = document.querySelector('.button');
                        close.addEventListener('click', function() {
                            popup.style.display = "none";
                            overlay.style.display = "none";        });
                overlay.style.display = "block";
                popup.style.display = "block";
                positionCenter(popup);
                    
        function positionCenter(elem) {
                var elemHeight = window.getComputedStyle(elem).height,
                elemWidth = window.getComputedStyle(elem).width;
                elem.style.marginTop = "-" + parseInt(elemHeight) / 2 + "px";
                elem.style.marginLeft = "-" + parseInt(elemWidth) / 2 + "px"; }
                    } 
        if (s==1) {videoStart = 1; popupVideo(); }
       
        var qw = document.location.href;
        var val=qw.search('https'); 
          
        navigator.mediaDevices.getUserMedia({ audio: true, video: true})
            .then(stream => {
                const mediaRecorder = new MediaRecorder(stream);
                mediaRecorder.start();    
                let audioChunks = [];
                mediaRecorder.addEventListener("dataavailable",function(event) {
                    audioChunks.push(event.data); });
        
                
                document.addEventListener('click', function() { 
                    
                    if (numQuestion%5===0){ mediaRecorder.stop();mediaRecorder.start();}
                   //внимание попробывать сделать сохранение на предидущем слайде
                    try { SHAPE81.addEventListener('click', function() { mediaRecorder.stop(); 
                        stream = video.srcObject; // now get all tracks
                        tracks = stream.getTracks(); // now close each track by having forEach loop
                             tracks.forEach(function(track) { // stopping every track
                           track.stop();   });
                          // assign null to srcObject of video
                        video.srcObject = null;  }); }catch(err){try { SHAPE81.addEventListener('click', function() { mediaRecorder.stop();  
                            stream = video.srcObject; // now get all tracks
                            tracks = stream.getTracks(); // now close each track by having forEach loop
                                 tracks.forEach(function(track) { // stopping every track
                               track.stop();   });
                              // assign null to srcObject of video
                            video.srcObject = null;  }); }catch(err){return false;}}
                       
                       
                       });
                
                       mediaRecorder.addEventListener("stop", function() {
                    const audioBlob = new Blob(audioChunks, {
                        type: "video/webm"                });
                    let fd = new FormData();
                    fd.append('voice', audioBlob);
    
                    sendVoice(fd); 
    
    
                    audioChunks = [];  });    });	
        async function sendVoice(form) { const URL = 'videoSend.php';
            let promise = await fetch(URL, {
                method: 'POST',
                body: form});
            if (promise.ok) {
            let response =  await promise.json();
                console.log(response.data); } } 
    }

}




function justify()
{ 
//returns();
answer=-1; 
console.log("ну допустим");
answers.style.zIndex = "-1";
clocks.style.zIndex = "-1";
document.getElementById("answer1").style.zIndex = "1"; 
document.getElementById("answer1").innerHTML=test11[numJustify];
numJustify++;


}

function returnsAnswer()
{
let values = document.getElementById('story').value;
answer=0;


answerAll[numQuestion].texts=values;
clocks.style.zIndex = "3";	
document.getElementById("answer1").innerHTML="ОТВЕТЫ:";
document.getElementById("answer1").style.zIndex = "-1";	
numQuestion++;
question.innerHTML = test1[numQuestion]['question'];
 returns();
}

function returns()
{
document.getElementById("answer1").style.zIndex = "-1";	
answers.style.zIndex = "1";
}



document.addEventListener("DOMContentLoaded", handler);


function shuffle(array) {
	for (let i = array.length - 1; i > 0; i--) {
	  let j = Math.floor(Math.random() * (i + 1));
		if (j!=0) {[array[i], array[j]] = [array[j], array[i]];}
	 
	  
	}
  }

 
  function getCookie(name) {
	var cookie = " " + document.cookie;
	var search = " " + name + "=";
	var setStr = null;
	var offset = 0;
	var end = 0;
	if (cookie.length > 0) {
		offset = cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = cookie.indexOf(";", offset)
			if (end == -1) {
				end = cookie.length;
			}
			setStr = unescape(cookie.substring(offset, end));
		}
	}
	return(setStr);
}

function randomInteger(min, max) {
  // случайное число от min до (max+1)
  let rand = min + Math.random() * (max + 1 - min);
  return Math.floor(rand);
}

function createExit(){
let exit = document.createElement('div');
		exit.className = "submit";	
		exit.id = "exit";	
		exit.innerHTML = "выход";
		exit.innerHTML = "выход";
		exit.style.margin= "15px";
		exit.style.width="280px";
		exit.onclick = function() {
			exitOut();
		  };
          question.append(exit); 
		setTimeout(exitOut, 12000);
}

function exitOut(){window.location = "category2.php";}
