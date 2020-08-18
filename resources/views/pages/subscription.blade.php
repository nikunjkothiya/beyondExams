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
					  	<a href="{{ url('dashboard/message') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option6')
					  	</a>
					  	<a href="{{ url('logout') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
					  		@lang('dashboard.option7')
					  	</a>
					</div>
				</div>
				<div class="col-md-9">
					<div class="content">
						@if ($errors->any())
	                      <span class="invalid-feedback" role="alert" style="width: 100%;margin-top: .25rem;font-size: 80%;color: #dc3545;display: block;">
	                        <strong>{{ $errors->first() }}</strong>
	                      </span>
	                    @endif
						@if($txnflag==0)
							<form method="POST" id="proceed" action="{{ url('checkout') }}">
							@csrf
							<div class="row">
								<div class="col-md-6">
									<div class="content-subtitle">@lang('subscription.content-subtitle')</div>
									<div class="content-title">@lang('subscription.content-title')</div>
									<div class="description">
										<ol>
											<li>@lang('subscription.perk1')</li>
											<li>@lang('subscription.perk2')</li>
											<li>@lang('subscription.perk3')</li>
										</ol>
									</div>	
								</div>
								<div class="col-md-6">
									<div class="plans">
										@foreach($plans as $plan)
											@if($plan->id == 1)
												<input type="radio" name="plans" id="plan-{{ $plan->id }}" value="{{ $plan->id }}" class="plan">
												<label for="plan-{{ $plan->id }}">
													{{$plan->name}} for free <i class="fa fa-check-circle"></i>
												</label>
											@else
												<input type="radio" name="plans" id="plan-{{ $plan->id }}" value="{{ $plan->id }}" class="plan">
												<label for="plan-{{ $plan->id }}">
													₹{{$plan->price}} for {{$plan->name}} <i class="fa fa-check-circle"></i>
												</label>
											@endif
										@endforeach
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6"></div>	
								<div class="col-md-6">
									<button type="button" class="btn btn-lg buy btn-block" data-toggle="modal" data-target="#checkout" hidden="hidden">
									  @lang('subscription.buy') <i class="fa fa-arrow-right"></i>
									</button>
								</div>
							</div>
							</form>
						@else
							<div class="sub">
								<div class="row">
									<div class="col">
										<h5>@lang('subscription.ack')</h5>
										<br>
										<h1>{{ $txnflag }}</h1> 
										<p>@lang('subscription.days')</p>
										
										@lang('subscription.expire')<b>{{ Carbon\Carbon::now()->addDays($txnflag)->toDateString() }}</b>
									</div>
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	@include('modals.checkout')
@endsection

@section('script')
	<script type="text/javascript">
		$('.plan').click(function(e){
		    $('button.buy ').removeAttr('hidden');
		});
	</script>
@endsection