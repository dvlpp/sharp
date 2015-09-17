<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    @yield('meta')

    <title>Sharp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">

    <link href='http://fonts.googleapis.com/css?family=Lato:100,400,700,400italic,700italic' rel='stylesheet' type='text/css'>

    <link href="/sharp/sharp.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>

<body class="sharp-cms @yield('viewname')" id="sharp">

<header>

    <nav class="navbar" role="navigation">
        <div class="container-fluid">

            <ul class="nav navbar-nav">

                <li class="site"><a href="{{ route('cms') }}">{{ \Dvlpp\Sharp\Config\SharpSiteConfig::getName() }}</a></li>

                @foreach($cmsCategories as $catKey => $cat)

                    @if(check_ability("category", $catKey))
                        <li class="{{ isset($category) && $catKey == $category->key ? 'active' : '' }}">
                            <a class="category" href="{{ route('cms.category', [$catKey]) }}">{{ $cat->label }}</a>
                        </li>
                    @endif

                @endforeach
            </ul>

            <div class="navbar-right user">
                {{ get_user_login() }}
                <a class="btn" href="{{ route('logout') }}">
                    <span class="fa-stack">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-power-off fa-stack-1x"></i>
                    </span>
                </a>
            </div>

            @if(\Dvlpp\Sharp\Config\SharpSiteConfig::getLanguages())

                <div class="dropdown navbar-right languages">
                    <a class="btn navbar-btn" data-toggle="dropdown" data-target="#">
                        {{ $language }} <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach(\Dvlpp\Sharp\Config\SharpSiteConfig::getLanguages() as $languageCode => $languageName)
                            <li>
                                <a href="{{ route("cms.lang", [$languageCode]) }}">
                                    {{ $languageName }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

            @endif

        </div>
    </nav>

</header>

<div id="contenu">
    <div class="container-fluid" id="mainrow">

            <div id="navcol">

                @yield('navcol')

            </div>

            <div class="row" id="maincontent">

                <div class="col-sm-12" id="contextbar">

                    <nav class="navbar" role="navigation">
                        <ul class="nav navbar-nav">

                            @yield('contextbar')

                        </ul>
                    </nav>

                </div>

                <div class="col-sm-12" id="page">

                    @yield('content')

                </div>
            </div>
    </div>
</div>

<footer>

    <p class="credits">Sharp {{ $sharpVersion }} — Développlan.</p>

</footer>

@section("scripts")
<script src="/sharp/sharp.ui.min.js?v=3"></script>
@show

</body>
</html>