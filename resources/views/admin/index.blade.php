@extends('layouts.head')
@section('content')
@if (\Session::has('success'))
<div class="alert alert-success alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {!! \Session::get('success') !!}
</div>
@elseif (\Session::has('error'))
<div class="alert alert-danger alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {!! \Session::get('error') !!}
</div>
@endif

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                ملخص
            </div>
            <h2 class="page-title">
                لوحة القيادة
            </h2>
        </div>
        <!-- Page title actions -->
        
    </div>
</div>
<div class="row row-deck row-cards">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader text-warning">مستخدمون جدد</div>
                    <div class="ml-auto lh-1 text-muted">آخر 30 يومًا</div>
                </div>
                <div class="h1 mb-3">{{$data['users']['total']}}</div>
                <div id="chart-active-users" class="chart-sm"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader text-warning">عملاء محتملين</div>
                    <div class="ml-auto lh-1 text-muted">آخر 30 يومًا</div>
                </div>
                <div class="h1 mb-3">{{$data['leads']['total']}}</div>
                <div id="chart-leads-bg" class="chart-sm"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader text-success">الأرباح ({{env('USD_EQ')}})</div>
                    <div class="ml-auto lh-1 text-muted">آخر 30 يومًا</div>
                </div>
                <div class="h1 mb-3">@if(env('USD_EQ') == 'USD')$@endif{{$data['earns']['total']}}</div>
                <div id="chart-revenue-bg" class="chart-sm"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader text-danger">انسحبت ({{env('USD_EQ')}})</div>
                    <div class="ml-auto lh-1 text-muted">آخر 30 يومًا</div>
                </div>
                <div class="h1 mb-3">@if(env('USD_EQ') == 'USD')$@endif{{$data['withs']['total']}}</div>
                <div id="chart-withdrawn-bg" class="chart-sm"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">أحدث العملاء</h4>
            </div>
            <table class="table card-table table-vcenter">
                <thead>
                    <tr>
                        <th>معرف المستخدم</th>
                        <th>شبكة</th>
                        <th>كمية</th>
                        <th>التاريخ / الوقت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['lhist'] as $l)
                    <tr>
                        <td><a href="{{route('userinfo', ['userid' => $l->userid])}}">{{$l->userid}}</a></td>
                        <td>{{$l->network}}</td>
                        <td>${{$l->points / env('CASHTOPTS')}}</td>
                        <td>{{$l->created_at}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">أعلى الدول</h3>
                <div class="embed-responsive embed-responsive-16by9">
                    <div class="embed-responsive-item">
                        <div id="map-world" class="w-100 h-100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-auto ml-auto d-print-none">
            <a href="{{route('clear_dash')}}" class="btn btn-white d-none d-sm-inline-block">
                
                    <path d="M12 17l-2 2l2 2m-2 -2h9a2 2 0 0 0 1.75 -2.75l-.55 -1" />
                    <path d="M12 17l-2 2l2 2m-2 -2h9a2 2 0 0 0 1.75 -2.75l-.55 -1" transform="rotate(120 12 13)" />
                    <path d="M12 17l-2 2l2 2m-2 -2h9a2 2 0 0 0 1.75 -2.75l-.55 -1" transform="rotate(240 12 13)" />
                مسح ذاكرة التخزين المؤقت للوحة المعلومات
            </a>
            
            <span class="d-none ml-3 d-sm-inline">
                <a href="{{route('push_msg')}}" class="btn btn-Info">أرسل رسالة</a>
            </span>
    </div>
    
</div>
@endsection
@section('javascript')
<script type="text/javascript" src="/public/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="/public/js/apexcharts.min.js"></script>
<script type="text/javascript" src="/public/js/jquery.vmap.min.js"></script>
<script type="text/javascript" src="/public/js/jquery.vmap.world.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        window.ApexCharts && (new ApexCharts(document.getElementById('chart-active-users'), {
            chart: {
                type: "bar",
                fontFamily: 'inherit',
                height: 40.0,
                sparkline: {
                    enabled: true
                },
                animations: {
                    enabled: false
                },
            },
            plotOptions: {
                bar: {
                    columnWidth: '60%',
                }
            },
            dataLabels: {
                enabled: false,
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            fill: {
                opacity: 1,
            },
            series: [{
                name: "Members",
                data: @php echo json_encode($data['users']['count']) @endphp
            }],
            grid: {
                strokeDashArray: 4,
            },
            xaxis: {
                labels: {
                    padding: 0
                },
                tooltip: {
                    enabled: false
                },
                axisBorder: {
                    show: false,
                },
                type: 'datetime',
            },
            yaxis: {
                labels: {
                    padding: 4
                },
            },
            labels: @php echo json_encode($data['users']['date']) @endphp,
            colors: ["#206bc4"],
            legend: {
                show: false,
            },
        })).render();
    });
    document.addEventListener("DOMContentLoaded", function () {
        window.ApexCharts && (new ApexCharts(document.getElementById('chart-leads-bg'), {
            chart: {
                type: "area",
                fontFamily: 'inherit',
                height: 40.0,
                sparkline: {
                    enabled: true
                },
                animations: {
                    enabled: false
                },
            },
            fill: {
                opacity: 1,
            },
            stroke: {
                width: 2,
                lineCap: "round",
                curve: "stepline",
            },
            series: [{
                name: "Completed",
                data: @php echo json_encode($data['leads']['count']) @endphp
            }],
            grid: {
                strokeDashArray: 4,
            },
            yaxis: {
                labels: {
                    padding: 4
                },
            },
            labels: @php echo json_encode($data['leads']['date']) @endphp,
            colors: ["#ff922b"],
            legend: {
                show: false,
            },
        })).render();
    });
    document.addEventListener("DOMContentLoaded", function () {
        window.ApexCharts && (new ApexCharts(document.getElementById('chart-revenue-bg'), {
            chart: {
                type: "area",
                fontFamily: 'inherit',
                height: 40.0,
                sparkline: {
                    enabled: true
                },
                animations: {
                    enabled: false
                },
            },
            dataLabels: {
                enabled: false,
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            fill: {
                opacity: .16,
                type: 'solid'
            },
            stroke: {
                width: 2,
                lineCap: "round",
                curve: "smooth",
            },
            series: [{
                name: "{{env('USD_EQ')}}",
                data: @php echo json_encode($data['earns']['count']) @endphp
            }],
            grid: {
                strokeDashArray: 4,
            },
            xaxis: {
                labels: {
                    padding: 0
                },
                tooltip: {
                    enabled: false
                },
                axisBorder: {
                    show: false,
                },
                type: 'datetime',
            },
            yaxis: {
                labels: {
                    padding: 4
                },
            },
            labels: @php echo json_encode($data['earns']['date']) @endphp,
            colors: ["#5eba00"],
            legend: {
                show: false,
            },
        })).render();
    });

    document.addEventListener("DOMContentLoaded", function () {
        window.ApexCharts && (new ApexCharts(document.getElementById('chart-withdrawn-bg'), {
            chart: {
                type: "area",
                fontFamily: 'inherit',
                height: 40.0,
                sparkline: {
                    enabled: true
                },
                animations: {
                    enabled: false
                },
            },
            dataLabels: {
                enabled: false,
            },
            fill: {
                opacity: .16,
                type: 'solid'
            },
            stroke: {
                width: 2,
                lineCap: "round",
                curve: "smooth",
            },
            series: [{
                name: "{{env('USD_EQ')}}",
                data: @php echo json_encode($data['withs']['count']) @endphp
            }],
            grid: {
                strokeDashArray: 4,
            },
            xaxis: {
                labels: {
                    padding: 0
                },
                tooltip: {
                    enabled: false
                },
                axisBorder: {
                    show: false,
                },
                type: 'datetime',
            },
            yaxis: {
                labels: {
                    padding: 4
                },
            },
            labels: @php echo json_encode($data['withs']['date']) @endphp,
            colors: ["#ff0000"],
            legend: {
                show: false,
            },
        })).render();
    });
    document.addEventListener("DOMContentLoaded", function () {
        $('#map-world').vectorMap({
            map: 'world_en',
            backgroundColor: 'transparent',
            color: 'rgba(120, 130, 140, .1)',
            borderColor: 'transparent',
            scaleColors: ["#d2e1f3", "#206bc4"],
            normalizeFunction: 'polynomial',
            values: (chart_data = {
                {!!$data['online']!!}
            }),
            onLabelShow: function (event, label, code) {
                if (chart_data[code] > 0) {
                    label.append(': <strong>' + chart_data[code] + '</strong>');
                }
            },
            onRegionClick: function (element, code) {
                window.open("{{route('members')}}?cc=" + code, "_self")
            }
        });
    });

</script>
@endsection