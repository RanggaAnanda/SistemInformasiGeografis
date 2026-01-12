<div x-data="leafletMap({
    lat: @js($latitude),
    lng: @js($longitude),
})" x-init="initMap()" class="w-full" wire:ignore>
    <div x-ref="map" class="w-full rounded-lg border shadow-sm" style="height: 450px;"></div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"/>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        function leafletMap(config) {
            return {
                map: null,
                marker: null,

                initMap() {
                    // 1️⃣ Init map
                    this.map = L.map(this.$refs.map).setView([config.lat, config.lng], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    // 2️⃣ Marker awal
                    if(config.lat && config.lng){
                        this.marker = L.marker([config.lat, config.lng], {draggable:true}).addTo(this.map);
                        this.marker.on('dragend', e => {
                            const pos = e.target.getLatLng();
                            this.updateLocation(pos.lat, pos.lng);
                        });
                    }

                    // 3️⃣ Geocoder / Search
                    if (L.Control.Geocoder) { // pastikan geocoder terload
                        L.Control.geocoder({
                            defaultMarkGeocode: false,
                            placeholder: "Cari alamat..."
                        }).on('markgeocode', e => {
                            const center = e.geocode.center;
                            this.map.setView(center, 16);
                            this.updateLocation(center.lat, center.lng);
                        }).addTo(this.map);
                    } else {
                        console.warn("Geocoder belum terload!");
                    }

                    // 4️⃣ GeoJSON (Kecamatan)
                    fetch('/geojson/bandung/kecamatan-bandung.json')
                        .then(res => res.json())
                        .then(data => {
                            L.geoJSON(data, {
                                style: { color: '#3b82f6', weight: 2, fillOpacity: 0.1 },
                                onEachFeature: (feature, layer) => {
                                    layer.bindTooltip(feature.properties.nama_kecamatan || "Kecamatan");
                                }
                            }).addTo(this.map);
                        });

                    // 5️⃣ Klik map untuk pindah marker
                    this.map.on('click', e => {
                        const { lat, lng } = e.latlng;

                        if(this.marker){
                            this.marker.setLatLng([lat, lng]);
                        } else {
                            this.marker = L.marker([lat, lng], {draggable:true}).addTo(this.map);
                            this.marker.on('dragend', ev => {
                                const pos = ev.target.getLatLng();
                                this.updateLocation(pos.lat, pos.lng);
                            });
                        }

                        this.updateLocation(lat, lng);
                    });
                },

                updateLocation(lat, lng) {
                    // Update marker jika ada
                    if(this.marker) this.marker.setLatLng([lat, lng]);

                    // Update Filament Form state
                    @this.set('data.latitude', lat);
                    @this.set('data.longitude', lng);
                }
            }
        }
    </script>
</div>
