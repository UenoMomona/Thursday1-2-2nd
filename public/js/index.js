const btn = document.getElementById("ok");
const inText = document.getElementById("inText");


btn.addEventListener("click", function(){
  let in_val = inText.value || "";
  alert(in_val + "が入力されました");
});


console.log("js読み込まれている");
