@extends('layouts.main')

@section('title') {{'10 WEB'}} @endsection
@section('description') {{'Here you can see 10 WEB website blog page articles'}} @endsection
@section('keywords') {{'10 WEB, test, blog, articles'}} @endsection

@section('styles')
<script src="{{ asset('css/bootstrap-datepicker.min.css') }}" defer></script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <nav class="navbar navbar-light bg-light p-0 mb-3">
              <div class="container-fluid p-0">
                <form class="d-flex">
                    <input type="text" class="form-control mr-2 article-date datepicker" name="article_date" value="{{old("article_date") ?? ''}}" placeholder="Select Date"/>
                    <input class="form-control mr-2" type="search" placeholder="Type" aria-label="Search" name="keyword" value={{old("keyword") ?? ''}}>
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
              </div>
            </nav>
        </div>
        @foreach ($articles as $article)
            <div class="col-md-4 mb-4">
                <div class="card">
                        @if ($article->image)
                            <img class='img-fluid' alt='article image' src="{{asset('storage').'/'. $article->image}}">
                        @endif
                    <div class="card-body">
                        <p>{{ $article->title }}</p>
                        <p class="mt-2">{{ $article->article_date }}</p>
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

@section('footer-scripts')
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}" defer></script>
    <script type="application/javascript">
        $(document).ready(function() {
            $('.datepicker').datepicker();
        });
    </script>
@endsection
