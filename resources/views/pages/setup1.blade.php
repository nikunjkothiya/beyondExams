@extends('layouts.master')

@section('style')
    <!--Custom Styles-->
    <link rel="stylesheet" href="{{asset('css/setup.css')}}">
@endsection

@section('content')
	<div class="language">
		<div class="container">
			<div class="row">
				<div class="col">
					<h1>@lang('setup.greetings')</h1>
					<div class="card">
						<div class="row">
							<div class="col">
								<div class="card-title">
									@lang('setup.cardtitle')
								</div>	
							</div>
						</div>
						<div class="row">
							<div class="col">
								<div class="card-content">
									<div class="list-group">
									  	@foreach($languages as $language)
									  		@if(\Config::get('app.locale') == $language->code)
									  			<a class="list-group-item list-group-item-action" href="{{url('lang/'.$language->code)}}">{{strtoupper($language->code)}} - {{$language->language}}<i class="fa fa-check-circle" style="color:#5b3495;float:right;font-size:15pt;"></i></a>
									  		@else
			  									<a class="list-group-item list-group-item-action" href="{{url('lang/'.$language->code)}}">{{strtoupper($language->code)}} - {{$language->language}}</a>
									  		@endif
			  							@endforeach
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<div class="card-btn">
									<div class="skip-btn" align="center">
										<a class="btn btn-lg btn-outline-secondary btn-skip" href="{{ url('setup/2') }}" role="button"></i>@lang('setup.skip')</a>
									</div>
								</div>
							</div>
							<div class="col">
								<div class="card-btn">
									<div class="next-btn" align="center">
										<a class="btn btn-lg btn-next" href="{{ url('setup/2') }}" role="button"></i>@lang('setup.next')</a>
									</div>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('script')

@endsection