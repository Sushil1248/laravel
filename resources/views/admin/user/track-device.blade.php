@extends(Auth::check() && Auth::user()->hasRole('1_Company') ? 'company.layouts.app' : 'admin.layouts.app')
@section('title', '- Device Track')


@section('content')
    <script src="https://cdn.socket.io/4.6.0/socket.io.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCTWcHCGo0JA8tBR6zBtvXMf93YrYMl_ok&callback=initMap" async
        defer></script>

    <section class="order-listing Invoice-listing">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <br />
                    <div class=" table-responsive list-items">
                        <table class="table">

                            {{-- User Details --}}
                            <tr style="background: #3eaf86;">
                                <td colspan="2" style="color:#FFF; font-weight:bold;">User Details</td>
                            </tr>
                            <tr>
                                <td width="40%">Name:</td>
                                <td>{{ $user['full_name'] }}</td>
                            </tr>
                            <tr>
                                <td width="40%">Email:</td>
                                <td>{{ $user['email'] }}</td>
                            </tr>
                            <tr>
                                <td width="40%">Contact No:</td>
                                <td>
                                    @if (isset($user->user_detail->mobile))
                                        {{ $user->user_detail->mobile }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>

                            {{-- Device Details --}}
                            @isset($device)
                                <tr style="background: #3eaf86;">
                                    <td colspan="2" style="color:#FFF; font-weight:bold;">Device Details</td>
                                </tr>
                                <tr>
                                    <td width="40%">Device Name</td>
                                        <td>
                                            @isset($device['device_name']){{ $device['device_name'] }}@endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%">Tracking Radius</td>
                                        <td>
                                            @isset($device['device_name']){{ $device['tracking_radius'] }}@endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="40%">Platform</td>
                                            <td>
                                                @isset($device['device_platform']){{ $device['device_platform'] }}
                                                @else
                                                    NA @endif
                                                </td>
                                            </tr>
                                        @endisset
                                        {{-- Vehicle Details --}}

                                        <tr style="background: #3eaf86;">
                                            <td colspan="2" style="color:#FFF; font-weight:bold;">Vechile Details</td>
                                        </tr>

                                        <tr>
                                            @forelse ($vehicles as $vehicle)
                                        <tr>
                                            <td width="50%">
                                                <ul>
                                                    <li>Vehicle Name: {{ ucfirst($vehicle->name) }}</li>
                                                    <li>Vehicle Number: {{ ucfirst($vehicle->vehicle_num) }}</li>
                                                    <li>Extra Notes: {{ $vehicle->extra_notes }}</li>
                                                </ul>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td width="100%">
                                                NA
                                            </td>
                                        </tr>
                                        @endforelse
                                        </tr>

                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row" style="margin:10px 0px;;">
                                    <div class="col-lg-8" style="font-weight:bold;">
                                        Note: The location will get updated after every 4 seconds
                                    </div>
                                    <div class="col-lg-4">
                                        <span class="float-right" id="myButton"
                                            style="background:#dc3545; color:#FFF; padding:2px 10px; border-radius:5px; font-size:13px; cursor:pointer">
                                            Track {{ ucwords($user['full_name']) }}
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                    </div>
                                </div>
                                <div id="map" style="height: 500px; width: 100%; height:82vh"></div>
                            </div>
                        </div>
                    </div>
                </section>
                <script>
                    var device_token = "{{ $token }}";
                    var latitude = "{{ $data ? $data->latitude : 30.8333 }}";
                    var longitude = "{{ $data ? $data->longitude : 76.9357 }}";
                
                    console.log("Device Token : ", latitude)
                
                    function initMap() {
                        var myLatLng = {
                            lat: parseFloat(latitude),
                            lng: parseFloat(longitude)
                        };
                        var map = new google.maps.Map(document.getElementById('map'), {
                            zoom: 18,
                            center: myLatLng
                        });
                
                        const customIcon = {
                            url: 'https://www.svgrepo.com/show/110969/car-with-roof-rack.svg',
                            size: new google.maps.Size(64, 64),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(32, 64),
                            scaledSize: new google.maps.Size(64, 64)
                        };
                
                
                        var marker = new google.maps.Marker({
                            position: myLatLng,
                            map: map,
                            icon: customIcon,
                            title: '{{ $user['full_name'] }}'
                        });
                
                        const path = new google.maps.Polyline({
                            path: [],
                            strokeColor: "#3eaf86",
                            strokeOpacity: 1.0,
                            strokeWeight: 5,
                            map: map
                        });
                
                        // Connect to Socket.io server
                        const socket = io('http://10.20.20.224:1234/', {
                            cors: {
                                origin: "http://10.20.20.224:1234",
                                methods: ["GET", "POST"]
                            }
                        });
                        // socket.emit('my_event', { my_data: 'Hello, server!' });
                
                        socket.on('connection', function (data) {
                            console.log("Messaege", data)
                        });
                
                        // Listen for real-time location data from Socket.io server
                        socket.on('fetch_lat_long', function (data) {
                            var latLng = {
                                lat: parseFloat(data.deviceLatitude),
                                lng: parseFloat(data.deviceLongitude)
                            };
                            console.log(data)
                
                            marker.setPosition(latLng);
                            map.panTo(latLng);
                
                            // Add the new point to the path
                            var pathPoints = path.getPath();
                            pathPoints.push(latLng);
                            path.setPath(pathPoints);
                        });
                    }
                
                    // Get a reference to the button element
                    const btn = document.getElementById("myButton");
                    let intervalId;
                
                    // Add a click event listener to the button
                    btn.addEventListener("click", () => {
                        if (intervalId) {
                            // Stop tracking
                            clearInterval(intervalId);
                            intervalId = null;
                            btn.innerHTML = "Track {{ ucwords($user['full_name']) }}";
                        } else {
                            // Start tracking
                            track_device();
                            intervalId = setInterval(track_device, 4000);
                            btn.innerHTML = "Stop Tracking {{ ucwords($user['full_name']) }}";
                        }
                    });
                
                    function track_device() {
                        console.log("kwjf");
                        const socket = io('http://10.20.20.224:1234/', {
                            cors: {
                                origin: "http://10.20.20.224:1234",
                                methods: ["GET", "POST"]
                            }
                        });
                        socket.emit("fetch_lat_long", {
                            token: device_token
                        });
                    }
                
                    // initMap();
                </script>         
                @parent
            @endsection
