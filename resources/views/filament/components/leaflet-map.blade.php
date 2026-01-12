<div x-data="leafletMap()" x-init="initMap()" class="w-full" wire:ignore>
    <div x-ref="map" class="w-full rounded-lg border shadow-sm" style="height: 450px;"></div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
        function leafletMap() {
            return {
                map: null,
                marker: null,
                districts: @json(\App\Models\District::pluck('id', 'nama_kecamatan')),
                geojsonLayer: null,

                initMap() {
                    const lat = @js($latitude ?? -6.9175);
                    const lng = @js($longitude ?? 107.6191);

                    // 1️⃣ Init map
                    this.map = L.map(this.$refs.map).setView([lat, lng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    // 2️⃣ Load GeoJSON kecamatan
                    fetch('/geojson/bandung/kecamatan-bandung.json')
                        .then(res => res.json())
                        .then(data => {
                            this.geojsonLayer = L.geoJSON(data, {
                                style: {
                                    color: '#3b82f6',
                                    weight: 2,
                                    fillOpacity: 0.1
                                },
                                onEachFeature: (feature, layer) => {
                                    layer.bindTooltip(feature.properties.nama_kecamatan || "Kecamatan");

                                    // Klik area polygon → set district_id
                                    layer.on('click', e => {
                                        const kecamatan = feature.properties.nama_kecamatan;
                                        if (kecamatan && this.districts[kecamatan]) {
                                            @this.set('data.district_id', this.districts[
                                            kecamatan]);
                                        }
                                        // Optional: pindahkan marker ke tengah polygon
                                        const center = layer.getBounds().getCenter();
                                        this.setMarker(center.lat, center.lng, false);
                                    });
                                }
                            }).addTo(this.map);
                        });

                    // 3️⃣ Marker awal
                    if (lat && lng) {
                        this.setMarker(lat, lng, true);
                    }

                    // 4️⃣ Geocoder / Search
                    if (typeof L.Control.Geocoder !== 'undefined') {
                        L.Control.geocoder({
                            defaultMarkGeocode: false,
                            placeholder: "Cari alamat..."
                        }).on('markgeocode', e => {
                            const center = e.geocode.center;
                            this.map.setView(center, 16);
                            this.setMarker(center.lat, center.lng, true, e.geocode.name);
                        }).addTo(this.map);
                    }

                    // 5️⃣ Klik map biasa → set marker & reverse geocode
                    this.map.on('click', e => {
                        this.setMarker(e.latlng.lat, e.latlng.lng, true);
                    });
                },

                async setMarker(lat, lng, updateAddress = false, address = null) {
                    if (this.marker) {
                        this.marker.setLatLng([lat, lng]);
                    } else {
                        this.marker = L.marker([lat, lng], {
                            draggable: true
                        }).addTo(this.map);
                        this.marker.on('dragend', e => {
                            const pos = e.target.getLatLng();
                            this.setMarker(pos.lat, pos.lng, true);
                        });
                    }

                    // Update koordinat
                    @this.set('data.latitude', lat);
                    @this.set('data.longitude', lng);

                    // Jika perlu update alamat
                    if (updateAddress) {
                        if (address) {
                            @this.set('data.alamat', address);
                        } else {
                            const {
                                address: revAddress
                            } = await this.reverseGeocode(lat, lng);
                            @this.set('data.alamat', revAddress);
                        }
                    }
                },

                async reverseGeocode(lat, lng) {
                    try {
                        const res = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`
                            );
                        const data = await res.json();
                        const address = data.display_name || "";
                        return {
                            address
                        };
                    } catch (e) {
                        console.error("Reverse geocode gagal:", e);
                        return {
                            address: ""
                        };
                    }
                }
            }
        }
    </script>
</div>
