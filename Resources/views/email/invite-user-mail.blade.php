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
                    <h2>Welcome to eBusiness</h2>
                </td>
            </tr>
            <tr>
                <td class="content" align="center">
                    <p>You have been invited to eBusiness.gy by someone from {{ $user->client->name }}. </p>
                    <p>Please login using the credentials below. You are required to change your password at login.</p>
                    <p>
                        Email: {{ $user->email }} <br />
                        Password: {{ $user->original_password }} <br />

                    </p>
                </td>
            </tr>
            <tr>
                <td class="content" align="center">
                    <p>You have been added to {{ $module['name'] }} module.</p>
                </td>
            </tr>
            <tr>
                <td class="content" align="center">
                    <h4 style="margin-bottom:20px;">Here is your Role Informations.</h4>
                        @foreach ($roles as $role)
                        <ul style="list-style-type: none;">
                            <li>
                                <h6> {{ $role->label }}</h6>
                            </li>
                            <li>
                                {{ $role->description }}
                            </li>
                        </ul>
                        @endforeach
                </td>
            </tr>
            @if ($module['video_url'] != null)
                <tr>
                    <td align="center" class="content">
                        <h5 style="margin-top: 0px;margin-bottom: 30px;">Introduction about {{ $module['name'] }} module!</h5>
                        <a href="{{$module['video_url']}}" target="_blank">
                            <img src="{{env('INTRO_VIDEO_THUMNAIL_URL')}}" height="250px" width="400px">
                        </a>
                    </td>
                </tr>
            @endif
            <tr>
                <td align="center" class="content">

                    <h4>Thanks and welcome!</h4>

                </td>
            </tr>
        </table>

    </td>
</tr>
@endsection