<!-- Top Bar -->
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
            <a href="javascript:void(0);" class="bars"></a>
           
            <div class="user">
              <div class="photo">
                 <a class="navbar-brand" href="{{ url('/admin/dashboard') }}">
                    <?php
                    $site_title = getSetting('site_title','site_settings', 'LaraOffice');
                    $site_logo = getSetting('site_logo','site_settings');
                    $imageObject = new \App\ImageSettings();          
                    $destinationPath      = public_path() . $imageObject->getSettingsImagePath();
                    ?>
                    @if ( ! empty( $site_logo ) && file_exists($destinationPath.$site_logo))
                    <img src="{{IMAGE_PATH_SETTINGS.$site_logo}}" alt="{{$site_title}}" title="{{$site_title}}">
                    @else
                    <img src="{{asset('images/logo3.png')}}" alt="{{$site_title}}" title="{{$site_title}}">
                    @endif
                </a>
              </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <!-- Call Search -->
                <li><a href="javascript:void(0);" class="js-search" data-close="true"><i class="material-icons">search</i></a></li>
                <!-- #END# Call Search -->
                <!-- Notifications -->
                <li class="dropdown notifications-menu">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                        <i class="material-icons">notifications</i>
                        @php($notificationCount = \Auth::user()->internalNotifications()->where('read_at', null)->count())
                        <span class="label-count label-warning">{{ $notificationCount }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">NOTIFICATIONS</li>
                        <li class="body">
                            <ul class="menu">
                                @if (count($notifications = \Auth::user()->internalNotifications()->where('read_at', null)->get()) > 0)
                                    @foreach($notifications as $notification)
                                        <li class="notification-link {{ $notification->pivot->read_at === null ? "unread" : false }}">
                                            <a href="{{ $notification->link ? $notification->link : "#" }}"
                                               class="{{ $notification->link ? 'is-link' : false }}">
                                                <div class="menu-info">
                                                    <h4>{!! clean($notification->text) !!}</h4>                                                    
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="notification-link" style="text-align:center;">
                                        No notifications
                                    </li>
                                @endif
                            </ul>
                        </li>                        
                    </ul>
                </li>
                <!-- #END# Notifications -->
                <!-- Tasks -->
                <?php
                storeLanguages(); 
                $languages = \App\Language::all();
                $languages_arr = array();
                ?>
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                        <i class="material-icons">flag</i>
                        <span class="label-count">{{ strtoupper(\App::getLocale()) }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">Languages</li>
                        <li class="body">
                            <ul class="menu tasks">
                               @foreach($languages as $language)
                                    <?php                            
                                    $languages_arr[ $language->code ] = $language->language;
                                    ?>
                                    <li>
                                        <a href="{{ route('admin.language', $language->code) }}">
                                            <h4>{{ $language->language }} ({{ strtoupper($language->code) }})</h4>
                                        </a>
                                    </li>
                                @endforeach   
                                <?php
                                config('app.languages', $languages_arr);
                                ?>                             
                            </ul>
                        </li>                        
                    </ul>
                </li>
                <?php
                $userprofileplacement = getActiveTheme('user-profile-placement', 'Topbar');
                ?>
                <?php
                $contact = \App\Contact::where( 'id', '=', getContactId() )->first();
                if ( $contact ) {
                    $name = $contact->first_name . ' ' . $contact->last_name;
                    $image = '';
                    if ($contact->thumbnail && file_exists(public_path().'/thumb/' . $contact->thumbnail)) {
                        $image = asset(env('UPLOAD_PATH').'/thumb/'.$contact->thumbnail);
                    }
                } else {                    
                    $name = Auth::user()->name;
                    $image = '';
                }

                $name = substr($name, 0, 2); // Let us display first 2 characters only for the sake for design!
                ?>
                <li class="dropdown">
                  <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                    <i class="material-icons" title="{{$name}}">person</i>
                    <span class="label-count">{{ $name }}</span>
                  </a>
                  <ul class="dropdown-menu">
                        <li class="header">Profile</li>
                        <li class="body">
                            <ul class="menu tasks">                               
                                <li>
                                    <a class="dropdown-item" href="{{route('admin.contacts.profile.edit')}}"><i class="material-icons">person</i>Profile</a>
                                </li>
                                <li>
                                    <a href="#logout" onclick="$('#logout').submit();" class="dropdown-item">
                                        <i class="fa fa-arrow-left"></i>
                                        <span class="title">@lang('global.app_logout')</span>
                                    </a>
                                </li>                                                           
                            </ul>
                        </li>                        
                    </ul>
                  
                </li>
                <!-- #END# Tasks -->
                <!--<li class="pull-right"><a href="javascript:void(0);" class="js-right-sidebar" data-close="true"><i class="material-icons">more_vert</i></a></li>-->
            </ul>
        </div>
    </div>
</nav>
<!-- #Top Bar -->