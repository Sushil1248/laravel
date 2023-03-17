@extends('company.layouts.app')
@section('title', '- Home')

@section('content')
<section>
    <div class="container">
      <div class="row mb-4 mt-4">
        <div class="col-xl-3 col-md-6">
          <div class="card h-100">
            <div class="card-body">
                <form class="navbar-search" id="search-form">
                    <div class="input-group">
                        <input type="text"  value="{{ request('daterange_filter') }}" name="daterange_filter" autocomplete="off" class="form-control bg-light multi-date-rangepicker small " placeholder="Date Range"
                        aria-label="Search" aria-describedby="basic-addon2"  autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-md btn-default" type="submit">
                                <i class="fa fa-search fa-sm"></i>
                            </button>
                        </div>
                        <div class="input-group-append">
                            <a title="Reiniciar" href="" class="btn btn-md dark-blue-btn" >
                                <i class="fa fa-redo" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
          </div>
        </div>
    </div>
        <div class="row mb-3">
            <div class="col-xl-3 col-md-6 mb-4 mt-4">
                <div class="card h-100 totalCard" data-open-url="{{ route('user.list') }}">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Registered Users</div>
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x "></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-md-6 mb-4 mt-4">
                <div class="card h-100 totalCard" data-open-url="{{ route('user.list') }}">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Active Users</div>
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $activeUsers }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x "></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>


        </div>
        <div class="row container">
            <div class="col-xl-6 col-md-6 mb-4 mt-4">
                <h5>Users Detail</h5>
                <div class="card h-100 totalCard">

                    <div class="card-body">

                        <!-- HTML -->
                        <div id="chartdiv"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@parent
@endsection

@section('page-js')
<!-- Styles -->
<style>

#chartdiv, #companyChartdiv {
  width: 100%;
  height: 500px;
}
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<!-- Chart code -->
<script>
am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
var root = am5.Root.new("chartdiv");

// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
root.setThemes([
  am5themes_Animated.new(root)
]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/xy-chart/
var chart = root.container.children.push(am5xy.XYChart.new(root, {
  panX: true,
  panY: true,
  wheelX: "panX",
  wheelY: "zoomX",
  pinchZoomX:true
}));

// Add cursor
// https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
cursor.lineY.set("visible", false);


// Create axes
// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
var xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });
xRenderer.labels.template.setAll({
  rotation: -90,
  centerY: am5.p50,
  centerX: am5.p100,
  paddingRight: 15
});

var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
  maxDeviation: 1,
  categoryField: "statistics",
  renderer: xRenderer,
  tooltip: am5.Tooltip.new(root, {})
}));

var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
  maxDeviation: 1,
  renderer: am5xy.AxisRendererY.new(root, {})
}));


// Create series
// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
var series = chart.series.push(am5xy.ColumnSeries.new(root, {
  name: "Series 1",
  xAxis: xAxis,
  yAxis: yAxis,
  valueYField: "value",
  sequencedInterpolation: true,
  categoryXField: "statistics",
  tooltip: am5.Tooltip.new(root, {
    labelText:"{valueY}"
  })
}));

series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5 , strokeWidth: 1 });
series.columns.template.adapters.add("fill", function(fill, target) {
  return chart.get("colors").getIndex(series.columns.indexOf(target));
});

series.columns.template.adapters.add("stroke", function(stroke, target) {
  return chart.get("colors").getIndex(series.columns.indexOf(target));
});





// Set data
var data = [{
  statistics: "Total users",
  value: {{ $totalUsers }}
}, {
  statistics: "Active users",
  value: {{ $activeUsers }}
}];

xAxis.data.setAll(data);
series.data.setAll(data);


// Make stuff animate on load
// https://www.amcharts.com/docs/v5/concepts/animations/
series.appear(1000);
chart.appear(1000, 100);

}); // end am5.ready()

</script>
@endsection
