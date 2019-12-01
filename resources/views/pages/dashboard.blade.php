@extends('layouts.master')

@section('style')
    <!--Custom Styles-->
    <link rel="stylesheet" href="{{asset('css/dashboard.css')}}">
@endsection

@section('content')
    <script src="{{asset('js/notify.min.js')}}"></script>
    <meta name="_token" content="{{ csrf_token() }}">
    <div class="dashboard">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h2>@lang('dashboard.dashboard')</h2>
                    <hr>
                    <div class="list-group">
                        <a href="{{ url('dashboard') }}"
                           class="list-group-item list-group-item-action active d-flex justify-content-between align-items-center">
                            @lang('dashboard.option1')
                        </a>
                        <a href="{{ url('dashboard/filter') }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            @lang('dashboard.option2')
                        </a>
                        <a href="{{ url('dashboard/profile') }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            @lang('dashboard.option3')
                            @if(!$pcheck)
                                <i class="fa fa-exclamation-circle"></i>
                            @endif
                        </a>
                        <a href="{{ url('dashboard/saved-opp') }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            @lang('dashboard.option4')
                        </a>
                        <a href="{{ url('dashboard/subscription') }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            @lang('dashboard.option5')
                        </a>
                        <a href="{{ url('dashboard/message') }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            @lang('dashboard.option6')
                        </a>
                        <a href="{{ url('logout') }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            @lang('dashboard.option7')
                        </a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="material_card-stack">
                        <button class="material_buttons material_prev" id="prev"><i class="fa fa-arrow-up"></i></button>
                        <div class="material_card-list">
                            @if($opportunities->isEmpty())
                                Oops
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
                                                    <span class="badge badge-danger">@lang('dashboard.deadline')</span>
                                                    : {{ $opportunity->deadline }}
                                                    @if(Auth::user()->saved_opportunities->contains($opportunity->id))
                                                        <div class="col-auto">
                                                            <div class="save">
                                                                <a href="#" value="{{ $opportunity->id }}" title="Save"
                                                                   id="save" class="save-btn"><i id="saveico"
                                                                                                 class="fa fa-star"></i></a>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-auto">
                                                            <div class="save">
                                                                <a href="#" value="{{ $opportunity->id }}" title="Save"
                                                                   id="save" class="save-btn"><i id="saveico"
                                                                                                 class="fa fa-star-o"></i></a>
                                                            </div>
                                                        </div>
                                                    @endif
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
                                                <a href="mailto:?Subject={{$opportunity->title}}&amp;Body={{$opportunity->description}} {{ url('/opportunity/'.$opportunity->id) }}"
                                                   class="btn btn share-btn smaller-font-button">@lang('opportunity.share')</a>
                                            </div>
                                            <div class="col-auto smaller-font-button">
                                                <a href="{{ url('opportunity/'.$opportunity->slug) }}" target="_blank"
                                                   class="btn btn apply-btn smaller-font-button">@lang('dashboard.readmore')</a>
                                            </div>
                                            @if($opportunity->status == 1)
                                                <div class="col-auto ml-auto">
                                                    <a class="btn btn-lg share-btn smaller-font-button">@lang('opportunity.guidance_requested')</a>
                                                </div>
                                            @else
                                                <div class="col-auto ml-auto" value="{{ $opportunity->id }}" id="guidance_request_button">
                                                    <a class="btn btn-lg share-btn smaller-font-button">@lang('opportunity.request_guidance')</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>


                                @endforeach
                            @endif

                        </div>
                        <button class="material_buttons material_next" id="next"><i class="fa fa-arrow-down"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        var user_opps = [@foreach(Auth::user()->saved_opportunities as $p){{ $p->id }},@endforeach];
        var html = null;
        var save = "";
        var guidance = "";
        var tags = "";
        var page = 1;
        var current_page = 0;
        var total_page = 0;
        var id = 0;

        $('.material_next').click(function (e) {
            console.log(user_opps);
            page = page + 1;
            e.preventDefault();
            $('#next').prop("disabled", true);
            var data = {'_token': "{{ csrf_token() }}", 'page': page};
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('/nextopps')}}",
                type: 'GET',
                data: data,
                success: function (response) {
                    console.log(page);
                    console.log(current_page);
                    console.log(total_page);
                    console.log(response);
                    total_page = response.total;
                    if (page >= total_page) {
                        current_page = response.current_page;
                        page = total_page;
                    } else {
                        current_page = response.current_page;

                    }
                    if (response.data.length) {
                        var save = "";
                        var guidance = "";
                        var tags = "";
                        var res = response.data[0];
                        id = res.id;
                        console.log(res.id);
                        if (user_opps.includes(res.id)) {
                            save = '' +
                                '<div class="col-auto">' +
                                '<div class="save">' +
                                '<a href="#" value="' + res.id + '" title="Save" id="save" class="save-btn">' +
                                '<i id="saveico" class="fa fa-star">' +
                                '</i></a></div></div>';
                        } else {
                            save = '' +
                                '<div class="col-auto">' +
                                '<div class="save">' +
                                '<a href="#" value="' + res.id + '" title="Save" id="save" class="save-btn">' +
                                '<i id="saveico" class="fa fa-star-o">' +
                                '</i></a></div></div>';
                        }
                        res.tags.forEach(function (item, index) {
                            tags += '<span class="badge badge-secondary">' + item.tag + '</span>'
                        });

                        if (res.status === 1) {
                            guidance = '<div class="col-auto ml-auto">' +
                                '<a class="btn btn-lg share-btn smaller-font-button">@lang("opportunity.guidance_requested")</a>' +
                                '</div>';
                        } else {
                            guidance = '<div class="col-auto ml-auto" id="guidance_request_button" value="' + res.id + '" >' +
                                '<a class="btn btn-lg share-btn smaller-font-button">@lang("opportunity.request_guidance")</a>' +
                                '</div>';
                        }

                        html = '' +
                            '<div class="card" id="' + res.id + '">' +
                            '<div class="row header">' +
                            '<div class="col-md-4">' +
                            '<img src="' + res.image + '" height="150" width="200">' +
                            '</div>' +
                            '<div class="col-md-8">' +
                            '<div class="title"><h3>' + res.title +
                            '</h3>' +
                            '<span class="badge badge-danger">@lang('dashboard.deadline')</span> : ' +
                            res.deadline + save +
                            '</div></div></div>' +
                            '<br>' +
                            '<div class="row">' +
                            '<div class="col">' +
                            '<p>' + res.description.substring(1, 300) + ' . . .' +
                            '</p></div></div><br>' +
                            '<div class="row">' +
                            '<div class="col">' + tags +
                            '</div>' +
                            '<div class="col-auto ml-auto">' +
                            '<a href="mailto:?Subject=' + res.title + '&amp;Body=' +
                            res.description + ' {{ url('/opportunity/') }}/' + res.id +
                            '" class="btn btn-lg share-btn smaller-font-button">@lang('opportunity.share')' +
                            '</a></div>' +
                            '<div class="col-auto">' +
                            '<a href="{{ url('/opportunity/') }}/' + res.slug +
                            '" target="_blank" class="btn btn-lg apply-btn smaller-font-button">@lang('dashboard.readmore')' +
                            '</a></div>' +
                            guidance +
                            '</div>';
                    }
                    $('.material_card-list').fadeOut(function () {
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

        $('.material_prev').click(function (e) {
            console.log(user_opps);
            page = page - 1;
            e.preventDefault();
            $('#prev').prop("disabled", true);
            var data = {'_token': "{{ csrf_token() }}", 'page': page};
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('/nextopps')}}",
                type: 'GET',
                data: data,
                success: function (response) {
                    console.log(page);
                    console.log(current_page);
                    console.log(total_page);
                    console.log(response);
                    total_page = response.total;
                    if (page <= 1) {
                        current_page = response.current_page;
                        page = current_page;
                    } else {
                        current_page = response.current_page;

                    }
                    if (response.data.length) {
                        var tags = "";
                        var guidance = "";
                        var res = response.data[0];
                        console.log(res);
                        id = res.id;
                        res.tags.forEach(function (item, index) {
                            tags += ' <span class="badge badge-secondary">' + item.tag + '</span> '
                        });
                        if (user_opps.includes(res.id)) {
                            save = '<div class="col-auto">' +
                                '<div class="save">' +
                                '<a href="#" value="' + res.id + '" title="Save" id="save" class="save-btn">' +
                                '<i id="saveico" class="fa fa-star">' +
                                '</i></a></div></div>';
                        } else {
                            save = '<div class="col-auto"><div class="save"><a href="#" value="' + res.id + '" title="Save" id="save" class="save-btn"><i id="saveico" class="fa fa-star-o"></i></a></div></div>';
                        }

                        if (res.status === 1) {
                            guidance = '<div class="col-auto ml-auto">' +
                                '<a class="btn btn-lg share-btn smaller-font-button">@lang("opportunity.guidance_requested")</a>' +
                                '</div>';
                        } else {
                            guidance = '<div class="col-auto ml-auto" id="guidance_request_button" value="' + res.id + '" >' +
                                '<a class="btn btn-lg share-btn smaller-font-button">@lang("opportunity.request_guidance")</a>' +
                                '</div>';
                        }

                        html = '<div class="card" id="' + res.id + '">' +
                            '<div class="row header">' +
                            '<div class="col-md-4">' +
                            '<img src="' + res.image + '" height="150" width="200">' +
                            '</div>' +
                            '<div class="col-md-8">' +
                            '<div class="title">' +
                            '<h3>' + res.title + '</h3>' +
                            '<span class="badge badge-danger">@lang('dashboard.deadline')</span> : ' +
                            res.deadline + save +
                            '</div></div></div>' +
                            '<br>' +
                            '<div class="row">' +
                            '<div class="col">' +
                            '<p>' + res.description.substring(1, 300) + ' . . .' +
                            '</p></div></div>' +
                            '<br>' +
                            '<div class="row">' +
                            '<div class="col">' + tags +
                            '</div><div class="col-auto ml-auto">' +
                            '<a href="mailto:?Subject=' + res.title + '&amp;Body=' + res.description + ' {{ url('/opportunity/') }}/' + res.id + '" class="btn btn-lg share-btn smaller-font-button">@lang('opportunity.share')</a></div>' +
                            '<div class="col-auto">' +
                            '<a href="{{ url('/opportunity/') }}/' + res.slug + '" target="_blank" class="btn btn-lg apply-btn smaller-font-button">@lang('dashboard.readmore')</a></div>' +
                            guidance + '</div>';

                        console.log(guidance);
                    }
                    $('.material_card-list').fadeOut(function () {
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

        $(document).on('click', '#guidance_request_button', function (e) {
            e.preventDefault();

            console.log("Clicked for guidance");
            console.log("Guidance requested for: ");

            var data = {'_token': "{{ csrf_token() }}", 'id': $(this).attr('value')};

            console.log(data);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ url('/opportunity/request_guidance')}}",
                type: 'POST',
                data: data,
                beforeSend: function () {
                    $('#guidance_request_button').addClass('btn--loading');
                },
                complete: function () {
                    $('#guidance_request_button').removeClass('btn--loading');
                },
                success: function (response) {
                    if (response.status_code == '200') {
                        console.log("GUIDANCE SUCCEEDED!");
                        // console.log(response.data);
                        document.getElementById("guidance_request_button").getElementsByTagName('a')[0].textContent = "Guidance requested";
                        $.notify("Guidance requested!", "success");
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(xhr.responseText);
                    console.log(thrownError);
                }
            });
        });

        $(document).on('click', '#save', function (e) {
            console.log("save btn pressed!");
            e.preventDefault();
            var data = {'_token': "{{ csrf_token() }}", 'id': $(this).attr('value')};
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            if ($("#saveico").hasClass("fa-star-o")) {
                console.log("save");
                $.ajax({
                    url: "{{ url('/opportunity/save')}}",
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        if (response.status_code == '200') {
                            $("#saveico").attr('class', 'fa fa-star');
                            $("#save").attr('title', 'unSave');
                            console.log(user_opps);
                            user_opps.push(parseInt($("#save").attr('value')));
                            console.log(user_opps);
                            $.notify("Saved!", "success");
                            save = '<div class="col-auto"><div class="save"><a href="#" value="' + $("#save").attr('value') + '" title="Save" id="save" class="save-btn"><i id="saveico" class="fa fa-star"></i></a></div></div>';
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr.status);
                        console.log(xhr.responseText);
                        console.log(thrownError);
                    }
                });
            } else {
                console.log("unsave");
                $.ajax({
                    url: "{{ url('/opportunity/unsave')}}",
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        if (response.status_code == '200') {
                            $("#saveico").attr('class', 'fa fa-star-o');
                            $("#save").attr('title', 'Save');
                            console.log(user_opps);
                            var index = user_opps.indexOf(parseInt($("#save").attr('value')));
                            if (index !== -1) user_opps.splice(index, 1);
                            console.log(user_opps);
                            $.notify("Removed!", "success");
                            save = '<div class="col-auto"><div class="save"><a href="#" value="' + $("#save").attr('value') + '" title="Save" id="save" class="save-btn"><i id="saveico" class="fa fa-star-o"></i></a></div></div>';
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
