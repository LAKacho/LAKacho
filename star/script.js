//container.onmouseover = container.onmouseout = handler;
console.log("!!!");
let i=true;
let j=0;
let numQuestion=-1;
let numJustify=0;

let answer=0;
let questionH = document.getElementById("question");
let question;
let timers=document.getElementById("timers");
let container;
let answers=document.getElementById("answer");
let clocks=document.getElementById("clock");
let answersAlls=[];





function handler(e) {

function ansewrsAll(){
numQuestion++; if(test1.length<=numQuestion){console.log(answersAlls); sendAnsw(answersAlls); return false;}
answers.style.font = "22px/2.8 normal, Verdana, sans-serif";
answers.style.padding = "0px 10px 16px 10px";

answers.innerHTML = `ОТВЕТЫ:<br> <div class="container" id="container" ></div>`;
container=document.getElementById("container");
	
if (!test1[numQuestion]['justify']){ questionH.innerHTML='ВОПРОС: <br><div class=questions" id="questions"></div>';} else {questionH.innerHTML='Уважаемый работник!<br><div class=questions" id="questions"></div>';}
question=document.getElementById("questions");

question.innerHTML = test1[numQuestion]['question'];
if(test1[numQuestion]['slider']){ createSlider(); return false;}

if (!test1[numQuestion]['answer1']){ textareaNew();}
let bi=0;
for (let j = 1; j< 7;j++)
{
	
let answer = document.createElement('div');
		answer.className = "submit";	
		answer.id = "answer"+j;	
		answer.innerHTML = "&#9898"+" "+test1[numQuestion]['answer'+j];
		if(test1[numQuestion]['answer'+j])
		{container.append(answer); 
		 bi++;
		answer.addEventListener('mouseover', e =>  { 
		answer.innerHTML = "&#9899"+" "+test1[numQuestion]['answer'+j];
		})
		
		answer.addEventListener('mouseout', e =>  { 
		answer.innerHTML = "&#9898"+" "+test1[numQuestion]['answer'+j];
		})
		
		answer.addEventListener('click', e =>  { 
		console.log(answer.innerHTML); 
		answersAlls[numQuestion].answer = answer.innerHTML; 
		if(answer.innerHTML.includes("другое")) {textareaNew();} else{delitAnswer();}
		
		})
		} 
	
} //alert(bi); 
}

function textareaNew(b){
		let a;
		
		if(b<7){a=1;}
		if (b>6){a=2;}
		if(b=undefined){a=3;}
		
		let inputTextaria;
		let	answer;
		let valid = false;
		if (a==1)
		{
		if(document.getElementById("textarea")){return false;}
		sliderExplain = document.getElementById("answerSlider");
		answer = document.getElementById("answer");
		inputTextaria = document.createElement('textarea');
		inputTextaria.className = "form-control2";
		inputTextaria.id = "textarea";		
		
		inputTextaria.style.height = "100px";
		inputTextaria.setAttribute ("required","");
		inputTextaria.setAttribute ("placeholder","объясните, почему?");
		answer.insertBefore(inputTextaria,sliderExplain);
		
		
		}
		if (a==2)
		{
		if(document.getElementById("textarea"))
		{	textarea = document.getElementById("textarea");
			textarea.parentNode.removeChild(textarea); valid = true;
		}
		else {valid = true;}	
			
		}
		
		if (a!=1&&a!=2) {
		answers.innerHTML = `конкретизируйте:<br> <div class="container" id="container" ></div>`;
		container=document.getElementById("container");
		inputTextaria = document.createElement('textarea');
		inputTextaria.className = "form-control";
		inputTextaria.id = "textarea";		
		inputTextaria.style.height = "100px";
		inputTextaria.setAttribute ("required","");
		container.append(inputTextaria); 
		
	 	answer = document.createElement('div');
		answer.className = "submit";	
		answer.id = "answer"+j;	
		answer.innerHTML = "Готово";
		container.append(answer);
		answer.addEventListener('click', e =>  { 
		if(inputTextaria.value.length<2){alert("Заполните поле"); return false;}
		console.log(inputTextaria.value); 
		answersAlls[numQuestion].explain = inputTextaria.value;
		delitAnswer();
		})
} return valid;}

function delitAnswer()
{
container.innerHTML="";
 ansewrsAll(); 	
}

function createSlider()
{

answers.innerHTML="Оцените по шкале от 0 до 10, где 0 – обозначает максимально отрицательную оценку, а 10 – максимально положительную."; 
answers.style.font = "14px/1.2 normal, Verdana, sans-serif";
answers.style.paddingTop = "15px";



let inputSlider = document.createElement('input');
inputSlider.className = "slider";
inputSlider.id = "myRange";	
inputSlider.type = "range";	

inputSlider.setAttribute("value", 7);
inputSlider.setAttribute("max", 10);
inputSlider.setAttribute("min", 1);

answers.append(inputSlider);

let valueSlider = document.createElement('input');
valueSlider.type = "number";
valueSlider.id = "demo";
valueSlider.setAttribute("max", 10);
valueSlider.setAttribute("min", 1);

answers.append(valueSlider);
let valid = true;
var slider = document.getElementById("myRange");
var output = document.getElementById("demo");
let inputTextaria;
output.value = slider.value;

slider.oninput = function() { 
  output.value = this.value;
  valid=textareaNew(output.value);
  inputTextaria=document.getElementById("textarea");
  }


output.oninput = function() { 
  slider.value = this.value;
  valid=textareaNew(output.value);
  inputTextaria=document.getElementById("textarea");
}



let	answer = document.createElement('div');
		answer.className = "submit";	
		answer.id = "answerSlider";	
		answer.innerHTML = "Готово";
		answers.append(answer);
		answer.addEventListener('click', e =>  
		{ console.log ("номер вопроса" +numQuestion+", ответ " + slider.value );
		answersAlls[numQuestion].answer = slider.value; 
		if(!inputTextaria){delitAnswer();}
		if(inputTextaria){ if(inputTextaria.value.length<2){alert("Заполните поле"); return false;} else { answersAlls[numQuestion].explain = inputTextaria.value;
							  answersAlls[numQuestion].answer = slider.value;
		delitAnswer(); } 
		}  });
}

ansewrsAll();

/*one.innerHTML="&#9898"+test1[numQuestion]['answer1']; 
two.innerHTML="&#9898"+test1[numQuestion]['answer2'];
three.innerHTML="&#9898"+test1[numQuestion]['answer3'];
if(test1[numQuestion]['answer4']) {four.innerHTML="&#9898"+test1[numQuestion]['answer4'];} else {four.innerHTML="";} 
if(test1[numQuestion]['answer4']) {five.innerHTML="&#9898"+test1[numQuestion]['answer5'];}	
if(test1[numQuestion]['answer4']) {six.innerHTML="&#9898"+test1[numQuestion]['answer6'];}
*/
function sendAnsw(a)
{ const pure = /[^a-zа-я0-9.,]+/g
for (let ii=0; ii<test1.length; ii++)
	
	{
	answersAlls[ii].answer=answersAlls[ii].answer.replace(pure,'');
	answersAlls[ii].explain=answersAlls[ii].explain.replace(pure,'');	
	}	
comsole.log(answersAlls);
}	
	 
	
	
	/* if(i){
	yes.addEventListener('mouseover', e =>  {  i=false; j=j+yes_num;
	yes_num=1; console.log(j+" yes"); })
	
	not.addEventListener('mouseover', e =>  {  i=false; j=j+not_num;
	not_num=1;console.log(j+" not");})
	
	dont.addEventListener('mouseover', e =>  {  i=false; j=j+dont_num;
	dont_num=1; console.log(j+" dont");})
	
	
	//**************
	
	
	yes.addEventListener('mousedown', e =>  {  i=false; 
	yes.innerHTML="&#9899 Да";
	;})
		
	not.addEventListener('mousedown', e =>  {  i=false; 
	not.innerHTML="&#9899 Нет";
	})
			
	dont.addEventListener('mousedown', e =>  {  
	i=false;
	dont.innerHTML="&#9899 Не уверен";
	})
	
	//**************
	yes.addEventListener('click', e =>  {  i=false; 
	yes.innerHTML="&#9898"+test1[numQuestion]['answer']; 
	answer="yes"; 
	
	
	answerAll[numQuestion].answer=answer;
	answerAll[numQuestion].attem=j;
	
	zerro(); delitAnswer(); 
	
	
	clock();	
	
	})
	
	not.addEventListener('click', e =>  {  i=false; 
	not.innerHTML="&#9898 Нет";
	answer="not"; 
	answerAll[numQuestion].answer=answer;
	answerAll[numQuestion].attem=j;
	zerro(); delitAnswer(); clock();	
	})
	
	dont.addEventListener('click', e =>  {  
	i=false;
	dont.innerHTML="&#9898 Не уверен";
	answer="dont"; 
	answerAll[numQuestion].answer=answer;
	answerAll[numQuestion].attem=j;
	delitAnswer();
	if(test1[numQuestion].justify==="yes"){justify();}
	zerro(); clock();	
	})
	
	} */


}

/*if(end != 0){clearTimeout(timer); alert();  current=0; numQuestion++; question.innerHTML = test1[numQuestion]['question']; timerQuestion(0, test1[numQuestion]['timeanswer'], 0);}
if(current >= to){ alert("время вышло");  numQuestion++; question.innerHTML = test1[numQuestion]['question']; timerQuestion(0, test1[numQuestion]['timeanswer'], 0);}*/


/*function clock(){
var counter = 0;

if (numQuestion===test1.length-1){
	answerAll[numQuestion].time=counter;
	localStorage.setItem('res', JSON.stringify(answerAll));
	window.location.href="result.html";}

  
  
  const intervalId = setInterval(() => {
  counter += 1;
  let counter2 = test1[numQuestion]['timeanswer']-counter;
	if(counter2<0){counter2=0;}
  if(counter2>9){timers.innerHTML=counter2;} else {timers.innerHTML="0"+counter2;}
  
if (counter >= (test1[numQuestion]['timeanswer']+1)) {
    
    clearInterval(intervalId);
	answerAll[numQuestion].answer="";
	answerAll[numQuestion].attem=j;
	clock()
	numQuestion++;
	question.innerHTML = test1[numQuestion]['question'];
  }

if (answer == -1) {
    
	answerAll[numQuestion].time=counter;
	counter=0;
	if(counter2>9){timers.innerHTML=counter2;} else {timers.innerHTML="0"+counter2;}
	clearInterval(intervalId);
	question.innerHTML = test1[numQuestion]['question'];
  } 

if (typeof(answer)=="string") {
    
	answerAll[numQuestion].time=counter;
	
	console.log(answerAll);
	
	counter=0;
	if(counter2>9){timers.innerHTML=counter2;} else {timers.innerHTML="0"+counter2;}
	clearInterval(intervalId);
	answer=0;
	numQuestion++;
	question.innerHTML = test1[numQuestion]['question']; returns(); 
  } 
  

  
}, 1000);

}*/


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
returns(); clock();
}

function returns()
{
document.getElementById("answer1").style.zIndex = "-1";	
answers.style.zIndex = "1";
}

/*function delitAnswer()
{
answers.style.zIndex = "-1";
document.getElementById("answer1").style.zIndex = "3";
}*/

// использование:

document.addEventListener("DOMContentLoaded", handler);
document.addEventListener("DOMContentLoaded", clock);
