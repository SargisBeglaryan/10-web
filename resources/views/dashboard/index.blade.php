@extends('layouts.app')

@section('styles')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert mb-2 alert-success alert-dismissible fade show" role="alert">
                          <strong>{{ session('success') }}</strong>
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert mb-2 alert-danger alert-dismissible fade show" role="alert">
                          <strong>{{ session('error') }}</strong>
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                    @endif
                    <form method="POST" action="{{route('dashboard.update', $scraperSettings->id)}}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="scrapLimit">Scrap limit</label>
                            <input type="number" min="1" max="50" class="form-control {{$errors->has('limit') ? 'is-valid' : ''}}" id="scrapLimit" name="limit" placeholder="Type limit" value="{{ !empty(old('limit')) ? old('limit') : $scraperSettings->limit}}">
                            @if($errors->has('limit'))
                                <div class="valid-feedback">{{$errors->first('limit')}}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="dateRange">Date range</label>
                            <input type="text" id="dateRange" name="daterange" class="form-control {{$errors->has('daterange') ? 'is-valid' : ''}}" value="{{!empty(old('daterange')) ? old('daterange') : $scraperSettings->getFormattedDateRange()}}" />
                            @if($errors->has('daterange'))
                                <div class="valid-feedback">{{$errors->first('daterange')}}</div>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary">Update Settings</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    <script type="application/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="application/javascript">
        $(document).ready(function() {
            $('input[name="daterange"]').daterangepicker();
        });
    </script>
@endsection