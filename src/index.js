
    // caching object to images associated to the states,
    var stateCache = {};
    
    var states = {
            'Arizona': 'AZ',
            'Alabama': 'AL',
            'Alaska': 'AK',
            'Arizona': 'AZ',
            'Arkansas': 'AR',
            'California': 'CA',
            'Colorado': 'CO',
            'Connecticut': 'CT',
            'Delaware': 'DE',
            'Florida': 'FL',
            'Georgia': 'GA',
            'Hawaii': 'HI',
            'Idaho': 'ID',
            'Illinois': 'IL',
            'Indiana': 'IN',
            'Iowa': 'IA',
            'Kansas': 'KS',
            'Kentucky': 'KY',
            'Kentucky': 'KY',
            'Louisiana': 'LA',
            'Maine': 'ME',
            'Maryland': 'MD',
            'Massachusetts': 'MA',
            'Michigan': 'MI',
            'Minnesota': 'MN',
            'Mississippi': 'MS',
            'Missouri': 'MO',
            'Montana': 'MT',
            'Nebraska': 'NE',
            'Nevada': 'NV',
            'New Hampshire': 'NH',
            'New Jersey': 'NJ',
            'New Mexico': 'NM',
            'New York': 'NY',
            'North Carolina': 'NC',
            'North Dakota': 'ND',
            'Ohio': 'OH',
            'Oklahoma': 'OK',
            'Oregon': 'OR',
            'Pennsylvania': 'PA',
            'Rhode Island': 'RI',
            'South Carolina': 'SC',
            'South Dakota': 'SD',
            'Tennessee': 'TN',
            'Texas': 'TX',
            'Utah': 'UT',
            'Vermont': 'VT',
            'Virginia': 'VA',
            'Washington': 'WA',
            'West Virginia': 'WV',
            'Wisconsin': 'WI',
            'Wyoming': 'WY',
        };
    
    function display_error(message) {
        var e = document.getElementById("errorMessage");
        e.innerHTML = message;
    }

    function hidden_input(name, value) {
        var d = document.getElementById("OK");
        d.dataset[name] = value || '';
    }

    function get_input(name) {
        var d = document.getElementById("OK");
        return d.dataset[name] || '';
    } 

    function get_user(map) {
        var data = {id: user_id,
                    action: 'get_user'};
        var cb = function(data) {
            var obj = JSON.parse(data);
            if (obj.status == "success") {
                var banner = document.getElementById("banner");
                banner.innerHTML = "Welcome, " + obj.message;
                if (obj.states != undefined && obj.states.length > 0) {
                   var mapData = {};
                    // populate the map with states from server,
                    obj.states.forEach(function(value, key) {
                         mapData[states[value]] = { fillKey : 'VISITED'};
                     });
                    map.updateChoropleth(mapData);
                 }
            }
            else {
                console.log("[get_user ] request failed. %O", data);
            }
        };

      call_user_info(data, cb);
    }

    function get_pending() {
        var data = {id: user_id,
                    action: 'get_pending'};
        var cb = function(data) {
            var obj = JSON.parse(data);
              if (obj.status == "success") {
                var msg = document.getElementById("msg");
                msg.innerHTML = "Number of currently stored images: " + obj.message;
              }
              else {
                    console.log("[get_pending ] request failed: %O", obj);
                }
        };
        call_user_info(data, cb);
    }
    
    function get_images(state) {
        if (stateCache[state] != undefined) {
            // grab from cache all of the images,
            var images = stateCache[state];
            images.forEach(function(v,k) {
                console.log("[get_images (cache) ] processing, %O", v);
                loadImage(
                        v,
                        function (img) {
                            document.getElementById("preview").appendChild(img);
                        },
                        {orientation: true,
                        maxWidth: 200,
                        maxHeight: 200} // Options
                    );                           
            });
        }
        else {
            var data = {id: user_id,
                        state: state,
                        action: 'get_images'};
            var cb = function(data) {
                var obj = JSON.parse(data);
                if (obj.status == "success") {
                    // empty out preview pane,
                    $("#preview").empty();
                    // present all the images onto the browser,
                    if (obj.image_path.length > 0) {
                        // add the data to cache,
                        stateCache[state] = obj.image_path;
                        console.log("[get_images ] added " + stateCache[state].length + ' images to cache');
                        obj.image_path.forEach(function(v,k) {
                            console.log("[get_images ] processing, %O", v);
                            loadImage(
                                    v,
                                    function (img) {
                                        document.getElementById("preview").appendChild(img);
                                    },
                                    {orientation: true,
                                    maxWidth: 200,
                                    maxHeight: 200} // Options
                                );                           
                        });
                    }
                    
                }
                else {
                    console.log("[get_images ] request failed: %O", obj);
                }
            };
            console.log("[get_images ] calling user info...");
            call_user_info(data, cb);
        }
    }
    
    // helper for requests, 
    function call_user_info(dataObj, callback) {
        $.ajax({
            type: 'GET',
            url: 'userinfo.php',
            data: dataObj,
            success: callback,
            error: function(data) {
                var obj = JSON.parse(data);
                console.log("[call_user_info ] error: %O", obj);
            }
        });
    }

    $(document).ready(function() {
        // we don't need to redefine `states` here, it should be
        //  globally accessible,
        var map = new Datamap({
            scope: 'usa',
            //projection: 'orthographic',
            element: document.getElementById('container'),
            responsive: true,
            height: 300,
            fills: {
                'VISITED': 'blue',
                'PENDING': 'red',
                defaultFill: 'black'
            },
            data: {},
            done: function(datamap) {
                // the below event can be used to send a request over to the server,
                //  to fetch all the images associated to the given state
                datamap.svg.selectAll('.datamaps-subunit').on('click', function(geography) {
                    // empty out preview pane before showing images,
                    var curr = get_input('currentState');
                    console.log("[onclick] current state",curr);
                    // if we have already clicked on the state, return immediately;
                    if (curr == geography.properties.name) return;
                    $("#preview").empty();
                    hidden_input('currentState', geography.properties.name);
                    get_images(geography.properties.name);
                });
            } 
        });

        map.legend();

        get_user(map);
        get_pending();

        $("#myForm").submit(function(e) {          
            e.preventDefault();
            submit_pending();
        });

    
        function submit_pending() {
            var address = document.getElementById("address");
            var preview = document.getElementById("preview");
            var hidden = document.getElementById("OK");
            var file = document.getElementById("uploadImg");
            var that = hidden.dataset;        

            var data = new FormData();

            $.each(file.files, function(key, value) {
                data.append(key, value);
            });

            data.append("user_id", user_id);
            data.append("filepath", file.value);
            data.append("lng", that.lng);
            data.append("lat", that.lat);
            data.append("address", that.address);
            data.append("city", that.city);
            data.append("state", that.state);
            data.append("postal", that.postal);
            data.append("country", that.country);

            console.log("[submit_pending ] trying to submit data...");

            if (address.value != '') {
            console.log("[submit_pending ] address: ", address.value);

            $.ajax({
                    type: 'POST',
                    url: 'pending.php',
                    processData: false, // Don't process the files
                    contentType: false, // Set content type to false 
                    data: data,
                    success: function(data) {
                        var obj = JSON.parse(data);
                        console.log("[submit_pending ] obj %O", obj);
                        if (obj.status == "success") {
                            get_pending();
                        }
                        else {
                            // an error had occurred,
                            display_error(obj.message);
                        }
                    }
                });

                address.value = '';
                file.value = '';
                // remove canvas element,
                $("#preview").empty();
            }
        }
        
        var fileEle = document.getElementById("uploadImg");
        fileEle.addEventListener("change", getExifData, false);

        function getExifData() {
            var files = this.files;

            for (var i = 0; i < files.length; i++) {
            $("#preview").empty();
            EXIF.getData(files[i], function() {
                    var exifData = EXIF.getAllTags(this);
                    var addressEle = document.getElementById("address");
                    var okay_btn = document.getElementById("OK");

                    // check whether we have actual data within the IMG,
                    if (Object.keys(exifData).length > 0) {

                        var latRef  = exifData['GPSLatitudeRef'];
                        var latList = exifData['GPSLatitude'];
                        
                        if (latList != undefined) {

                            var lat = DMS(latList);

                            if (latRef == "S") lat = lat * -1;
                            var lngRef  = exifData['GPSLongitudeRef'];
                            var lngList = exifData['GPSLongitude'];
                            var lng = DMS(lngList); 

                            if (lngRef == "W") lng = lng * -1;

                            $.ajax({
                                type: 'GET',
                                url: 'http://nominatim.openstreetmap.org/reverse',
                                data: {
                                        'format': 'json',
                                        'lat' : lat,
                                        'lon': lng
                                },
                                success: function(data) {
                                    var address = data.address;
                                    var state = address.state;
                                    /*
                                        data.address = {
                                            city:"Providence",
                                            country:"United States of America",
                                            country_code:"us",
                                            county:"Providence County",
                                            house_number:"112",
                                            postcode:"02906",
                                            road:"4th Street",
                                            state:"Rhode Island"
                                        };
                                    */

                                    console.log("[getExifData ] updating the map for", state);

                                    var mapData = {};
                                    mapData[states[state]] = { 'fillKey' : 'PENDING' };
                                    map.updateChoropleth(mapData);

                                    addressEle.value = data.display_name;
                                    hidden_input("user_id", user_id);
                                    hidden_input("lng", lng);
                                    hidden_input("lat", lat);
                                    hidden_input("address", data.display_name);
                                    hidden_input("city", data.address.city);
                                    hidden_input("state", data.address.state);
                                    hidden_input("postal", data.address.postcode);
                                    hidden_input("country", data.address.country_code);
                                }
                            });

                                loadImage(
                                    this,
                                    function (img) {
                                        document.getElementById("preview").appendChild(img);
                                    },
                                    {orientation: true,
                                    maxWidth: 200,
                                    maxHeight: 200} // Options
                                );
                        }
                        else {
                                addressEle.innerHTML = "Unable to read GPS data.";
                        }
                    }
                });
            }
        }

        function DMS(coords) {
            var deg = coords[0],
                mins = coords[1],
                sec = coords[2];
            return ( deg + ( (mins / 60.) + (sec / 3600.) ) );
        }
    });