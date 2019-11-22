@extends('layouts.master')

@section('style')
    <!--Custom Styles-->
    <link rel="stylesheet" href="{{asset('css/saved-opp.css')}}">
@endsection

@section('content')
	<script src="{{asset('js/notify.min.js')}}"></script>
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
					  	<a href="{{ url('dashboard/message') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
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
							@if($opportunities->isEmpty())
								
							@else
								@foreach($opportunities as $opportunity)
									<div class="card" id="{{$opportunity->id}}">
										<div class="row header">
											<div class="col-md-4">
												<img src="{{ $opportunity->image }}" height="150" width="200">
											</div>
											<div class="col-md-8">
												<div class="title">
													<h3>{{ $opportunity->title }}</h3>
													<span class="badge badge-danger">@lang('dashboard.deadline')</span> : {{ $opportunity->deadline }}
													<div class="col">
														<div class="delete">
															<a href="#" value="{{ $opportunity->id }}" title="Delete" id="delete" class="btn btn-danger btn-sm">Delete</a>
														</div>
													</div>
													
												</div>
											</div>
										</div>
										<br>
										<div class="row">
											<div class="col">
												<p>{{ $opportunity->description }}</p>
											</div>
										</div>
										
										<div class="row">
											<div class="col">
												@foreach($opportunity->tags as $tag)
													<span class="badge badge-secondary">{{ $tag->tag }}</span>
												@endforeach
											</div>
											<div class="col-auto ml-auto">
												<a href="mailto:?Subject={{$opportunity->title}}&amp;Body={{$opportunity->description}} {{ url('/opportunity/'.$opportunity->id) }}" class="btn btn-lg share-btn">@lang('opportunity.share')</a>
											</div>
											<div class="col-auto">
												<a href="{{ url('opportunity/'.$opportunity->slug) }}" target="_blank" class="btn btn-lg apply-btn">@lang('dashboard.readmore')</a>
											</div>
										</div>
									</div>
								@endforeach
							@endif
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
		var html=null;
		var page = 1;
		var current_page = 0;
		var total_page = 0;
		var id=0;
		$('.material_next').click(function(e){
			page=page+1;
			e.preventDefault();
			$('#next').prop("disabled", true);
			var data = {'_token': "{{ csrf_token() }}",'page':page};
		 	$.ajaxSetup({
		    	headers: {
		      		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
		    	}
		  	});
		  	$.ajax({
			    url: "{{ url('/nextsavedopps')}}",
			    type: 'GET',
			    data: data,
			    success: function(response) {
			    	console.log(page);
			    	console.log(current_page);
			    	console.log(total_page);
			    	console.log(response);
			    	total_page = response.total;
			    	if(page>=total_page){
			    		current_page = response.current_page;
			    		page = total_page;
			    	}
			    	else{
			    		current_page = response.current_page;
			    		
			    	}
			    	if(response.data.length){
			    		var save = "";
			    		var tags="";
				    	var res = response.data[0];
				    	id = res.id;
				    	save = '<div class="col"><div class="delete"><a href="#" value="'+res.id+'" title="Delete" id="delete" class="btn btn-danger btn-sm">Delete</a></div></div>';
				    	res.tags.forEach(function(item, index){
				    		tags+=' <span class="badge badge-secondary">'+item.tag+'</span> ';
				    	});
				    	html = '<div class="card" id="'+res.id+'"><div class="row header"><div class="col-md-4"><img src="'+res.image+'" height="150" width="200"></div><div class="col-md-8"><div class="title"><h3>'+res.title+'</h3><span class="badge badge-danger">@lang('dashboard.deadline')</span> : '+res.deadline+save+'</div></div></div><br><div class="row"><div class="col"><p>'+res.description.substring(1, 300)+' . . .</p></div></div><br><div class="row"><div class="col">'+tags+'</div><div class="col-auto ml-auto"><a href="mailto:?Subject='+res.title+'&amp;Body='+res.description+' {{ url('/opportunity/') }}/'+res.id+'" class="btn btn-lg share-btn">@lang('opportunity.share')</a></div><div class="col-auto"><a href="{{ url('/opportunity/') }}/'+res.slug+'" target="_blank" class="btn btn-lg apply-btn">@lang('dashboard.readmore')</a></div></div>';
			    	}
			    	$('.material_card-list').fadeOut(function(){
			    		$('.material_card-list').html(html);
			    		$('.material_card-list').fadeIn('fast');
			    	});
			    	$('#next').prop("disabled", false);
			   	},
			    error: function (xhr, ajaxOptions, thrownError) {
			        console.log(xhr.status);
			        console.log(xhr.responseText);
			        console.log(thrownError);
			    }
			});
			
		});

		$('.material_prev').click(function(e) {
			page=page-1;
			e.preventDefault();
			$('#prev').prop("disabled", true);
			var data = {'_token': "{{ csrf_token() }}",'page':page};
		 	$.ajaxSetup({
		    	headers: {
		      		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
		    	}
		  	});
		  	$.ajax({
			    url: "{{ url('/nextsavedopps')}}",
			    type: 'GET',
			    data: data,
			    success: function(response) {
			    	console.log(page);
			    	console.log(current_page);
			    	console.log(total_page);
			    	console.log(response);
			    	total_page = response.total;
			    	if(page<=1){
			    		current_page = response.current_page;
			    		page = current_page;
			    	}
			    	else{
			    		current_page = response.current_page;
			    		
			    	}
			    	if(response.data.length){
				    	var tags="";
				    	var res = response.data[0];
				    	id = res.id;
				    	res.tags.forEach(function(item, index){
				    		tags+=' <span class="badge badge-secondary">'+item.tag+'</span> ';
				    	});
				    	save = '<div class="col"><div class="delete"><a href="#" value="'+res.id+'" title="Delete" id="delete" class="btn btn-danger btn-sm">Delete</a></div></div>';
				    	html = '<div class="card" id="'+res.id+'"><div class="row header"><div class="col-md-4"><img src="'+res.image+'" height="150" width="200"></div><div class="col-md-8"><div class="title"><h3>'+res.title+'</h3><span class="badge badge-danger">@lang('dashboard.deadline')</span> : '+res.deadline+save+'</div></div></div><br><div class="row"><div class="col"><p>'+res.description.substring(1, 300)+' . . .</p></div></div><br><div class="row"><div class="col">'+tags+'</div><div class="col-auto ml-auto"><a href="mailto:?Subject='+res.title+'&amp;Body='+res.description+' {{ url('/opportunity/') }}/'+res.id+'" class="btn btn-lg share-btn">@lang('opportunity.share')</a></div><div class="col-auto"><a href="{{ url('/opportunity/') }}/'+res.slug+'" target="_blank" class="btn btn-lg apply-btn">@lang('dashboard.readmore')</a></div></div>';
			    	}
			    	$('.material_card-list').fadeOut(function(){
			    		$('.material_card-list').html(html);
			    		$('.material_card-list').fadeIn('fast');
			    	});
			    	$('#prev').prop("disabled", false);
			   	},
			    error: function (xhr, ajaxOptions, thrownError) {
			        console.log(xhr.status);
			        console.log(xhr.responseText);
			        console.log(thrownError);
			    }
			});
		});
		$(document).on('click', '#delete',function(e){
			e.preventDefault(); 
			var data = {'_token': "{{ csrf_token() }}",'id':$(this).attr('value') };
		 	$.ajaxSetup({
		    	headers: {
		      		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
		    	}
		  	});
		  	$.ajax({
			    url: "{{ url('/opportunity/unsave')}}",
			    type: 'POST',
			    data: data,
			    success: function(response) {
			    	if(response.status_code == '200'){
			       		$.notify("Removed!", "success");
			       		html=null;
						$('.material_card-list').fadeOut(function(){
			    			$('.material_card-list').html("");
			    		});
			    		$('.material_prev').click();
			    	}
			   	},
			    error: function (xhr, ajaxOptions, thrownError) {
			        console.log(xhr.status);
			        console.log(xhr.responseText);
			        console.log(thrownError);
			    }
			});
		});

	</script>
@endsection