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

    <link href='http://fonts.googleapis.com/css?family=Lato:100,400,700,400italic,700italic' rel='stylesheet' type='text/css'>

    <link href="/packages/dvlpp/sharp/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/packages/dvlpp/sharp/css/sharp.css" rel="stylesheet">
    <link rel="stylesheet" href="/packages/dvlpp/sharp/bower_components/fontawesome/css/font-awesome.min.css">

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

                <li class="site"><a href="{{ URL::route('cms') }}">{{ \Dvlpp\Sharp\Config\SharpSiteConfig::getName() }}</a></li>

                @foreach($cmsCategories as $catKey => $cat)

                    @if(\Dvlpp\Sharp\Auth\SharpAccessManager::granted("category", "view", $catKey))
                        <li class="{{ !isset($masterCategoryKey) && isset($category) && $catKey == $category->key ? 'active' : '' }}">
                            <a class="category" href="{{ URL::route('cms.category', [$catKey]) }}">{{ $cat->label }}</a>
                        </li>
                    @endif

                @endforeach
            </ul>

            @if(\Dvlpp\Sharp\Config\SharpSiteConfig::getAuthService())
                <div class="navbar-right user">
                    {{ Session::get("sharp_user") }}
                    <a class="btn" href="{{ URL::route('logout') }}">
                        <span class="fa-stack">
                            <i class="fa fa-circle fa-stack-2x"></i>
                            <i class="fa fa-power-off fa-stack-1x"></i>
                        </span>
                    </a>
                </div>
            @endif

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

                <div class="col-sm-12 {{ isset($isEmbedded) && $isEmbedded ? "embedded" : "" }}" id="contextbar">

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

@section("scripts")
<script src="/packages/dvlpp/sharp/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/packages/dvlpp/sharp/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/packages/dvlpp/sharp/js/vendor/jquery-ui-1.10.4.custom.min.js"></script>
<script src="/packages/dvlpp/sharp/js/sharp.ui.min.js"></script>
@show

</body>
</html>