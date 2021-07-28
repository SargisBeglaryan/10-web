@extends('layouts.main')

@section('title') {{'10 WEB'}} @endsection
@section('description') {{'Here you can see 10 WEB website blog page articles'}} @endsection
@section('keywords') {{'10 WEB, test, blog, articles'}} @endsection

@section('styles')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <nav class="navbar navbar-light bg-light p-0 mb-3">
              <div class="container-fluid p-0">
                <form class="d-flex" method="GET" action="{{route('home')}}">
                    <input type="text" class="form-control mr-2 article-date datepicker" name="article_date" value="{{Request::input("article_date") ?? ''}}" placeholder="Select Date" autocomplete="off" />
                    <input class="form-control mr-2" type="search" placeholder="Type" aria-label="Search" name="keyword" value="{{Request::input("keyword") ?? ''}}">
                    <button class="btn btn-outline-danger mr-2 reset-button" type="reset">Reset</button>
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
              </div>
            </nav>
        </div>
        @if ($articles->count() == 0)
            <div class="mt-5" style="font-size: 20px;">Not found</div>
        @endif
        @foreach ($articles as $article)
            <div class="col-md-4 mb-4">
                <div class="card position-relative">
                        @if ($article->image)
                            <img class='img-fluid' alt='article image' src="{{asset('storage').'/'. $article->image}}">
                        @endif
                    <div class="card-body">
                        <h1 style="font-size: 20px;">{{ $article->title }}</h1>
                        <p class="mt-2">{{ $article->article_date }}</p>
                    </div>
                    <a class="article-link" href="{{route('article.show', $article->id)}}"></a>
                </div>
            </div>
        @endforeach
    </div>
    <div class="d-felx justify-content-center">

        {{ $articles->links() }}

    </div>
</div>
@endsection

@section('footer-scripts')
    <script type="application/javascript">
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                showRightIcon: false,
                uiLibrary: 'bootstrap4'
            });

            $('.reset-button').on('click', function () {
                $('form input').each(function () {
                    $(this).removeAttr('value');
                });
            });
        });
    </script>
@endsection
