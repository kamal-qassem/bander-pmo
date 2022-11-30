<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" name="frmTransaction" id="frmTransaction">
   <input type="hidden" name="business" value="{{getSetting('email','paypal')}}">
   <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="item_name" value="rwerwewe">
   <input type="hidden" name="item_number" value="333">
   <input type="hidden" name="amount" value="20">   
   <input type="hidden" name="currency_code" value="USD">   
   <input type="hidden" name="cancel_return" value="http://demo.expertphp.in/payment-cancel">
   <input type="hidden" name="return" value="http://demo.expertphp.in/payment-status">
</form>
<script>document.frmTransaction.submit();</script>