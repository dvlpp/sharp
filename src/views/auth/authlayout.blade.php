<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Sharp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">

    <link href="/packages/dvlpp/sharp/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/packages/dvlpp/sharp/css/sharp.css" rel="stylesheet">
    <link rel="stylesheet" href="/packages/dvlpp/sharp/bower_components/fontawesome/css/font-awesome.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>

<body class="sharp-auth @yield('viewname')" id="sharp">

<div id="contenu">
    <div class="container">

        <div class="row">
            <div id="maincontent">
                @yield('content')
            </div>
        </div>
    </div>
</div>

@section("scripts")
<script src="/packages/dvlpp/sharp/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/packages/dvlpp/sharp/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/packages/dvlpp/sharp/js/sharp.ui.min.js"></script>
@show

</body>
</html>