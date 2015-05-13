(function(){

totalprice = 628.99;

$(".item_add").on("click",function(){

});


$(".paypalcheckout").on("click",function(){
  if(totalprice > 0)
  {
    $.post("checkout.php",totalprice)
      .done(function(res))
        {

        }
  }
});




})();



