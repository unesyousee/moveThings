
<aside class="control-sidebar control-sidebar-dark">

{{--<aside class="control-sidebar control-sidebar-dark control-sidebar-open">--}}
<!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-gear"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab" disabled><i class="fa fa-dollar"></i></a></li>
        @if(Auth::user()->roles()->first()->name == "manager")<li><a href="#control-sidebar-user-tab" data-toggle="tab" disabled><i class="fa fa-user"></i></a></li>@endif
    </ul>
<!-- Tab panes -->
    <div class="tab-content ">
    <!-- Home tab content -->
     <div class="tab-pane active" id="control-sidebar-home-tab">
            <div class="setting-header">
                <h3 class="control-sidebar-heading"> اقلام هزینه‌بر</h3>
                <button type="button" class="btn btn-info btn-lg new_user" data-toggle="modal" data-target="#heavy_modal">افزودن</button>

            <!-- Modal -->
            </div>

            <ul class="control-sidebar-menu">
            @foreach( $heavy as $val)
                    <li >
                        <a class="setting_heavy" href="javascript:void(0)">
                            <i class="menu-icon fa fa-arrows-alt bg-red"></i>
                            <div class="menu-info ">
                                <h4 class="control-sidebar-subheading">{{ $val->name }}</h4>

                                <p>
                            <?php

                                    $date = $val->updated_at;
                                if ($date){
                                $time= isset(explode(' ',$date)[1])? explode(' ',$date)[1] : '';
                                list($gy,$gm,$gd)=explode('-',$date);
                                $gd=explode(' ',$gd)[0];
                                $j_date_array=gregorian_to_jalali($gy,$gm,$gd);
                                echo $j_date_array[0]. ','. $j_date_array[1].','.$j_date_array[2] .' '.$time ;}
                                else{
                                echo 'بدون تاریخ';
                                }
                                ?>
                                <form class="heavy_destroy" action="{{route('heavythings.destroy',$val->id)}}" method="post" class="user_delete">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}
                                    <label>
                                        <button name="delete" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                    </label>
                                </form>
                                </p>
                            </div>
                        </a>
                        <div class="heavy-form">
                            <form action="{{route('heavythings.update',$val->id)}}" method="post">
                                {{csrf_field()}}
                                {{method_field('put')}}
                                <input type="text" name="price" value="{{$val->price}}">
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    <!-- /.tab-pane -->
    <!-- Settings tab content -->
        <div class="tab-pane" id="control-sidebar-settings-tab">
            <div class="setting-header">
                <h3 class="control-sidebar-heading">هزینه ها</h3>
                <button type="button" class="btn btn-info btn-lg new_user" data-toggle="modal" data-target="#price_modal">افزودن</button>

                <!-- Modal -->
            </div>
            <ul class="control-sidebar-menu">
                @foreach($prices as $price)

                    <li >
                        <a class="setting_heavy" href="javascript:void(0)">
                            <i class="menu-icon fa fa-arrows-alt bg-red"></i>
                            <div class="menu-info ">
                                <h4 class="control-sidebar-subheading">{{ $price->title }}</h4>

                                <p>
                                <?php

                                $date = $price->updated_at;
                                if ($date){
                                    $time= isset(explode(' ',$date)[1])? explode(' ',$date)[1] : '';
                                    list($gy,$gm,$gd)=explode('-',$date);
                                    $gd=explode(' ',$gd)[0];
                                    $j_date_array=gregorian_to_jalali($gy,$gm,$gd);
                                    echo $j_date_array[0]. ','. $j_date_array[1].','.$j_date_array[2] .' '.$time ;}
                                else{
                                    echo 'بدون تاریخ';
                                }
                                ?>
                                <form class="heavy_destroy" action="{{route('prices.destroy',$price->id)}}" method="post" class="user_delete">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}
                                    <label>
                                        <button name="delete" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                    </label>
                                </form>
                                </p>
                            </div>
                        </a>
                        <div class="heavy-form">
                            <form action="{{route('prices.update',$price->id)}}" method="post">
                                {{csrf_field()}}
                                {{method_field('put')}}
                                <input type="text" name="amount" value="{{$price->amount}}">
                            </form>
                        </div>
                    </li>
                    @endforeach
            </ul>
        </div>
        <!-- Settings tab content -->
            @if(Auth::user()->roles()->first()->name == "manager")
        <div class="tab-pane" id="control-sidebar-user-tab">

            <div class="setting-header">
                <h3 class="control-sidebar-heading">کاربران پنل </h3>
                <button type="button" class="btn btn-info btn-lg new_user" data-toggle="modal" data-target="#admin-modal">افزودن</button>

                <!-- Modal -->
            </div>
            <ul class="control-sidebar-menu">
                @foreach ($roles as $role)
                    <li class="panelAminList">
                        <div class="setting_heavy panel_admins" href="javascript:void(0)">
                            <i class="menu-icon fa fa-user bg-red position-relative"><span class="label position-absolute label-success">{{ count($role->users)}}</span></i>
                            <div class="menu-info ">
                                <h4 class="admins control-sidebar-subheading">{{ $role->description}}
                                </h4>
                                <div class="members">
                                    @foreach ($role->users as $user)
                                        <div class="panel_user">
                                            <p style="padding: 3px 10px;">
                                                {{$user->first_name}} {{$user->last_name}}
                                            </p>
                                            <form class="admin_destroy" action="{{route('adminDestroy',$role->id)}}"
                                                  method="post" class="user_delete">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                                <label>
                                                    <button name="delete" style="display: none;"></button><span class="btn fa fa-remove"></span>
                                                </label>
                                            </form>
                                        </div>
                                    @endforeach
                                    <p>
                                </div>

                                </p>
                            </div>
                        </div>
                    </li>



                    </li>
                @endforeach
            </ul>
        </div>
            @endif
        <!-- /.user tab content -->
    <!-- /.tab-pane -->
    </div>

</aside>
<div id="heavy_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">وسیله جدید</h4>
            </div>
            <div class="modal-body">

                <div class="box box-primary">
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form role="form" action="{{route('heavythings.store')}}" method="post">
                        {{csrf_field()}}
                        <div class="box-body">
                            <input type="hidden" name="status" value="1">
                            <div class="form-group">
                                <label for="phone">نام<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="نام">
                            </div>
                            <div class="form-group">
                                <label for="phone">قیمت(حتما از کیبورد انگلیسی استفاده نمیایید)<span class="text-danger">*</span></label>
                                <input type="text" name="price" class="form-control" id="price" placeholder="قیمت">
                            </div>

                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary submit_user">ثبت</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<div id="price_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">هزینه جدید</h4>
            </div>
            <div class="modal-body">

                <div class="box box-primary">
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form role="form" action="{{route('prices.store')}}" method="post">
                        {{csrf_field()}}
                        <div class="box-body">
                            <input type="hidden" name="status" value="1">
                            <div class="form-group">
                                <label for="phone">نام<span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" id="name" placeholder="نام">
                            </div>
                            <div class="form-group">
                                <label for="phone">قیمت(حتما از کیبورد انگلیسی استفاده نمیایید)<span class="text-danger">*</span></label>
                                <input type="text" name="amount" class="form-control" id="price" placeholder="قیمت">
                            </div>

                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary submit_user">ثبت</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<div id="admin-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">کابر پنل جدید</h4>
            </div>
            <div class="modal-body">

                <div class="box box-primary">
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form role="form" action="{{route('addAdmin')}}" method="post">
                        {{csrf_field()}}
                        <div class="box-body">
                            <input type="hidden" name="status" value="1">
                            <div class="form-group">
                                <label for="first_name">نام </label>
                                <input autocomplete="off" type="text" name="first_name" class="form-control" id="first_name" placeholder="نام">
                            </div>
                            <div class="form-group">
                                <label for="last_name">نام‌خانوادگی</label>
                                <input autocomplete="off" type="text" name="last_name" class="form-control" id="last_name" placeholder="نام‌خانوادگی">
                            </div>
                            <div class="form-group">
                                <label for="phone">موبایل</label>
                                <input required autocomplete="off" type="text" name="phone" class="form-control" id="phone" placeholder="نام‌خانوادگی">
                            </div>
                            <div class="form-group">
                                <label for="username">ایمیل</label>
                                <input autocomplete="off" type="email" name="username" class="form-control" id="username" placeholder="ایمیل" required>
                            </div>
                            <div class="form-group">
                                <label for="">نقش</label>
                                <select name="role" id="">
                                    <option value="1">مدیر کل</option>
                                    <option value="2">پشتیبان</option>
                                    <option value="3">ناظر</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password">رمز</label>
                                <input required autocomplete="off" type="password" name="password" class="form-control" id="password" placeholder="رمز">

                            </div>

                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary submit_user">ثبت</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
