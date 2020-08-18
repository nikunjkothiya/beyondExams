<html>
<head>
    <title>Precisely</title>
</head>
<body>
    <form method="post" name="redirect" action="{{ $endPoint }}">
        <input type=hidden name="key" value="{{ $parameters['key'] }}">
        <input type=hidden name="hash" value="{{ $hash }}">
        <input type=hidden name="txnid" value="{{ $parameters['txnid'] }}">
        <input type=hidden name="amount" value="{{ $parameters['amount'] }}">
        <input type=hidden name="firstname" value="{{ $parameters['firstname'] }}">
        <input type=hidden name="email" value="{{ $parameters['email'] }}">
        <input type=hidden name="phone" value="{{ $parameters['phone'] }}">
        <input type=hidden name="productinfo" value="{{ $parameters['productinfo'] }}">
        <input type=hidden name="surl" value="{{ $parameters['surl'] }}">
        <input type=hidden name="furl" value="{{ $parameters['furl'] }}">
        <input type=hidden name="service_provider" value="{{ $parameters['service_provider'] }}">

        <input type=hidden name="lastname" value="">
        <input type=hidden name="curl" value="">
        <input type=hidden name="address1" value="">
        <input type=hidden name="address2" value="">
        <input type=hidden name="city" value="">
        <input type=hidden name="state" value="">
        <input type=hidden name="country" value="">
        <input type=hidden name="zipcode" value="">
        <input type=hidden name="udf1" value="{{ $parameters['udf1'] }}">
        <input type=hidden name="udf2" value="">
        <input type=hidden name="udf3" value="">
        <input type=hidden name="udf4" value="">
        <input type=hidden name="udf5" value="">
        <input type=hidden name="pg" value="">
    </form>
<script language='javascript'>document.redirect.submit();</script>
</body>
</html>

