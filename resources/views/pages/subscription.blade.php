@extends('layouts.master')

@section('style')
    <!--Custom Styles-->
    <link rel="stylesheet" href="{{asset('css/subscription.css')}}">
@endsection

@section('content')
	<div class="subscription">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					<h2>@lang('subscription.title')</h2>
					<hr>
					<div class="list-group">
					  	<a href="{{ url('dashboard') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					    	@lang('dashboard.option1')
					  	</a>
					  	<a href="{{ url('dashboard/filter') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option2')
					  	</a>
					  	<a href="{{ url('dashboard/profile') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option3')
					  		@if(!$pcheck)
					  			<i class="fa fa-exclamation-circle"></i>
					  		@endif
					  	</a>
					  	<a href="{{ url('dashboard/saved-opp') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option4')
					  	</a>
					  	<a href="{{ url('dashboard/subscription') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center active">
					  		@lang('dashboard.option5')
					  	</a>
					  	<a href="{{ url('dashboard/support') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option6')
					  	</a>
					  	<a href="{{ url('logout') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option7')
					  	</a>
					</div>
				</div>
				<div class="col-md-9">
					
				</div>
			</div>
		</div>
	</div>
@endsection

@section('script')
	
@endsection