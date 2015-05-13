(function(){

totalprice = 364.97;


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
    var data = JSON.stringify(cart);
    $.post("/PayPalCheckOut/checkout.php",{"pdata": data, "totalprice": totalprice})
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



