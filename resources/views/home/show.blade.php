@extends('layouts.main')

@section('title') {{'10 WEB'}} @endsection
@section('description') {{$article->title}} @endsection
@section('keywords') {{$article->excerpt}} @endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7 mt-4">
            <div class="card position-relative article-content">
                    @if ($article->image)
                        <img class='img-fluid' alt='article image' src="{{asset('storage').'/'. $article->image}}">
                    @endif
                <div class="card-body">
                    <h1>{{ $article->title }}</h1>
                    <p class="mt-4">
                        <strong class="mr-3">{{ $article->article_date }}</strong>
                        <span>{{ $article->author }}</span>
                    </p>
                    <div class="article-content mt-5">
                        {!! json_decode($article->original_content) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
