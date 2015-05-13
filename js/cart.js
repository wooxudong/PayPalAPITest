(function(){

totalprice = 628.99;


var cart =[
    {"name":"Deco Lamp",
      "quantity": 1,
      "price": 34.99
    },
    {
      "name":"Read Table",
      "quantity": 2,
      "price": 164.99
    }
];

$(".item_add").on("click",function(){

});


$(".paypalcheckout").on("click",function(e){

  e.preventDefault();

  if(totalprice > 0)
  {
    $.post("PayPalCheckOut/checkout.php",{"pdata": cart, "totalprice": totalprice})
      .done(function(res)
        {
          alert("success");
        });
  }
  else
  {
    $(".info").html("You haven't shopped any goods yet.");
  }
});




})();



