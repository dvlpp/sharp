@extends('sharp::auth/authlayout')

@section('viewname') sharp-login @stop

@section('content')

    <div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3" id="login-form">

        <h1>
            {{ \Dvlpp\Sharp\Config\SharpSiteConfig::getName() }}
        </h1>

        @if(Session::get("flashMessage"))
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ Session::get("flashMessage") }}
            </div>
        @endif

        {!! Form::open(['route' => 'login', 'class' => 'form well', 'role' => 'form']) !!}

        <div class="form-group {{ $errors->first('login') ? 'has-error' : '' }}">
            {!! Form::text('login', '', ['autocomplete'=>'off', 'class' => 'form-control', 'placeholder' => trans('sharp::ui.login_loginPlaceholder')]) !!}
            {{ $errors->first('login', '<p class="help-block">:message</p>') }}
        </div>
        <div class="form-group {{ $errors->first('password') ? 'has-error' : '' }}">
            {!! Form::password('password', ['autocomplete'=>'off', 'class' => 'form-control', 'placeholder' => trans('sharp::ui.login_passwordPlaceholder')]) !!}
            {{ $errors->first('password', '<p class="help-block">:message</p>') }}
        </div>
        {!! Form::submit(trans('sharp::ui.login_submitBtn'), ["class"=>"btn btn-info"]) !!}
        <!--a class="btn btn-link" href="">{{ trans('sharp::ui.login_passwordForgotten') }}</a-->

        {!! Form::close() !!}

    </div>

@stop

