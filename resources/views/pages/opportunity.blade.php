@extends('layouts.master')

@section('style')
    <!--Custom Styles-->
    <link rel="stylesheet" href="{{asset('css/opportunity.css')}}">
    <style type="text/css">
    	#header{
			background: url('{{ $opportunity->image }}') center;
			-webkit-background-size: cover;
  			-moz-background-size: cover;
  			-o-background-size: cover;
  			background-size: cover;
		}

    </style>
@endsection

@section('content')
	<script src="{{asset('js/notify.min.js')}}"></script>
	<meta name="_token" content="{{ csrf_token() }}">
	<div class="opportunity">
		<div class="container">

			<div class="row">
				<div class="col">
					<h2>
						<!-- <a href="{{ url('dashboard') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a> -->
						@lang('opportunity.title')
					</h2>
					<div class="card">
						<div class="row">
							<div class="col">
								<div id="header">
									<div class="overlay">
										<div class="text">{{ $opportunity->title }}</div>	
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<div class="content">
									<div class="row">
										<div class="col-auto mr-auto">
											<div class="deadline">
												@lang('opportunity.deadline') : {{ $opportunity->deadline }}
											</div>
										</div>
										@auth
											@if(\Auth::user()->saved_opportunities->contains($opportunity->id))
												<div class="col-auto">
													<div class="save">
														<a href="#" title="Save" id="save"><i id="saveico" class="fa fa-star"></i></a>
													</div>
												</div>
											@else
												<div class="col-auto">
													<div class="save">
														<a href="#" title="Save" id="save"><i id="saveico" class="fa fa-star-o"></i></a>
													</div>
												</div>
											@endif
										@endauth
									</div>
									<div class="row">
										<div class="col">
											<div class="description">
												{{ $opportunity->description }}
											</div>
										</div>
									</div>
									
								</div>
							</div>
						</div>
						<div class="row" style="margin:0px 10px;">
							<div class="col">
								@foreach($opportunity->tags as $tag)
									<span class="badge badge-secondary">{{ $tag->tag }}</span>
								@endforeach
							</div>
							<div class="col-auto ml-auto">
								<a href="mailto:?Subject={{$opportunity->title}}&amp;Body={{$opportunity->description}} {{ url('/opportunity/'.$opportunity->id) }}" class="btn btn-lg share-btn">@lang('opportunity.share')</a>
							</div>
							<div class="col-auto">
								@auth
									<a href="{{ $opportunity->link }}" target="_blank" class="btn btn-lg apply-btn">@lang('opportunity.apply')</a>
								@else
									<a href="{{ url('login') }}" class="btn btn-lg apply-btn">@lang('opportunity.apply')</a>
								@endauth
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('script')
	<script type="text/javascript">
		$('#save').click(function(e) {
			e.preventDefault(); 
			var data = {'_token': "{{ csrf_token() }}",'id':"{{$opportunity->id}}" };
		 	$.ajaxSetup({
		    	headers: {
		      		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
		    	}
		  	});
			if($( "#saveico" ).hasClass( "fa-star-o" )){
				$.ajax({
			    	url: "{{ url('/opportunity/save')}}",
			    	type: 'POST',
			    	data: data,
			    	success: function(response) {
			      		if(response.status_code == '200'){
			        		$("#saveico").attr('class', 'fa fa-star');
			        		$("#save").attr('title', 'unSave');
			        		$.notify("Saved!", "success");
			      		}
			   		},
			    	error: function (xhr, ajaxOptions, thrownError) {
			           console.log(xhr.status);
			           console.log(xhr.responseText);
			           console.log(thrownError);
			       	}
			  	});
			}
			else{
				$.ajax({
			    	url: "{{ url('/opportunity/unsave')}}",
			    	type: 'POST',
			    	data: data,
			    	success: function(response) {
			      		if(response.status_code == '200'){
			        		$("#saveico").attr('class', 'fa fa-star-o');
			        		$("#save").attr('title', 'Save');
			        		$.notify("Removed!", "success");
			      		}
			   		},
			    	error: function (xhr, ajaxOptions, thrownError) {
			           console.log(xhr.status);
			           console.log(xhr.responseText);
			           console.log(thrownError);
			       	}
			  	});
			}
			
		  	
		});
	</script>
@endsection

<!--  -->