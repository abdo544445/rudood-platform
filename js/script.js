let btn = document.getElementById("btn");
let number1 = document.getElementById("num1");
let number2 = document.getElementById("num2");
let result = document.getElementById('rslt');


btn.addEventListener('click',function(){
  result.innerHTML = Number( number1.value ) +parseFloat( number2.value)
number1.value="";
number2.value="";
})