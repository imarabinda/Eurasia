@extends('auth.layout.index')

@section('content')

        
<div class="container">
    <div class="row justify-content-center text-center">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">

                   
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                   
                    
                    <div class="pt-3">
                        
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-info waves-effect waves-light align-baseline">{{ __('Click here to request another') }}</button>.
                    </form>

                    <div class="pt-3">

                        <a class="dropdown-item text-danger"  href="#"
                        onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();"><i class="mdi mdi-power text-danger"></i>
                                                Logout
                                            </a>
                                            
                                            <form id="logout-form" action="{{route('logout')}}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                            
                                    </div>
                                            
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection
