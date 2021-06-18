@extends('errors.minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message')

{{$message}}

<script>

    setTimeout(function(){
        location.reload();
    },2000)
</script>
@endsection
