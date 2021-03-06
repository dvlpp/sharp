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

    <link href='//fonts.googleapis.com/css?family=Lato:100,400,700,400italic,700italic' rel='stylesheet'
          type='text/css'>

    <link href="/sharp/sharp.min.css?v={{ $sharpVersion }}" rel="stylesheet">

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

                <li class="site"><a href="{{ route('sharp.cms') }}">{{ sharp_site_name() }}</a></li>

                @foreach(sharp_categories() as $categoryMenuKey => $categoryMenu)

                    @if(check_ability("category", $categoryMenuKey))
                        <li class="{{ isset($category) && $categoryMenuKey == $category->key() ? 'active' : '' }}">
                            <a class="category"
                               href="{{ route('sharp.cms.category', [$categoryMenuKey]) }}">{{ $categoryMenu->label() }}</a>
                        </li>
                    @endif

                @endforeach
            </ul>

            <div class="navbar-right user">
                {{ get_user_login() }}
                <a class="btn" href="{{ route('sharp.logout') }}">
                    <span class="fa-stack">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-power-off fa-stack-1x"></i>
                    </span>
                </a>
            </div>

            @if(sharp_languages())

                <div class="dropdown navbar-right languages">
                    <a class="btn navbar-btn" data-toggle="dropdown" data-target="#">
                        {{ $language }} <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach(sharp_languages() as $languageCode => $languageName)
                            <li>
                                <a href="{{ route("sharp.cms.lang", [$languageCode]) }}">
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

<div class="container-fluid" id="container">

    <div id="navcol">
        @yield('navcol')
    </div>

    <div id="maincontent">

        <div id="contextbar">
            <nav class="navbar" role="navigation">
                <ul class="nav navbar-nav">
                    @yield('contextbar')
                </ul>
            </nav>
        </div>

        <div id="page">
            @yield('content')
        </div>

    </div>
</div>

<footer>
    <p class="credits">Sharp {{ $sharpVersion }} — Développlan.</p>
</footer>

@section('scripts')
<script src="/sharp/sharp.ui.min.js?v={{ $sharpVersion }}"></script>
@show

</body>
</html>