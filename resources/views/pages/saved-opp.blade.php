@extends('layouts.master')

@section('style')
    <!--Custom Styles-->
    <link rel="stylesheet" href="{{asset('css/saved-opp.css')}}">
@endsection

@section('content')
	<div class="saved-opp">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					<h2>@lang('saved-opp.title')</h2>
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
					  	<a href="{{ url('dashboard/saved-opp') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center active">
					  		@lang('dashboard.option4')
					  	</a>
					  	<a href="{{ url('dashboard/subscription') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
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
					<div class="material_card-stack">
						<a class="material_buttons material_prev" href="#"><i class="fa fa-arrow-up"></i></a>
						<ul class="material_card-list">
							<li class="material_card" style="background-color: #fff;"></li>
							<li class="material_card" style="background-color: #fff;"></li>		
							<li class="material_card" style="background-color: #fff;"></li>
						</ul>	
						<a class="material_buttons material_next" href="#"><i class="fa fa-arrow-down"></i></a>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('script')
	<script type="text/javascript">
		var $card = $('.material_card');
		var lastCard = $(".material_card-list .material_card").length - 1;

		$('.material_next').click(function(){ 
			var prependList = function() {
				if( $('.material_card').hasClass('activeNow') ) {
					var $slicedCard = $('.material_card').slice(lastCard).removeClass('transformThis activeNow');
					$('.material_card-list').prepend($slicedCard);
				}
			}
			$('.material_card').last().removeClass('transformPrev').addClass('transformThis').prev().addClass('activeNow');
			setTimeout(function(){prependList(); }, 150);
		});

		$('.material_prev').click(function() {
			var appendToList = function() {
				if( $('.material_card').hasClass('activeNow') ) {
					var $slicedCard = $('.material_card').slice(0,1).addClass('transformPrev');
					$('.material_card-list').append($slicedCard);
				}}
			
					$('.material_card').removeClass('transformPrev').last().addClass('activeNow').prevAll().removeClass('activeNow');
			setTimeout(function(){appendToList();}, 150);
		});

	</script>
@endsection