<header class="main-header">
    <!-- Logo -->
    <a class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">پنل</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>کنترل پنل مدیریت</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <span class="label label-success">4</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">۴ پیام خوانده نشده</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <li><!-- start message -->
                                    <a href="#">
                                        <div class="pull-right">
                                            <img src="/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                                        </div>
                                        <h4>
                                            علیرضا
                                            <small><i class="fa fa-clock-o"></i> ۵ دقیقه پیش</small>
                                        </h4>
                                        <p>متن پیام</p>
                                    </a>
                                </li>
                                <!-- end message -->
                                <li>
                                    <a href="#">
                                        <div class="pull-right">
                                            <img src="/dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                                        </div>
                                        <h4>
                                            نگین
                                            <small><i class="fa fa-clock-o"></i> ۲ ساعت پیش</small>
                                        </h4>
                                        <p>متن پیام</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="pull-right">
                                            <img src="/dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                                        </div>
                                        <h4>
                                            نسترن
                                            <small><i class="fa fa-clock-o"></i> امروز</small>
                                        </h4>
                                        <p>متن پیام</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="pull-right">
                                            <img src="/dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                                        </div>
                                        <h4>
                                            نگین
                                            <small><i class="fa fa-clock-o"></i> دیروز</small>
                                        </h4>
                                        <p>متن پیام</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="pull-right">
                                            <img src="/dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                                        </div>
                                        <h4>
                                            نسترن
                                            <small><i class="fa fa-clock-o"></i> ۲ روز پیش</small>
                                        </h4>
                                        <p>متن پیام</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer"><a href="#">نمایش تمام پیام ها</a></li>
                    </ul>
                </li>
                <!-- Notifications: style can be found in dropdown.less -->
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning">{{ $n_seen->count()  }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">{{ $n_seen->count()  }} سفارش جدید</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                @foreach ($n_seen as $val)
                             <li>
                                    <a href="{{ route('orders.show', $val->id ) }}">
                                        <i class="fa fa-shopping-cart text-green"></i>{{ $val->id }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="footer">
                            <form action="{{route('seen_all')}}" method="post">
                                @csrf
                                <button type="submit" class="btn-flat btn btn-default btn-block form-controller" href="{{ route('orders.index') }}">علامت دیده شده برای همه </button>
                            </form>
                        </li>
                    </ul>
                </li>
                <!-- Tasks: style can be found in dropdown.less -->
                <li class="dropdown tasks-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-flag-o"></i>
                        <span class="label label-danger">{{$todayOrders + $todayUsers}}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">امار امروز </li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <li><!-- Task item -->
                                    <a href="{{route('orders.index')}}">
                                        <h3>
                                            سفارش امروز 
                                            <span class="pull-left">{{$todayOrders}}</span>
                                        </h3>
                                        <!-- <div class="progress xs">
                                            <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"
                                                 aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">20% تکمیل شده</span>
                                            </div>
                                        </div> -->
                                    </a>
                                </li>
                                <li><!-- Task item -->
                                    <a href="{{route('users.index')}}">
                                        <h3>
                                            کاربران امروز 
                                            <span class="pull-left">{{$todayUsers}}</span>
                                        </h3>
                                        <!-- <div class="progress xs">
                                            <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"
                                                 aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                <span class="sr-only">20% تکمیل شده</span>
                                            </div>
                                        </div> -->
                                    </a>
                                </li>
                                <!-- end task item -->
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="{{route('users.index')}}">نمایش همه</a>
                        </li>
                    </ul>
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                        <span class="hidden-xs">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                            <p>
                                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                                <small>{{ Auth::user()->roles[0]->dascription}}</small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="row">
                                <div class="col-xs-4 text-center">
                                    <a href="#">صفحه من</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="#">فروش</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="#">دوستان</a>
                                </div>
                            </div>
                            <!-- /.row -->
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-right">
                                <a href="#" class="btn btn-default btn-flat">پروفایل</a>
                            </div>
                            <div class="pull-left">
                                {{--<a href="#" class="btn btn-default btn-flat">خروج</a>--}}
                                <a class="btn btn-default btn-flat" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('خروج') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li class="settings">
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>