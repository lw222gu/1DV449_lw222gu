"use strict";

var TrafficMap = {

  json: {},
  map: L.map('map', {
      center: [63.0, 14.0], //latitude and longitude for Östersund, the middle of Sweden
      minZoom: 2,
      zoom: 5
  }),
  icons: {},

  init: function(){
    /*var map = L.map('map', {
        center: [63.0, 14.0], //latitude and longitude for Östersund, the middle of Sweden
        minZoom: 2,
        zoom: 5
    });*/

    L.tileLayer( 'http://{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright" title="OpenStreetMap" target="_blank">OpenStreetMap</a> contributors | Tiles Courtesy of <a href="http://www.mapquest.com/" title="MapQuest" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png" width="16" height="16">',
        subdomains: ['otile1','otile2','otile3','otile4']
    }).addTo( TrafficMap.map );

    TrafficMap.createPointerIcons();
    TrafficMap.renderPointers();
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

  renderPointers: function(){
    TrafficMap.getJson();
    var messages = TrafficMap.json["messages"];
    for(var i = 0; i < messages.length; i++){
      var category = messages[i]["category"];
      var marker = L.marker([messages[i].latitude, messages[i].longitude], {icon: TrafficMap.icons[category]}).addTo(TrafficMap.map);
    }
  },

  createPointerIcons: function(){

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
  }

};

window.onload = TrafficMap.init;
