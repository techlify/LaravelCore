@extends('email.layouts.app')

@section('title', 'Welcome to eBusiness')

@section('content')
        <tr>
            <td class="container">

                <table>
                    <tr>
                        <td class="padding-high">
                            <div class="line"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="line-space"></td>
                    </tr>
                    <tr>
                        <td class="padding-low">
                            <div class="line"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="line-space"></td>
                    </tr>
                    <tr>
                        <td align="center" class="content">

                            <h3>Hi, {{ $client->name }}!</h3>

                        </td>
                    </tr>
                    <tr>
                        <td class="content" align="center">
                            <p>Welcome to our Business Management Platform. </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" class="content">
                            <h5 style="margin-top: 0px;margin-bottom: 20px;">We've Different modules to use</h5>
                            @foreach ($modules as $module)
                                <p>{{ $module->name}}</p>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td align="center" class="content">
                            <h5 style="margin-top: 0px;margin-bottom: 30px;">Introduction about our App!</h5>
                            <a href="{{env('INTRO_VIDEO_URL')}}" target="_blank">
                                <img src="{{env('INTRO_VIDEO_THUMNAIL_URL')}}" height="250px" width="400px">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" class="content">

                            <h5>Please reply to this email if you have any questions. Welcome!</h5>

                        </td>
                    </tr>
                </table>

            </td>
        </tr>
@endsection
