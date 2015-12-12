"use strict";

var TrafficMap = {

  json: {},
  map: {},
  lat: 59.0,
  long: 15.0,
  icons: {},
  selection: document.getElementById("select-category").value,
  markers: [],
  markersGroup: L.layerGroup(this.markers),
  //options: ["Vägtrafik": "0", "Kollektivtrafik": "1", "Planerad störning":"2", "Övrigt": "3", "Visa alla kategorier": null],

  init: function(){
    TrafficMap.map =  L.map('map', {
      center: [TrafficMap.lat, TrafficMap.long], //standard latitude and longitude
      minZoom: 2,
      zoom: 5
    });

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(TrafficMap.getPosition);
    }

    L.tileLayer('http://{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://osm.org/copyright" title="OpenStreetMap" target="_blank">OpenStreetMap</a> contributors | Tiles Courtesy of <a href="http://www.mapquest.com/" title="MapQuest" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png" width="16" height="16">',
      subdomains: ['otile1','otile2','otile3','otile4']
    }).addTo(TrafficMap.map);

    var select = document.getElementById("select-category");
    select.onchange = function(){
      TrafficMap.selection = this.value;
      TrafficMap.renderMarkers();
    };

    TrafficMap.createMarkerIcons();
    TrafficMap.renderMarkers();
  },

  getJson: function(){
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function(){
      if (xhr.readyState === 4 && xhr.status === 200){
        TrafficMap.json = JSON.parse(xhr.responseText);
      }
    };
    xhr.open("GET", "Resources/trafficMessages.json", false);
    xhr.send(null);
  },

  renderMarkers: function(){

    TrafficMap.markers.forEach(function(mark){
      TrafficMap.map.removeLayer(mark);
    });

    TrafficMap.getJson();
    var messages = TrafficMap.json["messages"];

    for(var i = 0; i < messages.length; i++){
      var category = messages[i]["category"];

      if(TrafficMap.selection === "4"){
        var marker = L.marker([messages[i].latitude, messages[i].longitude], {icon: TrafficMap.icons[category]}).addTo(TrafficMap.map);
        TrafficMap.markers.push(marker);
      }

      else {
        if(messages[i].category == TrafficMap.selection){
          var marker = L.marker([messages[i].latitude, messages[i].longitude], {icon: TrafficMap.icons[category]}).addTo(TrafficMap.map);
          TrafficMap.markers.push(marker);
        }
      }
    }
  },

  createMarkerIcons: function(){

    var Icon = L.Icon.extend({
      options: {
        iconSize:     [25, 24],
        iconAnchor:   [8, 23],
        popupAnchor:  [-3, -76]
      }
    });

    var kollektivtrafikIcon = new Icon({iconUrl: 'Content/css/images/kollektivtrafik-06.svg'}),
    ovrigtIcon = new Icon({iconUrl: 'Content/css/images/ovrigt-08.svg'}),
    planeradStorningIcon = new Icon({iconUrl: 'Content/css/images/planerad-storning-07.svg'}),
    vagtrafikIcon = new Icon({iconUrl: 'Content/css/images/vagtrafik-05.svg'});

    TrafficMap.icons = {
      "0": vagtrafikIcon,
      "1": kollektivtrafikIcon,
      "2": planeradStorningIcon,
      "3": ovrigtIcon
    };
  },

  getPosition: function(position){
    TrafficMap.lat = position.coords.latitude;
    TrafficMap.long = position.coords.longitude;
    TrafficMap.map.setView([TrafficMap.lat, TrafficMap.long], 7);
  }

};

window.onload = TrafficMap.init;
