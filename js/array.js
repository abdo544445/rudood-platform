let students =["mohammed","osamah","fatimah","Hassan","fatimah"];

let listUl = document.querySelector("ul");

for(i = 0 ; i < students.length ; i++){

    let li = document.createElement("li");
    li.appendChild(document.createTextNode(students[i]));
    listUl.appendChild(li);
}

// alert(students.length)


// for(i=0; i < students.length ;i++){
//  alert(students[i]);
// }
// let x = 4 ;

// for(i=0; i < students.length ;i++){

//     if(students[i]==="fatimah"){
//         alert(students[i]);
//     }
// }