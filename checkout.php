<?php
include_once("PayPalCheckOut/config.php");
include_once("PayPalCheckOut/paypalapi.php");

$paypalmode = ($PayPalMode=='sandbox') ? '.sandbox' : '';

if($_POST) 
{
    
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
	$ShippinDiscount 	= -3.00; //Shipping discount for this order. 
	$ShippinCost 		= 3.00; 
	$GrandTotal = ($itemAmount + $TotalTaxAmount + $HandalingCost + $InsuranceCost + $ShippinCost + $ShippinDiscount);
	//Grand total including all tax, insurance, shipping cost and discount
	
	//Parameters for SetExpressCheckout, which will be sent to PayPal
				
		$padata = $padata."&PAYMENTREQUEST_0_ITEMAMT=".urlencode($itemAmount).
				'&NOSHIPPING=0'. 
				'&PAYMENTREQUEST_0_TAXAMT='.urlencode($TotalTaxAmount).
				'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($ShippinCost).
				'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($HandalingCost).
				'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($ShippinDiscount).
				'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($InsuranceCost).
				'&PAYMENTREQUEST_0_AMT='.urlencode($GrandTotal).
				'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode).
				'&LOCALECODE=GB'. 
				'&CARTBORDERCOLOR=FFFFFF'. 
				'&ALLOWNOTE=1';
				
				############# set session variable we need later for "DoExpressCheckoutPayment"
				$_SESSION['Item'] 				=  $item; 
				$_SESSION['ItemTotalPrice']     =  $itemAmount;
				$_SESSION['TotalTaxAmount'] 	=  $TotalTaxAmount; 
				$_SESSION['HandalingCost'] 		=  $HandalingCost;  
				$_SESSION['InsuranceCost'] 		=  $InsuranceCost;  
				$_SESSION['ShippinDiscount'] 	=  $ShippinDiscount; 
				$_SESSION['ShippinCost'] 		=  $ShippinCost; 
				$_SESSION['GrandTotal'] 		=  $GrandTotal;

		//We need to execute the "SetExpressCheckOut" method to obtain paypal token
		$paypal= new MyPayPal();
		$httpParsedResponseAr = $paypal->PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);

		//Respond according to message receiveed from Paypal
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) or "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
		{
			$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
			$return["url"] = $paypalurl;
			echo json_encode($return);
		}else{
			echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
			echo "error";
		}
}
