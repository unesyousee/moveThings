@extends('admin.layout/master')
@section('content')

    <div class="content-wrapper nobaar-dark">
        <section class="content-header nobaar-dark">
            <h1>خطاهای اپلیکیشن </h1>
        </section>
        <!-- Main content -->
        <section class="content nobaar-dark">

            <div class="row">
                <div class="col-lg-12">
                    <table class="nobaar-table errors-logs">
                        <caption>خطاها</caption>
                        <thead>
                        <tr>
                            <th>data</th>
                            <th>header</th>
                            <th>body</th>
                            <th>response</th>
                            <th>url</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($App_errors as $error)
                            <tr>
                                <td style="width: 10%;">{{$error->created_at}}</td>
                                <td style="width: 25%;">{{$error->body}}</td>
                                <td style="width: 25%;">{{$error->header}}</td>
                                <td style="width: 25%;">{{$error->response}}</td>
                                <td style="width: 25%;">{{$error->url}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@stop
