<?php

  $url = parse_url(getenv('DATABASE_URL'));

  $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));

  $pdo = new PDO($dsn, $url['user'], $url['pass']);

$sql = 'SELECT * FROM poster order by id';
$stmt = $pdo->query($sql);

$sql = 'SELECT * FROM relation_poster order by id';
$stmt1 = $pdo->query($sql);
$relation_data_tmp = array();
while($r = $stmt1->fetch(PDO::FETCH_ASSOC)){
  $relation_data_tmp[] = array($r['kind1'], $r['kind2']); 
}
$relation_data = json_encode($relation_data_tmp);
?>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<style type="text/css">
html {
  height: 100%;
}
</style>

<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
<script type="text/javascript">
function postCheck(id){ 
  var el = document.getElementById('check_'+id);
  console.log(el.checked);
   $.ajax('http://url/check_post.php',
      {
        type: 'post',
        data: { 
          'id': id,
          'checked':el.checked
        },
        dataType: 'xml'
      }
    )
    .done(function(data) {
      console.log("success");
    })
    .fail(function() {
      console.log('failed');
    });
} 
</script>
<script>
let relation_data = <?php echo $relation_data; ?>;
let map;
var marker = {};
var myMarker;
let infoWindow = {};
function setRelationData(kind){
    var ary = [];
    for(x = 0; x < relation_data.length; x++)
    {
        if(relation_data[x][0] == kind){
            ary.push(relation_data[x][1]);
        }else if(relation_data[x][1] == kind){
            ary.push(relation_data[x][0]);
        }
    }
    var reration_html = "";
    for(x = 0; x < ary.length; x++)
    {
        if(x != 0){
            reration_html += ",";
        }
        reration_html += ""+ary[x];
    }
    $('#reration_data').html("<div>周辺区域:"+reration_html+"</div>");
}
function initMap() {
    map = new google.maps.Map(document.getElementById("gmap"), {
    center: { lat: 35.511143, lng: 139.470022 },
    zoom: 16,
  });
  setMyPlace();
}
function setMyPlace(){
    /*
    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition((position) => {
            var latitude  = position.coords.latitude;//緯度
            var longitude = position.coords.longitude;//経度
            var latlng = new google.maps.LatLng( latitude , longitude ) ;
            myMarker = new google.maps.Marker( {
                map: map ,
                position: latlng ,
            } ) ;
        });
    }
    */
}
function move_map(longitude, latitude, name){
  var ll = new google.maps.LatLng(longitude, latitude);
  map.panTo(ll);
  set_marker(longitude, latitude, name);
}
function set_marker(longitude, latitude, name){
  var ll = new google.maps.LatLng(longitude, latitude);
    if(marker[name]){
        marker[name].setMap(null);
    }
    marker[name] = new google.maps.Marker({
        position: ll,
        map: map,
        title: name
    });
    infoWindow[name] = new google.maps.InfoWindow({
        content: '<div class="window">'+name+'</div>'
    });
    infoWindow[name].open(map, marker[name]);
}
function filterPoster(kind){
    setRelationData(kind);
    $(".vis").hide();
    $("*[name=kind_"+kind+"]").show();
    $("*[name=kind_"+kind+"]").each(function(index, el){
        set_marker($(el).data('longitude'), $(el).data('latitude'), $(el).data('name'));
    });
}
</script>
</head>
<body>
<?php
for($x = 0;$x < 7; $x++){
?>
<div>
<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
<?php
for($y = 1;$y <= 10; $y++){
    if(($x*10)+$y > 67){
        break;
    }
?>
  <input type="radio" class="btn-check" name="btnradio" id="btnradio_<?php echo ($x*10)+$y; ?>" autocomplete="off" onclick="filterPoster(<?php echo ($x*10)+$y; ?>)" checked>
  <label class="btn btn-outline-primary" for="btnradio_<?php echo ($x*10)+$y; ?>"><?php echo ($x*10)+$y; ?></label>
<?php
}
?>
</div>
</div>
<?php
}
?>
<div id="reration_data">
</div>
<div>
<table class="table">
  <thead>
    <tr>
      <th scope="col"></th>
      <th scope="col">掲示板番号</th>
      <th scope="col">poster map</th>
      <th scope="col">google map</th>
      <th scope="col">map移動</th>
      <th scope="col">更新日時</th>
    </tr>
  </thead>
  <tbody>
<?php
while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
?>
  <tr name="kind_<?php echo $r['kind']; ?>" class="vis" data-longitude="<?php echo $r['longitude']; ?>" data-latitude="<?php echo $r['latitude']; ?>" data-name="<?php echo $r['name']; ?>">
    <td>
      <input type="checkbox" class="form-check-input" id="check_<?php echo $r['id']; ?>" onclick="postCheck(<?php echo $r['id']; ?>)" <?php echo $r['checked'] ? "checked" : ""; ?>/>
    </td>
    <td>
      <label class="form-check-label" for="check_<?php echo $r['id']; ?>"><?php echo $r['number']; ?> </label>
    </td>
    <td>
      <a href="https://machida.kukanjoho.jp/webgis/s#mapPage?z=16&ll=<?php echo $r['longitude']; ?>%2C<?php echo $r['latitude']; ?>&t=roadmap&mp=10&vlf=-1" target="_blank" rel="noopener">町田地図</a>
    </td>
    <td>
      <a href="https://www.google.com/maps/place/35%C2%B030'09.2%22N+139%C2%B028'42.0%22E/@<?php echo $r['longitude']; ?>,<?php echo $r['latitude']; ?>,16.36z/data=!4m5!3m4!1s0x0:0x6795fd83b04da7de!8m2!3d<?php echo $r['longitude']; ?>!4d<?php echo $r['latitude']; ?>" target="_blank" rel="noopener">google map</a>
    </td>
    <td>
      <a href="#" onclick="move_map('<?php echo $r['longitude']; ?>','<?php echo $r['latitude']; ?>','<?php echo $r['name']; ?>')">map移動</a>
    </td>
    <td>
      <label class="form-check-label"><?php echo !is_null($r['update_at']) ? date('H:i n/j', strtotime($r['update_at'])) : "" ?></label>
    </td>
  </tr>
<?php
}
?>
  </tbody>
</table>
</div>
<div>
    <div id="gmap" style="height:700px;"></div>
</div>
<script async
    src="https://maps.googleapis.com/maps/api/js?[key=]&callback=initMap">
</script>
</body>
</html>
