"use strict";

var TrafficMap = {

  json: undefined,
  map: {},
  lat: 59.0,
  long: 15.0,
  icons: {},
  selection: "4",
  markers: [],
  markersGroup: L.layerGroup(this.markers),
  options: ["Vägtrafik", "Kollektivtrafik", "Planerad störning", "Övrigt", "Visa alla kategorier"],
  //options: ["Vägtrafik": "0", "Kollektivtrafik": "1", "Planerad störning":"2", "Övrigt": "3", "Visa alla kategorier": null] ,

  init: function(){

    TrafficMap.getJson();
    TrafficMap.renderList();

    TrafficMap.map =  L.map('map', {
      center: [TrafficMap.lat, TrafficMap.long], //standard latitude and longitude
      minZoom: 4,
      zoom: 6
    });

    //If user grants access to location, reset map-view
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
      TrafficMap.renderList();
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
    //Clear map from markers
    TrafficMap.markers.forEach(function(mark){
      TrafficMap.map.removeLayer(mark);
    });

    var messages = TrafficMap.json["messages"];

    for(var i = 0; i < messages.length; i++){
      var category = messages[i]["category"];
      var priority = messages[i]["priority"];

      if(TrafficMap.selection == category || TrafficMap.selection == "4"){
        var marker = L.marker([messages[i].latitude, messages[i].longitude], {icon: TrafficMap.icons[category + "" + priority]}).addTo(TrafficMap.map);
        marker.bindPopup(TrafficMap.renderListItemContent(messages[i]));
        TrafficMap.markers.push(marker);
      }
    }
  },

  createMarkerIcons: function(){
    var Icon = L.Icon.extend({
      options: {
        shadowUrl: 'Content/css/images/icons/shadow.svg',
        iconSize:     [30, 35],
        shadowSize:   [30, 32],
        iconAnchor:   [15, 33],
        shadowAnchor: [10, 30],
        popupAnchor:  [0, -25]
      }
    });

    //Creates new Icon objects for custom icons

    var vagtrafik1 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 0, 31, 35))'}),
        vagtrafik2 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 40, 31, 35))'}),
        vagtrafik3 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 80, 31, 35))'}),
        vagtrafik4 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 120, 31, 35))'}),
        vagtrafik5 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 160, 31, 35))'}),
        kollektivtrafik1 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 200, 31, 35))'}),
        kollektivtrafik2 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 240, 31, 35))'}),
        kollektivtrafik3 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 280, 31, 35))'}),
        kollektivtrafik4 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 320, 31, 35))'}),
        kollektivtrafik5 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 360, 31, 35))'}),
        planeradStorning1 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 400, 31, 35))'}),
        planeradStorning2 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 440, 31, 35))'}),
        planeradStorning3 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 480, 31, 35))'}),
        planeradStorning4 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 520, 31, 35))'}),
        planeradStorning5 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 560, 31, 35))'}),
        ovrigt1 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 600, 31, 35))'}),
        ovrigt2 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 640, 31, 35))'}),
        ovrigt3 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 680, 31, 35))'}),
        ovrigt4 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 720, 31, 35))'}),
        ovrigt5 = new Icon({iconUrl: 'Content/css/images/markers-sprite.svg#svgView(viewBox(0, 760, 31, 35))'});

    TrafficMap.icons = {
      "01": vagtrafik1,
      "02": vagtrafik2,
      "03": vagtrafik3,
      "04": vagtrafik4,
      "05": vagtrafik5,
      "11": kollektivtrafik1,
      "12": kollektivtrafik2,
      "13": kollektivtrafik3,
      "14": kollektivtrafik4,
      "15": kollektivtrafik5,
      "21": planeradStorning1,
      "22": planeradStorning2,
      "23": planeradStorning3,
      "24": planeradStorning4,
      "25": planeradStorning5,
      "31": ovrigt1,
      "32": ovrigt2,
      "33": ovrigt3,
      "34": ovrigt4,
      "35": ovrigt5
    };
  },

  setOnclicks: function(){
    var anchors = document.getElementsByClassName("message-anchor");
    for(var i = 0; i < anchors.length; i++){
      anchors[i].onclick = function(){
        var coordinates = this.getAttribute("value");
        coordinates = coordinates.split(", ", 2);
        TrafficMap.map.setView([coordinates[0], coordinates[1]], 10);

        //Loops through existing markers to find the one that is connected to the messege the user clicked on.
        TrafficMap.markers.forEach(function(mark){
          if(mark.getLatLng()["lat"] == coordinates[0] && mark.getLatLng()["lng"] == coordinates[1]){
            mark.openPopup();
            document.getElementById("map").scrollIntoView();
          }
        });
        return false;
      }
    }
  },

  renderList: function(){
    var messagesDiv = document.getElementById("traffic-messages");
    var messageListDiv = document.getElementById("traffic-messages-list");

    if(messageListDiv === null){
      messageListDiv = document.createElement("div");
      messageListDiv.setAttribute("id", "traffic-messages-list");
    }
    else {
      messageListDiv.innerHTML = "";
    }

    //Create list of messages
    var ul = document.createElement("ul");
    ul.setAttribute("class", "messages-list");

    var messages = TrafficMap.json["messages"];
    var classes = ["vagtrafik", "kollektivtrafik", "planerad-storning", "ovrigt"];

    for(var mess = 0; mess < messages.length; mess++){
      if(TrafficMap.selection == messages[mess]["category"] || TrafficMap.selection == "4"){
        var a = document.createElement("a");
        a.setAttribute("class", "message-anchor");
        a.setAttribute("href", "#");
        a.setAttribute("value", messages[mess]["latitude"] + ", " + messages[mess]["longitude"]);

        var li = document.createElement("li");
        li.setAttribute("class", classes[messages[mess]["category"]]+messages[mess]["priority"]);

        var liContent = TrafficMap.renderListItemContent(messages[mess]);

        li.appendChild(liContent);
        a.appendChild(li);
        ul.appendChild(a);
      }
    }
    messageListDiv.appendChild(ul);
    messagesDiv.appendChild(messageListDiv);

    TrafficMap.setOnclicks();
  },

  //Function is used both by renderList-function and to generate content for popups
  renderListItemContent: function(message){

    var liContent = document.createElement("div");

    var h3 = document.createElement("h3");
    h3.innerHTML = message["title"];

    var pDate = document.createElement("p");
    pDate.setAttribute("class", "date");
    pDate.innerHTML = TrafficMap.formatDate(message["createddate"]);

    var pCategory = document.createElement("p");
    pCategory.setAttribute("class", "category");
    pCategory.innerHTML = TrafficMap.options[message["category"]];

    var pDescription = document.createElement("p");
    if(message["description"] != ""){
      pDescription.innerHTML = message["description"];
    }
    else {
      pDescription.innerHTML = "Beskrivning saknas.";
    }

    liContent.appendChild(h3);
    liContent.appendChild(pDate);
    liContent.appendChild(pCategory);
    liContent.appendChild(pDescription);

    return liContent;
  },

  formatDate: function(date){
    date = parseInt(date, 10);
    date = new Date(date);

    var y = date.getFullYear();
    var m = TrafficMap.addZeroToDate(date.getMonth() + 1);
    var d = TrafficMap.addZeroToDate(date.getDate());
    var h = TrafficMap.addZeroToDate(date.getHours());
    var min = TrafficMap.addZeroToDate(date.getMinutes());
    var s = TrafficMap.addZeroToDate(date.getSeconds());
    return y + "-" + m + "-" + d + ", " + h + "." + min + "." + s + " ";
  },

  addZeroToDate: function (i){
      if (i < 10){
          i = "0" + i;
      }
      return i;
  },

  getPosition: function(position){
    TrafficMap.lat = position.coords.latitude;
    TrafficMap.long = position.coords.longitude;
    TrafficMap.map.setView([TrafficMap.lat, TrafficMap.long], 10);
  }
};

window.onload = TrafficMap.init;
