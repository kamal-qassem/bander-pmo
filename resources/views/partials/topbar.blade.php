<header class="main-header">

    <!-- Header Navbar: style can be found in header.less -->

    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <!-- logo for regular state and mobile devices -->
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>


        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell-o"></i>
                        @php($notificationCount = \Auth::user()->internalNotifications()->where('read_at', null)->count())
                        @if($notificationCount > 0)
                            <span class="label label-warning">
                            {{ $notificationCount }}
                        </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div class="slimScrollDiv"
                                 style="position: relative;">
                                <ul class="menu notification-menu">
                                    @if (count($notifications = \Auth::user()->internalNotifications()->get()) > 0)
                                        @foreach($notifications as $notification)
                                            <li class="notification-link {{ $notification->pivot->read_at === null ? "unread" : false }}">
                                                <a href="{{ $notification->link ? $notification->link : "#" }}"
                                                   class="{{ $notification->link ? 'is-link' : false }}">
                                                    {{ $notification->text }}
                                                </a>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="notification-link" style="text-align:center;">
                                            @lang('custom.topbar.no-notifications')
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    </ul>
                </li>

                <?php
                $user = Auth::user();
                $name = $user->name;
                $image = '';
                if ($user->thumbnail && file_exists(public_path().'/thumb/' . $user->thumbnail)) {
                    $image = asset(env('UPLOAD_PATH').'/thumb/'.$user->thumbnail);
                }
                ?>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                      @if ( ! empty( $image ) )
                      <img src="{{$image}}" class="user-image" alt="{{$name}}">
                      @endif
                      <span class="hidden-xs">{{$name}}</span>
                    </a>
                    <ul class="dropdown-menu">
                      <!-- User image -->
                      <li class="user-header">
                        @if ( ! empty( $image ) )
                        <img src="{{$image}}" class="img-circle" alt="{{$name}}">
                        @endif

                        <p>{{$name}}
                          <small>@lang('custom.topbar.last-login'){{digiDate( Auth::user()->updated_at, true )}}
                            @if( ! empty( Auth::user()->last_login_from ) ) <br> @lang('custom.topbar.login-from'){{ Auth::user()->last_login_from }} @endif</small>
                        </p>
                      </li>

                      <!-- Menu Footer-->
                      <li class="user-footer">
                        <div class="pull-left">
                          <a href="{{route('admin.contacts.profile.edit')}}" class="btn btn-success btn-flat">@lang('custom.topbar.profile')</a>
                        </div>
                        <div class="pull-right">
                          <a href="#logout" onclick="$('#logout').submit();" class="btn btn-warning btn-flat">
                            <i class="fa fa-arrow-left"></i>
                            <span class="title">@lang('global.app_logout')</span>
                        </a>
                        </div>
                      </li>
                    </ul>
                  </li>

            </ul>
        </div>
        <?php
        $languages = getLanguages();
        $languages_arr = array();
        ?>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown languages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        {{ strtoupper(\App::getLocale()) }}
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header"></li>
                        <ul class="menu language-menu">
                            @foreach($languages as $language)
                            <?php
                            $languages_arr[ $language->code ] = $language->language;
                            ?>
                                <li class="language-link">
                                    <a href="{{ route('admin.language', $language->code) }}">
                                        {{ $language->language }} ({{ strtoupper($language->code) }})
                                    </a>
                                </li>
                            @endforeach
                            <?php
                            config('app.languages', $languages_arr);
                            ?>
                        </ul>
                        <li class="footer"></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>


    </nav>
</header>
