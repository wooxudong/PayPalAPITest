(function(){

var totalprice = 0.0;

var cart =[];

$(".item_add").on("click",function(){
 
  $(".info").html("");

  var add_cart = {};
  var item = $(this);
  var quantity = item.parent().find("input").val();
  var price = item.parent().parent().find("h4.price").text();
  var name = item.parent().parent().find("h4.name").text();

  add_cart["name"] = name;
  add_cart["quantity"] = parseInt(quantity);
  add_cart["price"] = parseFloat(price.replace('$',''));

  var found = false;//if the good is already in the cart.
  for (var i = 0; i < cart.length; i++) {
    if(cart[i]["name"] === name) 
      {
        found = true;
        cart[i]["quantity"] += parseInt(quantity);
      }
  }
  
  if(!found) cart.push(add_cart);

  updateShoppingCart();
});

$(document).on("click","#delete",function(e){
  e.preventDefault();
  var name = $(this).parent().parent().find("td#name").text();
  console.log(name);
  for (var i = 0; i < cart.length; i++) {
    if(cart[i]["name"] === name) 
      {
        cart.splice(i,1);
      }
  }
  updateShoppingCart();
});

function updateShoppingCart()
{
  $(".cart_body").empty();
  totalprice = 0.0;
  for (var i = 0; i < cart.length; i++) {
    var cart_addrow = '<tr><td id="name">'+cart[i]["name"]+'</td><td id="quantity">X '+
                        cart[i]["quantity"]+'</td><td id="price">$'+cart[i]["price"]+
                        '</td><td><a id="delete" href="#">Delete</a></td></tr>';
    $(".cart_body").append(cart_addrow);
    totalprice += cart[i]["quantity"]*cart[i]["price"];
  }

  $(".total_price").text("Total Price: $"+totalprice);
}

//to pass the information to PayPal Express Checkout API.
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



