@extends('layouts.main')

@section('title') {{'10 WEB'}} @endsection
@section('description') {{'Here you can see 10 WEB website blog page articles'}} @endsection
@section('keywords') {{'10 WEB, test, blog, articles'}} @endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @foreach ($articles as $article)
            <div class="col-md-4 mb-4">
                <div class="row">
                    <div class="card mb-4">
                        <div class="card-header">
                            @if ($article->image)
                                <img class='img-fluid' alt='article image' src="{{asset('storage').'/'. $article->image}}">
                            @endif
                        </div>
                        <div class="card-body">
                            {{ $article->title }}
                        </div>
                        <div class="card-footer">
                            {{ $article->article_date }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="d-felx justify-content-center">

        {{ $articles->links() }}

    </div>
</div>
@endsection
