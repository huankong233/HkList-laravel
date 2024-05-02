<meta name="app_name" content="{{config('app.name')}}"/>
<title>{{config('app.name')}}</title>
@php
    try {
        echo \Illuminate\Support\Facades\File::get(public_path('index.html'));
    }catch (Exception $e){
        echo '';
    }
@endphp