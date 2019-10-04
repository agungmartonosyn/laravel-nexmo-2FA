@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Verify</div>

                <div class="card-body">
                    @if (session('resend_sms'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>{{ session('resend_sms') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <form class="form-horizontal" role="form" method="POST" action="{{ route('verify') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="code" class="col-md-4 col-form-label text-md-right">Code</label>

                            <div class="col-md-6">
                                <input id="code" type="number" class="form-control @error('code') is-invalid @enderror"
                                    name="code" value="{{ old('code') }}" required>

                                @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Verify Account
                                </button>
                            </div>
                        </div>

                        <br>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Resend OTP</label>

                            <div class="col-md-6">
                                <input id="timer" type="hidden">
                                <a  href="{{ route('resend_sms') }}" class="btn btn-primary" id="download" disabled>
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let timerOn = true;
    var downloadButton = document.getElementById("download");
    downloadButton.innerHTML = "You can Resend in 5 seconds.";
    var id;

    function timer(remaining) {
        var m = Math.floor(remaining / 60);
        var s = remaining % 60;

        m = m < 10 ? '0' + m : m;
        s = s < 10 ? '0' + s : s;
        document.getElementById('timer').innerHTML = m + ':' + s;
        downloadButton.innerHTML = "You can Resend in " + m + ':' + s + " seconds.";
        remaining -= 1;

        if (remaining >= 0 && timerOn) {
            setTimeout(function () {
                timer(remaining);
            }, 1000);
            return;
        }

        if (!timerOn) {
            return;
        }

        downloadButton.innerHTML = "Resend SMS";
        downloadButton.removeAttribute('disabled');
    }

    timer(900);
</script>
@endpush
