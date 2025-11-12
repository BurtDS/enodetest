import 'leaflet';
import 'leaflet/dist/leaflet.css';

// Fix for default marker icons in Leaflet
import L from 'leaflet';
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

// Initialize drive route map
window.initDriveMap = function(mapId, startLat, startLng, endLat, endLng) {
    // Create map centered between start and end points
    const centerLat = (parseFloat(startLat) + parseFloat(endLat)) / 2;
    const centerLng = (parseFloat(startLng) + parseFloat(endLng)) / 2;

    const map = L.map(mapId).setView([centerLat, centerLng], 13);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Add start marker (green)
    const startIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #10b981; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
    L.marker([startLat, startLng], { icon: startIcon })
        .addTo(map)
        .bindPopup('<b>Start Location</b>');

    // Add end marker (red)
    const endIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #ef4444; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
    L.marker([endLat, endLng], { icon: endIcon })
        .addTo(map)
        .bindPopup('<b>End Location</b>');

    // Draw line between start and end
    const polyline = L.polyline([
        [startLat, startLng],
        [endLat, endLng]
    ], {
        color: '#3b82f6',
        weight: 3,
        opacity: 0.7
    }).addTo(map);

    // Fit map to show entire route
    map.fitBounds(polyline.getBounds(), { padding: [50, 50] });

    return map;
};

// Initialize current location map
window.initLocationMap = function(mapId, lat, lng) {
    const map = L.map(mapId).setView([lat, lng], 15);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Add marker for current location (blue)
    const locationIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #3b82f6; width: 28px; height: 28px; border-radius: 50%; border: 4px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.4);"></div>',
        iconSize: [28, 28],
        iconAnchor: [14, 14]
    });
    L.marker([lat, lng], { icon: locationIcon })
        .addTo(map)
        .bindPopup('<b>Current Location</b>')
        .openPopup();

    return map;
};
