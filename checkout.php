<?php
include_once("PayPalCheckout/config.php");
include_once("PayPalCheckout/paypalapi.php");

$paypalmode = ($PayPalMode=='sandbox') ? '.sandbox' : '';

if($_POST) //Post Data received from product list page.
{
	//Mainly we need 4 variables from product page Item Name, Item Price, Item Number and Item Quantity.
	
	//Please Note : People can manipulate hidden field amounts in form,
	//In practical world you must fetch actual price from database using item id. Eg: 
	//$ItemPrice = $mysqli->query("SELECT item_price FROM products WHERE id = Product_Number");

	$item = json_decode($_POST["pdata"]);
	$totalprice = (float) $_POST["totalprice"];
	$itemAmount = 0.0;
	$padata = 	'&METHOD=SetExpressCheckout'.
				'&RETURNURL='.urlencode($PayPalReturnURL ).
				'&CANCELURL='.urlencode($PayPalCancelURL).
				'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE");
	for($i=0;$i<count($item);$i++)
	{	
		$itemAmount += $item[$i]->price*$item[$i]->quantity;
		$padata = $padata.'&L_PAYMENTREQUEST_0_NAME'.$i.'='.urlencode($item[$i]->name).
				'&L_PAYMENTREQUEST_0_AMT'.$i.'='.urlencode((float)$item[$i]->price).
				'&L_PAYMENTREQUEST_0_QTY'.$i.'='. urlencode((int)$item[$i]->quantity);

	}
	//Other important variables like tax, shipping cost
	$TotalTaxAmount 	= 2.58;  //Sum of tax for all items in this order. 
	$HandalingCost 		= 2.00;  //Handling cost for this order.
	$InsuranceCost 		= 1.00;  //shipping insurance cost for this order.
	$ShippinDiscount 	= -3.00; //Shipping discount for this order. Specify this as negative number.
	$ShippinCost 		= 3.00; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate.
	$GrandTotal = ($itemAmount + $TotalTaxAmount + $HandalingCost + $InsuranceCost + $ShippinCost + $ShippinDiscount);
	//Grand total including all tax, insurance, shipping cost and discount
	
	//Parameters for SetExpressCheckout, which will be sent to PayPal
				
		$padata = $padata."&PAYMENTREQUEST_0_ITEMAMT=".urlencode($itemAmount).
				'&NOSHIPPING=0'. //set 1 to hide buyer's shipping address, in-case products that does not require shipping
				'&PAYMENTREQUEST_0_TAXAMT='.urlencode($TotalTaxAmount).
				'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($ShippinCost).
				'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($HandalingCost).
				'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($ShippinDiscount).
				'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($InsuranceCost).
				'&PAYMENTREQUEST_0_AMT='.urlencode($GrandTotal).
				'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode).
				'&LOCALECODE=GB'. //PayPal pages to match the language on your website.
				'&CARTBORDERCOLOR=FFFFFF'. //border color of cart
				'&ALLOWNOTE=1';
				
				############# set session variable we need later for "DoExpressCheckoutPayment" #######
				$_SESSION['Item'] 				=  $item; //Item Name
				$_SESSION['ItemTotalPrice']     =  $itemAmount;
				$_SESSION['TotalTaxAmount'] 	=  $TotalTaxAmount;  //Sum of tax for all items in this order. 
				$_SESSION['HandalingCost'] 		=  $HandalingCost;  //Handling cost for this order.
				$_SESSION['InsuranceCost'] 		=  $InsuranceCost;  //shipping insurance cost for this order.
				$_SESSION['ShippinDiscount'] 	=  $ShippinDiscount; //Shipping discount for this order. Specify this as negative number.
				$_SESSION['ShippinCost'] 		=  $ShippinCost; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate.
				$_SESSION['GrandTotal'] 		=  $GrandTotal;

		//We need to execute the "SetExpressCheckOut" method to obtain paypal token
		$paypal= new MyPayPal();
		$httpParsedResponseAr = $paypal->PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);

		//Respond according to message we receive from Paypal
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) or "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
		{

				$token = $httpParsedResponseAr["TOKEN"];
				$getexp = '&'.http_build_query(array('TOKEN'=>$token));
				$httpParsedResponseAr2 = $paypal->PPHttpPost('GetExpressCheckoutDetails', $getexp, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature,$PayPalMode);
				
				if("SUCCESS" == strtoupper($httpParsedResponseAr2["ACK"]) or "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr2["ACK"])) 
				{
					$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
				 	$return["url"] = $paypalurl;
				 	$return["token"] = $httpParsedResponseAr["TOKEN"];
				 	$return["PayerID"] = $httpParsedResponseAr2["PAYERID"];
				 	echo json_encode($return);
				}

				else{
					echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
					echo "error";
				}
			 
		}else{
			print_r($httpParsedResponseAr);
		}
}
