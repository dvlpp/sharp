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

    <link href="/sharp/sharp.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>

<body class="sharp-auth sharp-login" id="sharp">

<div id="contenu">
    <div class="container">

        <div class="row">
            <div id="maincontent">

                <div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3" id="login-form">

                    <h1>{{ sharp_site_name() }}</h1>

                    @if (count($errors))
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    {!! Form::open(['route' => 'login', 'class' => 'form well', 'role' => 'form']) !!}

                    <div class="form-group {{ $errors->first('login') ? 'has-error' : '' }}">
                        {!! Form::text('login', '', ['autocomplete'=>'off', 'class' => 'form-control', 'placeholder' => trans('sharp::ui.login_loginPlaceholder')]) !!}
                    </div>

                    <div class="form-group {{ $errors->first('password') ? 'has-error' : '' }}">
                        {!! Form::password('password', ['autocomplete'=>'off', 'class' => 'form-control', 'placeholder' => trans('sharp::ui.login_passwordPlaceholder')]) !!}
                    </div>

                    {!! Form::submit(trans('sharp::ui.login_submitBtn'), ["class"=>"btn btn-info"]) !!}

                    {!! Form::close() !!}

                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>