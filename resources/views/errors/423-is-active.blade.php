@extends('errors.minimal')

@section('title', __('Locked'))
@section('code', '423')
@section('message')

{{$message}}

<script>

    setTimeout(function(){
        location.reload();
    },5000)

</script>
@endsection
