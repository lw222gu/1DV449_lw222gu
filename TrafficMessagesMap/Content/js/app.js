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
    TrafficMap.renderSelect();
    TrafficMap.renderList();

    TrafficMap.map =  L.map('map', {
      center: [TrafficMap.lat, TrafficMap.long], //standard latitude and longitude
      minZoom: 2,
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
    //Clears map from markers
    TrafficMap.markers.forEach(function(mark){
      TrafficMap.map.removeLayer(mark);
    });

    var messages = TrafficMap.json["messages"];

    for(var i = 0; i < messages.length; i++){
      var category = messages[i]["category"];

      if(TrafficMap.selection == category || TrafficMap.selection == "4"){
        var marker = L.marker([messages[i].latitude, messages[i].longitude], {icon: TrafficMap.icons[category]}).addTo(TrafficMap.map);
        marker.bindPopup(TrafficMap.renderListItemContent(messages[i]));
        TrafficMap.markers.push(marker);
      }
    }
  },

  createMarkerIcons: function(){
    var Icon = L.Icon.extend({
      options: {
        iconSize:     [25, 24],
        iconAnchor:   [8, 23],
        popupAnchor:  [0, -25]
      }
    });

    //Creates new Icon objects for custom icons
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

  setOnclicks: function(){
    var anchors = document.getElementsByClassName("message-anchor");
    for(var i = 0; i < anchors.length; i++){
      anchors[i].onclick = function(){
        var coordinates = this.getAttribute("value");
        coordinates = coordinates.split(", ", 2);
        TrafficMap.map.setView([coordinates[0], coordinates[1]], 6);

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

  renderSelect: function(){
    var messagesDiv = document.getElementById("traffic-messages");
    var h2 = document.createElement("h2");
    h2.innerHTML = "Trafikmeddelanden";
    messagesDiv.appendChild(h2);

    //Create select element
    var select = document.createElement("select");
    select.setAttribute("id", "select-category");

    for(var index = 0; index < TrafficMap.options.length; index++){
      var option = document.createElement("option");
      option.setAttribute("value", index);
      option.innerHTML = TrafficMap.options[index];
      if(index == 4){
        option.setAttribute("selected", "selected");
      }
      select.appendChild(option);
    }
    messagesDiv.appendChild(select);
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
        li.setAttribute("class", classes[messages[mess]["category"]]);

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
    //date = date.replace("/Date(", "");
    //date = date.replace(")/", "");
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
