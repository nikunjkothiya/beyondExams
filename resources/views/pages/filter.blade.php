@extends('layouts.master')

@section('style')
    <!--Custom Styles-->
    <link rel="stylesheet" href="{{asset('css/filter.css')}}">
    
@endsection

@section('content')
	<div class="filter">
		<div class="container">
			<form method="POST" id="filter" action="{{ url('/save/filter') }}">	
			@csrf
			<div class="row">
				<div class="col">
					<h2>@lang('filter.title')</h2>
					@if ($errors->any())
                      <span class="invalid-feedback" role="alert" style="width: 100%;margin-top: .25rem;font-size: 80%;color: #dc3545;display: block;">
                        <strong>{{ $errors->first() }}</strong>
                      </span>
                    @endif
				</div>
			</div>
			<div class="row">
				<div class="col">
					<div class="title">
						<div class="row">
							<div class="col">
								@lang('filter.tagtitle')
							</div>
						</div>
					</div>
					<div class="content">
						<div class="row">
							@foreach($tags as $tag)
								@if(in_array($tag->id,$utid))
									<a href="#" class="tag tagcl" id="tag{{$tag->id}}">
										<div class="overlay overlay-selected">
											<div class="text">{{ __($tag->tag) }}</div>
											<input type="checkbox" name="tags[]" value="{{$tag->id}}" hidden="hidden" checked="checked" />
										</div>
									</a>
								@else
									<a href="#" class="tag tagcl" id="tag{{$tag->id}}">
										<div class="overlay">
											<div class="text">{{ __($tag->tag) }}</div>
											<input type="checkbox" name="tags[]" value="{{$tag->id}}" hidden="hidden" />
										</div>
									</a>
								@endif
							@endforeach
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<div class="title">
						<div class="row">
							<div class="col">
								@lang('filter.qualificationtitle')
							</div>
						</div>
					</div>
					<div class="content">
						<div class="row">
							@foreach($qualifications as $qualification)
								@if(in_array($qualification->id,$utid))
									<a href="#" class="tag quacl" id="qua{{$qualification->id}}">
										<div class="overlay overlay-selected">
											<div class="text">{{ __($qualification->tag) }}</div>
											<input type="checkbox" name="tags[]" value="{{$qualification->id}}" hidden="hidden" checked="checked" />
										</div>
									</a>
								@else
									<a href="#" class="tag quacl" id="qua{{$qualification->id}}">
										<div class="overlay">
											<div class="text">{{ __($qualification->tag) }}</div>
											<input type="checkbox" name="tags[]" value="{{$qualification->id}}" hidden="hidden" />
										</div>
									</a>
								@endif
							@endforeach					
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<div class="title">
						<div class="row">
							<div class="col">
								@lang('filter.disciplinetitle')
							</div>
						</div>
					</div>
					<div class="content">
						<div class="row">
							@foreach($disciplines as $discipline)
								@if(in_array($discipline->id,$utid))
									<a href="#" class="tag discl" id="dis{{$discipline->id}}">
										<div class="overlay overlay-selected">
											<div class="text">{{ __($discipline->tag) }}</div>
											<input type="checkbox" name="tags[]" value="{{$discipline->id}}" hidden="hidden" checked="checked" />
										</div>
									</a>
								@else
									<a href="#" class="tag discl" id="dis{{$discipline->id}}">
										<div class="overlay">
											<div class="text">{{ __($discipline->tag) }}</div>
											<input type="checkbox" name="tags[]" value="{{$discipline->id}}" hidden="hidden" />
										</div>
									</a>
								@endif
							@endforeach
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<div class="button" align="center">
						<button type="submit" class="btn btn-lg btn-save">@lang('filter.save')</button>
					</div>
				</div>
			</div>
			</form>
		</div>
	</div>
@endsection

@section('script')
	<script type="text/javascript">
		$('.tagcl').click(function(e) {
			e.preventDefault();
  			$(this).find('.overlay').toggleClass('overlay-selected');
  			$check = $(this).find('input').attr('checked');
  			if($check === 'checked')
  				$(this).find('input').attr('checked',false);
  			else	
  				$(this).find('input').attr('checked',true);
		});
		$('.quacl').click(function(e) {
			e.preventDefault();
  			$(this).find('.overlay').toggleClass('overlay-selected');
  			$check = $(this).find('input').attr('checked');
  			if($check === 'checked')
  				$(this).find('input').attr('checked',false);
  			else	
  				$(this).find('input').attr('checked',true);
		});
		$('.discl').click(function(e) {
			e.preventDefault();
  			$(this).find('.overlay').toggleClass('overlay-selected');
  			$check = $(this).find('input').attr('checked');
  			if($check === 'checked')
  				$(this).find('input').attr('checked',false);
  			else	
  				$(this).find('input').attr('checked',true);
		});
	</script>
@endsection
