(function(){

var totalprice = 364.97;

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
    $.ajax({
      method: "POST",
      url: "/checkout.php",
      dataType: "json",
      data: {"pdata": data, "totalprice": totalprice}
      }).done(function(res)
      {
        token = res["token"];
        PayerID = res["PayerID"];
        console.log(PayerID);
        console.log(token);
        window.location.href=res["url"];
      });
  }
  else
  {
    $(".info").html("You haven't shopped any goods yet.");
  }
});

})();



