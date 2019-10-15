<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-right image">
                <img src="/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-right info">
                <p>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                <a href="#"><i class="fa fa-circle-o text-success"></i> آنلاین</a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input id="aside_search" type="text" name="q" autocomplete="off" class="form-control"
                       placeholder="جستجو">
                <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search "></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" id="aside_list" data-widget="tree">
            @if(Auth::user()->roles()->first()->name == "manager")
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="fa fa-circle-o "></i> <span>داشبرد</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="/admin/orders">
                        <i class="fa fa-circle-o "></i> <span>سفارشات</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>

                <li>
                    <a href="/admin/users">
                        <i class="fa fa-circle-o "></i> <span>مشتریان</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="/admin/admins">
                        <i class="fa fa-circle-o "></i> <span>کاربران پنل</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('workers') }}">
                        <i class="fa fa-circle-o "></i> <span>کارگر ها</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('drivers') }}">
                        <i class="fa fa-circle-o "></i> <span> راننده ها </span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('service.index') }}">
                        <i class="fa fa-circle-o "></i> <span> سرویس ها </span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('notifications.index') }}">
                        <i class="fa fa-circle-o "></i> <span> اعلانها </span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('area.index') }}">
                        <i class="fa fa-circle-o "></i> <span> محدوده فعالیت </span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="/admin/transaction">
                        <i class="fa fa-circle-o "></i> <span>حسابداری</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('errors') }}">
                        <i class="fa fa-circle-o "></i> <span>خطاها</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>

                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-circle-o "></i> <span>شرکاء تجاری</span>
                        <span class="pull-left-container">
                        <i class="fa fa-angle-right pull-left"></i>
                    </span>
                    </a>
                    <ul class="treeview-menu" style="display: none;">
                        @foreach ($third as $val)
                            <li><a href="{{route('thirdparty.show',$val->id)}}"><i class="fa fa-circle-o -o">

                                    </i>{{ $val->user->first_name . ' '. $val->user->last_name }}
                                </a></li>
                        @endforeach
                            <li><a href="{{ route('thirdparty.create') }}"><i class="fa fa-circle-o -o"></i>افزودن</a></li>
                    </ul>
                </li>
                <li>
                    <a href="/admin/discount">
                        <i class="fa fa-circle-o "></i> <span>کد تخفیف</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('nobaarAccounting') }}">
                        <i class="fa fa-circle-o "></i> <span>حسابداری نوبار</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
            @elseif(Auth::user()->roles()->first()->name == "supporter")
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="fa fa-circle-o "></i> <span>داشبرد</span>
                        <span class="pull-left-container">
            </span>
                    </a>
                </li>
                <li>
                    <a href="/admin/orders">
                        <i class="fa fa-circle-o "></i> <span>سفارشات</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>

                <li>
                    <a href="/admin/users">
                        <i class="fa fa-circle-o "></i> <span>کاربران</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('workers') }}">
                        <i class="fa fa-circle-o "></i> <span>کارگر ها</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('drivers') }}">
                        <i class="fa fa-circle-o "></i> <span> راننده ها </span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>

                <li>
                    <a href="/admin/discount">
                        <i class="fa fa-circle-o "></i> <span>کد تخفیف</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
            @elseif(Auth::user()->roles()->first()->name == "observer")
                <li>
                    <a href="/admin/orders">
                        <i class="fa fa-circle-o "></i> <span>سفارشات</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('workers') }}">
                        <i class="fa fa-circle-o "></i> <span>کارگر ها</span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('drivers') }}">
                        <i class="fa fa-circle-o "></i> <span> راننده ها </span>
                        <span class="pull-left-container"></span>
                    </a>
                </li>
            @endif


        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
