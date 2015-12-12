"use strict";

var TrafficMap = {

  json: undefined,
  map: {},
  lat: 59.0,
  long: 15.0,
  icons: {},
//  selection: document.getElementById("select-category").value,
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
      zoom: 5
    });

    //If user grants access to location, reset map-view
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(TrafficMap.getPosition);
    }

    L.tileLayer('http://{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://osm.org/copyright" title="OpenStreetMap" target="_blank">OpenStreetMap</a> contributors | Tiles Courtesy of <a href="http://www.mapquest.com/" title="MapQuest" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png" width="16" height="16">',
      subdomains: ['otile1','otile2','otile3','otile4']
    }).addTo(TrafficMap.map);

    //TrafficMap.setOnclicks();

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

    //Requests JSON if not requested
    //if(TrafficMap.json == undefined){
    //  TrafficMap.getJson();
    //}

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
    /*for(var i = 0; i < anchors.length; i++){
      set onclick...
    }*/
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
    var messageList = document.getElementById("traffic-messages-list");

    if(messageList === null){
      messageList = document.createElement("div");
      messageList.setAttribute("id", "traffic-messages-list");
    }
    else {
      messageList.innerHTML = "";
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

        var li = document.createElement("li");
        li.setAttribute("class", classes[messages[mess]["category"]]);

        var h3 = document.createElement("h3");
        h3.innerHTML = messages[mess]["title"];

        var pDate = document.createElement("p");
        pDate.setAttribute("class", "date");
        pDate.innerHTML = messages[mess]["createddate"];

        var pCategory = document.createElement("p");
        pCategory.setAttribute("class", "category");
        pCategory.innerHTML = TrafficMap.options[messages[mess]["category"]];

        var pDescription = document.createElement("p");
        if(messages[mess]["description"] != ""){
          pDescription.innerHTML = messages[mess]["description"];
        }
        else {
          pDescription.innerHTML = "Beskrivning saknas.";
        }

        li.appendChild(h3);
        li.appendChild(pDate);
        li.appendChild(pCategory);
        li.appendChild(pDescription);
        a.appendChild(li);
        ul.appendChild(a);
      }
    }
    messageList.appendChild(ul);
    messagesDiv.appendChild(messageList);
  },

  getPosition: function(position){
    TrafficMap.lat = position.coords.latitude;
    TrafficMap.long = position.coords.longitude;
    TrafficMap.map.setView([TrafficMap.lat, TrafficMap.long], 7);
  }

};

window.onload = TrafficMap.init;
