@extends('layouts.default')
@section('content')
    <div class="jumbotron">
        <h1>Hello Laravel</h1>
        <p class="lead">
            你现在所看到的是 <a href="https://fsdhub.com/books/laravel-essential-training-5.1">Laravel </a> 的示例项目主页。
        </p>
        <p>
            everthing now begin!
        </p>
        <p>
            <a class="btn btn-lg btn-success" href="{{ route('signup') }}" role="button">现在注册</a>
        </p>
    </div>
    @stop