<?php
$pageTitle = "Document";
$currentPage = "form_student";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
    <div class="container">
        <div class="row text-center">
            
    <form class="form">
        <div class="col-md-6">
            <label>Name :</label>
            <input type="text" id="name"  class="form-control">
        </div>
<br>
<div class="col-md-6">
    <input type="submit" id="btn" value="Add" class="btn btn-primary">
</div>

</form>
<ul>

</ul>


        </div>

        
    </div>
<script>
let nameStu = document.getElementById("name");
let listStu = document.querySelector("ul");
let btn = document.getElementById("btn");

btn.addEventListener("click",function(){
 if(nameStu.value==""){
    alert('eroore ')
 }else{
    let li = document.createElement("li");
    li.appendChild(document.createTextNode( nameStu.value ));
    listStu.appendChild(li);
    nameStu.value="";
 }
})

</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
